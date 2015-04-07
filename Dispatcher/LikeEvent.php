<?php

namespace Undelete\LikesBundle\Dispatcher;

use Symfony\Component\EventDispatcher\Event;
use Undelete\LikesBundle\Entity\Like;
use Undelete\LikesBundle\Entity\LikeableInterface;

class LikeEvent extends Event
{
    const ON_LIKE = 'onLike';
    const ON_LIKE_REMOVE = 'onLikeRemove';

    private $like;

    private $entity;

    public function __construct(Like $like, LikeableInterface $entity)
    {
        $this->like = $like;
        $this->entity = $entity;
    }

    public function getLike()
    {
        return $this->like;
    }

    public function getEntity()
    {
        return $this->entity;
    }
}
