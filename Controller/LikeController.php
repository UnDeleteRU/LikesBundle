<?php

namespace Undelete\LikesBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Undelete\LikesBundle\Entity\Like;
use Undelete\LikesBundle\Entity\LikeableInterface;
use Undelete\LikesBundle\Helper\LikeHelper;

class LikeController
{
    /* @var LikeHelper */
    protected $likeHelper;

    /* @var EntityManager */
    protected $em;

    public function __construct(EntityManager $em, LikeHelper $likeHelper)
    {
        $this->likeHelper = $likeHelper;
        $this->em = $em;
    }

    public function likeAction($type, $id)
    {
        $this->likeHelper->checkUser();
        $class = $this->likeHelper->getClassByType($type);

        if (!$class) {
            throw new NotFoundHttpException;
        }

        $entity = $this->em->getRepository($class)->find($id);

        if (!$entity) {
            throw new NotFoundHttpException;
        }

        if (!$entity instanceof LikeableInterface) {
            throw new \Exception('Could not like Entity not implements Likeable interface');
        }

        $active = false;

        if ($like = $this->likeHelper->getUserLike($entity)) {
            $this->em->remove($like);
            $this->em->flush();
        } else {
            $like = $this->likeHelper->createLike();
            $this->em->persist($like);

            $entity->addLike($like);
            $this->em->flush();

            $active = true;
        }

        $count = $this->likeHelper->countLikes($entity);

        return new JsonResponse(['count' => $count, 'active' => $active]);
    }
}
