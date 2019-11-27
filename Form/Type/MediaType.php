<?php

namespace AppVerk\MediaBundle\Form\Type;

use AppVerk\MediaBundle\Form\DataTransformer\MediaTransformer;
use AppVerk\MediaBundle\Service\MediaValidation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{
    /**
     * @var MediaTransformer
     */
    private $mediaTransformer;

    /**
     * @var MediaValidation
     */
    private $mediaValidation;


    /**
     * MediaType constructor.
     *
     * @param MediaTransformer $mediaTransformer
     * @param MediaValidation  $mediaValidation
     */
    public function __construct(MediaTransformer $mediaTransformer, MediaValidation $mediaValidation)
    {
        $this->mediaTransformer = $mediaTransformer;
        $this->mediaValidation = $mediaValidation;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->mediaTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['group'] = $options['group'];
        $view->vars['allowed_mime_types'] = $this->mediaValidation->getAllowedMimeTypes($options['group']);
        $view->vars['max_size'] = $this->mediaValidation->getMaxSize($options['group']);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('group', null);
    }
}
