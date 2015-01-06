<?php

namespace Undelete\LikesBundle\Entity;

interface LikeableInterface
{
    public function getId();

    public function addLike(Like $like);

    public function removeLike(Like $like);

    public function getLikes();
}
