<?php

namespace AppVerk\MediaBundle\Form\DataTransformer;

use AppVerk\Components\Doctrine\EntityInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MediaTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var string
     */
    private $className;

    public function __construct(ObjectManager $objectManager, string $className)
    {
        $this->objectManager = $objectManager;
        $this->className = $className;
    }

    public function transform($value)
    {
        if (!$value instanceof EntityInterface) {
            return '';
        }

        return $value;
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        $entity = $this->objectManager
            ->getRepository($this->className)
            ->find($value);

        if (!$entity) {
            throw new TransformationFailedException(sprintf(
                'An entity with ID "%s" does not exist!',
                $value
            ));
        }

        return $entity;
    }
}
