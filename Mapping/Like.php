<?php

namespace Undelete\LikesBundle\Mapping;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class Like
{
    private $userClass;

    public function __construct($userClass)
    {
        $this->userClass = $userClass;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /* @var $metadata ClassMetadataInfo */
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->getName() == 'Undelete\LikesBundle\Entity\Like') {
            $metadata->mapManyToOne([
                'targetEntity' => $this->userClass,
                'fieldName' => 'user',
            ]);
        }
    }
}
