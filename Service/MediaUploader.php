<?php

namespace AppVerk\MediaBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaUploader
{
    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var MediaValidation
     */
    private $mediaValidation;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * MediaUploader constructor.
     *
     * @param string $targetDirectory
     * @param MediaValidation $mediaValidation
     */
    public function __construct(
        string $targetDirectory,
        MediaValidation $mediaValidation,
        TranslatorInterface $translator
    ) {
        $this->targetDirectory = $targetDirectory;
        $this->mediaValidation = $mediaValidation;
        $this->translator = $translator;
    }

    /**
     * @param UploadedFile $file
     * @param null|string $groupName
     *
     * @return string
     */
    public function upload(UploadedFile $file, ?string $groupName = null)
    {
        $this->validate($file, $groupName);
        $this->validateSize($file, $groupName);

        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->targetDirectory, $fileName);

        return [$fileName, $this->targetDirectory];
    }

    /**
     * @param UploadedFile $file
     * @param null|string $groupName
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

    private function validateSize(UploadedFile $file, ?string $groupName = null)
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
                        '%max_width%'  => $maxWidth,
                        '%min_width%'  => $minWidth,
                        '%max_height%' => $maxHeight,
                        '%min_height%' => $minHeight,
                    ]
                )
            );
        }
    }
}
