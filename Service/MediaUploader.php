<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaUploader
{
    protected const GOOGLE_API_URL = 'https://storage.googleapis.com';

    protected MediaValidation $mediaValidation;
    protected TranslatorInterface $translator;
    protected Storage $storage;

    /**
     * MediaUploader constructor.
     *
     * @param MediaValidation     $mediaValidation
     * @param TranslatorInterface $translator
     * @param Storage             $storage
     */
    public function __construct(
        MediaValidation $mediaValidation,
        TranslatorInterface $translator,
        Storage $storage
    ) {
        $this->mediaValidation = $mediaValidation;
        $this->translator = $translator;
        $this->storage = $storage;
    }

    /**
     * @param UploadedFile $file
     * @param null|string  $groupName
     *
     * @return array
     */
    public function upload(UploadedFile $file, ?string $groupName = null, ?string $filename = null, bool $nativeName = false): array
    {
        $this->validate($file, $groupName);
        $this->validateSize($file, $groupName);
        $dir =  $this->getDir($groupName);

        if(is_null($filename) && !$nativeName){
            $filename = md5(uniqid()).'.'.$file->guessExtension();
        }

        if($nativeName && is_null($filename)) {
            $filename = $file->getClientOriginalName();
        }
        $fileObject = fopen($file->getRealPath(), 'r');
        $fileMime = !empty($file->getClientMimeType()) ? $file->getClientMimeType() : $file->getMimeType();

        $object = $this->storage->bucket()
            ->upload(
                $fileObject,
                [
                    'name' => sprintf("%s%s",$dir,preg_replace('/\s/', '', $filename)),
                    'metadata' => ['contentType' => $fileMime],
                    'predefinedAcl' => 'publicRead',
                ]
            );

        $fileData = $object->info();

        return [$this->getUrl($fileData), $fileData['size']];
    }

    /**
     * @param UploadedFile $file
     * @param null|string  $groupName
     *
     * @return array
     */
    public function uploadWithName(UploadedFile $file, ?string $groupName = null): array
    {
        $this->validate($file, $groupName);
        $this->validateSize($file, $groupName);
        $dir =  $this->getDir($groupName);

        $fileObject = fopen($file->getRealPath(), 'r');
        $fileMime = !empty($file->getClientMimeType()) ? $file->getClientMimeType() : $file->getMimeType();

        $object = $this->storage->bucket()
            ->upload(
                $fileObject,
                [
                    'name' => sprintf('%s%s',$dir, preg_replace('/\s/', '',  $file->getClientOriginalName())),
                    'metadata' => ['contentType' => $fileMime, 'filename' => $file->getClientOriginalName() ],
                    'predefinedAcl' => 'publicRead',
                ]
            );

        $fileData = $object->info();

        return [$this->getUrl($fileData), $fileData['size']];
    }

    /**
     * @param UploadedFile $file
     * @param string|null  $groupName
     */
    protected function validate(UploadedFile $file, ?string $groupName = null): void
    {
        $allowedMimeTypes = $this->mediaValidation->getAllowedMimeTypes($groupName);
        if (!empty($allowedMimeTypes) && !in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new BadRequestHttpException(
                $this->translator->trans('media.validation.image_type', ['%type%' => $file->getMimeType()])
            );
        }

        $maxSize = $this->mediaValidation->getMaxSize($groupName);
        if ($maxSize) {
            if (!($fileSize = $file->getClientSize())) {
                throw new NotFoundHttpException();
            }

            if ($fileSize > $maxSize) {
                throw new BadRequestHttpException(
                    $this->translator->trans('media.validation.image_size', ['%max_size%' => $maxSize])
                );
            }
        }
    }

    /**
     * @param UploadedFile $file
     * @param string|null  $groupName
     */
    protected function validateSize(UploadedFile $file, ?string $groupName = null): void
    {
        $sizes = $this->mediaValidation->getGroupSizes($groupName);
        if (empty($sizes)) {
            return;
        }

        list($imageWidth, $imageHeight) = getimagesize($file->getPathname());

        $minWidth = $sizes['min_width'];
        $maxWidth = $sizes['max_width'];
        $minHeight = $sizes['min_height'];
        $maxHeight = $sizes['max_height'];

        if ($imageWidth < $minWidth || $imageWidth > $maxWidth || $imageHeight < $minHeight || $imageHeight > $maxHeight) {
            throw new BadRequestHttpException(
                $this->translator->trans(
                    'media.validation.image_dimension',
                    [
                        '%max_width%' => $maxWidth,
                        '%min_width%' => $minWidth,
                        '%max_height%' => $maxHeight,
                        '%min_height%' => $minHeight,
                    ]
                )
            );
        }
    }

    protected function getDir(?string $groupName = null): string
    {
        $dir = '';
        if(!is_null($groupName)){
            $dir = $this->mediaValidation->getDir($groupName);
        }

        return $dir;
    }

    protected function getUrl(array $info): string
    {
        return implode('/', [self::GOOGLE_API_URL, $info['bucket'], $info['name']]);
    }

    public function getUrlFromData(string $name, string $group): string
    {
        $fileName = $this->getFileNameWithContextDir($name, $group);
        return implode('/', [self::GOOGLE_API_URL, $this->storage->getBucketId(), $fileName]);
    }

    public function moveFile(string $oldFileName, string $newFileName): ?string{

        if(!$this->storage->bucket()->object($oldFileName)->exists()) {
            return null;
        }

        $oldObject = $this->storage->bucket()->object($oldFileName);
        $newObject = $oldObject->copy($this->storage->bucket(), ['name' => $newFileName]);
        $fileData = $newObject->info();
        $oldObject->delete();

        return $this->getUrl($fileData);
    }

    public function getFileNameWithContextDir(string $fileName, string $groupName): string
    {
        $dir = $this->getDir($groupName);

        return sprintf('%s%s', $dir, $fileName);
    }

    public function deleteFile(string $fileName): void
    {
        $object = $this->storage->bucket()->object($fileName);
        $object->delete();
    }

    public function getFileName(string $fileUrl): string
    {
        $fileUlrArray = explode('/', urldecode(parse_url($fileUrl, PHP_URL_PATH)));
        $fileUlrArray = array_slice($fileUlrArray, 2);
        if(count($fileUlrArray) === 7){

            $fileUlrArray = array_slice($fileUlrArray, 5);
        }
        return implode('/',$fileUlrArray);
    }
}
