<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaUploader
{
    /**
     * @var MediaValidation
     */
    private $mediaValidation;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /** @var Storage */
    private $storage;

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
    public function upload(UploadedFile $file, ?string $groupName = null): array
    {
        $this->validate($file, $groupName);
        $this->validateSize($file, $groupName);

        $object = $this->storage->bucket()
            ->upload(
                $file,
                [
                    'metadata' => ['contentType' => $file->getMimeType()],
                    'predefinedAcl' => 'publicRead',
                ]
            );

        $fileData = $object->info();

        return [$fileData['mediaLink'], $fileData['size']];
    }

    /**
     * @param UploadedFile $file
     * @param string|null  $groupName
     */
    private function validate(UploadedFile $file, ?string $groupName = null): void
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
    private function validateSize(UploadedFile $file, ?string $groupName = null): void
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
}
