# GoogleCloudStorageMediaBundle

Symfony Media Bundle. The bundle allow in easy way upload files. The bundle required to working [dropzone.js](http://www.dropzonejs.com/) script.

## Configure

Require the bundle with composer:

    $ composer require app-verk/google-cloud-storage-media-bundle

Enable the bundle in the kernel:

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new AppVerk\GoogleCloudStorageMediaBundle\GoogleCloudStorageMediaBundle(),
            // ...
        );
    }

Create your Media class:
    
    <?php
    
    namespace AppBundle\Entity;
    
    use AppVerk\GoogleCloudStorageMediaBundle\Entity\Media as BaseMedia;
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * @ORM\Entity()
     */
    class Media extends BaseMedia
    {
    
    }
    
Add to config.yml:

    twig:
        form:
            resources:
                - 'GoogleCloudStorageMediaBundle:form:fields.html.twig'
                
    media:
        entities:
            media_class: AppBundle\Entity\Media
        gcs:
            project_id: 123
            bucket_id: my_bucket
        allowed_mime_types: ["image/jpeg", "image/jpg", "image/png", "image/gif", "application/pdf"]
        
Add to routing.yml:

    media:
        resource: '@GoogleCloudStorageMediaBundle/Controller/'
        type: annotation
                
Add these libs into your admin panel:

    <!--css -->
    <link rel="stylesheet" href="{{ asset('bundles/media/css/dropzone.min.css') }}" />
    
    <!-- js -->
    <script src="{{ asset('bundles/media/js/dropzone.min.js') }}"></script>

Update your database schema:

    $ php app/console doctrine:schema:update --force
    
## Media Form Type

    <?php
    
    use Symfony\Component\Form\AbstractType;
    use AppVerk\GoogleCloudStorageMediaBundle\Form\Type\MediaType;
    use Symfony\Component\Form\FormBuilderInterface;
    
    class Post extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $formMapper
                ->add('image', MediaType::class)
            ;
        }
    }
    
## Twig helper

Render a media:

    <img src="{{ post.media|media }}" />

## Group of validation

Bundle allow to validation every single used MediaType in different way. For example you want to allow only PDF files: 

You need to add group into config.yml:

    media:
        entities:
            media_class: AppBundle\Entity\Media
        allowed_mime_types: ["image/png", "image/gif"]
        max_file_size: 15000000
        groups:
            lorem:
                allowed_mime_types: ["application/pdf"]
                max_file_size: 560000

Set group in MediaType:
    
    $formMapper->add('image', MediaType::class, [
        'group' => 'lorem'
    ]);

## License

The bundle is released under the [MIT License](LICENSE).
