<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\Controller;

use AppVerk\GoogleCloudStorageMediaBundle\Doctrine\MediaManager;
use AppVerk\GoogleCloudStorageMediaBundle\Service\MediaUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/media")
 */
class MediaController extends AbstractController
{
    /**
     * @Route("/upload/{group}", name="upload_media", methods={"POST"})
     *
     * @param Request       $request
     * @param MediaUploader $mediaUploader
     * @param MediaManager  $mediaManager
     * @param null|string   $group
     *
     * @return JsonResponse|Response
     */
    public function uploadAction(Request $request, MediaUploader $mediaUploader, MediaManager $mediaManager, ?string $group = null)
    {
        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            try {
                list($fileName, $size) = $mediaUploader->upload($file, $group);
                $media = $mediaManager->createMedia($file, $fileName, $size);

                $output['fileName'] = $media->getFileName();
                $output['id'] = $media->getId();

                return new JsonResponse($output);
            } catch (\Exception $e) {
                return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }

        return new Response('', Response::HTTP_BAD_REQUEST);
    }
}
