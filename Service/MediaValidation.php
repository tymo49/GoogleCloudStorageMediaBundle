<?php

namespace AppVerk\MediaBundle\Service;

class MediaValidation
{
    /**
     * @var int|null
     */
    private $maxSize;
    /**
     * @var array
     */
    private $allowedMimeTypes;
    /**
     * @var array
     */
    private $groups;


    /**
     * MediaValidation constructor.
     *
     * @param int|null $maxSize
     * @param array    $allowedMimeTypes
     * @param array    $groups
     */
    public function __construct(?int $maxSize = null, array $allowedMimeTypes = [], array $groups = [])
    {
        $this->maxSize = $maxSize;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->groups = $groups;
    }

    /**
     * @param string $groupName
     *
     * @return array|null
     */
    private function getGroup(string $groupName): ?array
    {
        if (array_key_exists($groupName, $this->groups)) {
            return $this->groups[$groupName];
        }

        return null;
    }

    /**
     * @param null|string $groupName
     *
     * @return array
     */
    public function getAllowedMimeTypes(?string $groupName = null): array
    {
        if (null !== $groupName) {
            if ($group = $this->getGroup($groupName)) {
                return $group['allowed_mime_types'];
            }
        }

        return $this->allowedMimeTypes;
    }

    /**
     * @param null|string $groupName
     *
     * @return int|null
     */
    public function getMaxSize(?string $groupName = null): ?int
    {
        if (null !== $groupName) {
            if ($group = $this->getGroup($groupName)) {
                return $group['max_file_size'];
            }
        }

        return $this->maxSize;
    }

    public function getGroupSizes(?string $groupName = null): array
    {
        $group = $this->getGroup($groupName);
        if (!$group) {
            return [];
        }
        if (!isset($group['sizes'])) {
            return [];
        }

        return $group['sizes'];
    }
}
