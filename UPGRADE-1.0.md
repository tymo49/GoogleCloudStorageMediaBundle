Upgrade 0.x -> 1.x guide
---

Starting from version 1.x this bundle uses [Flysystem](https://flysystem.thephpleague.com/v2/docs) 
as filesystem abstraction underneath. In order to use this bundle please install and configure bundle accordingly.
This bundle requires at least one filesystem to be created and configured.

This bundle also support various namers which are registered as services by default.
In order to use them set `google_cloud_storage_media.namer` to one of the values or create a separate `StorageService`
that extends `AppVerk\GoogleCloudStorageMediaBundle\Service\v2\StorageService` with other namer injected.
