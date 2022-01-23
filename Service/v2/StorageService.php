<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Service\v2;

use AppVerk\GoogleCloudStorageMediaBundle\Event\FileWasAdded;
use AppVerk\GoogleCloudStorageMediaBundle\Event\FileWasRemoved;
use AppVerk\GoogleCloudStorageMediaBundle\Model\UploadFile;
use AppVerk\GoogleCloudStorageMediaBundle\Exception\FilesystemException as AppFileSystemException;
use AppVerk\GoogleCloudStorageMediaBundle\Exception\InvalidMimetypeException;
use AppVerk\GoogleCloudStorageMediaBundle\Exception\InvalidSizeException;
use AppVerk\GoogleCloudStorageMediaBundle\Flysystem\Retriever\UrlRetrieverInterface;
use AppVerk\GoogleCloudStorageMediaBundle\Namer\NamerInterface;
use AppVerk\GoogleCloudStorageMediaBundle\Service\MediaValidation;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

class StorageService
{
    private FilesystemOperator $filesystem;

    private NamerInterface $namer;

    private TranslatorInterface $translator;

    private MediaValidation $mediaValidation;

    private UrlRetrieverInterface $urlRetriever;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        MediaValidation $mediaValidation,
        NamerInterface $namer,
        FilesystemOperator $filesystem,
        TranslatorInterface $translator,
        UrlRetrieverInterface $urlRetriever,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->mediaValidation = $mediaValidation;
        $this->namer = $namer;
        $this->filesystem = $filesystem;
        $this->translator = $translator;
        $this->urlRetriever = $urlRetriever;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(UploadedFile $file, ?string $originalFilename = '', ?string $groupName = null): UploadFile
    {
        $this->validate($file, $groupName);
        $this->validateSize($file, $groupName);

        $url = $filename = $this->namer->generate(
            $originalFilename ?: $file->getFilename(),
            $file->getExtension() ?: $file->guessExtension()
        );

        $splitName = explode('/', $filename);
        $friendlyName = end($splitName);

        try {
            $this->filesystem->write($filename, $file->getContent());
            $url = $this->urlRetriever->getUrl($filename);
        } catch (FilesystemException $e) {
            throw new AppFileSystemException($e->getMessage());
        }

        $model = new UploadFile(
            $friendlyName,
            $filename,
            $url,
            $file->getMimeType(),
            $file->getSize()
        );
        $this->eventDispatcher->dispatch(new FileWasAdded($model));

        return $model;
    }

    public function sanitize(string $filename): string
    {
        return preg_replace(['/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'], ['_', '.', ''], $filename);
    }

    /**
     * @return resource
     */
    public function stream(string $filename)
    {
        return $this->filesystem->readStream($filename);
    }

    public function read(string $filename): string
    {
        return $this->filesystem->read($filename);
    }

    public function remove(string $filename): bool
    {
        try {
            if (true === $this->filesystem->fileExists($filename)) {
                $this->filesystem->delete($filename);
                $this->eventDispatcher->dispatch(new FileWasRemoved($filename));

                return true;
            }
        } catch (FilesystemException $e) {
            throw new AppFileSystemException($e->getMessage());
        }

        return false;
    }

    /**
     * @param UploadedFile $file
     * @param string|null  $groupName
     */
    private function validate(UploadedFile $file, ?string $groupName = null): void
    {
        $allowedMimeTypes = $this->mediaValidation->getAllowedMimeTypes($groupName);
        if (!empty($allowedMimeTypes) && !in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new InvalidMimetypeException(
                $this->translator->trans('media.validation.image_type', ['%type%' => $file->getMimeType()])
            );
        }

        $maxSize = $this->mediaValidation->getMaxSize($groupName);
        if ($maxSize) {
            if (!($fileSize = $file->getSize())) {
                throw new InvalidSizeException('media.validation.file_not_found', null, 404);
            }

            if ($fileSize > $maxSize) {
                throw new InvalidSizeException(
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
            throw new InvalidSizeException(
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
