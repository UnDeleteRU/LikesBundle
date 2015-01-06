<?php

namespace Undelete\LikesBundle\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Undelete\LikesBundle\Entity\Like;
use Undelete\LikesBundle\Entity\LikeableInterface;

class LikeHelper
{
    /* @var EntityManager */
    private $em;

    private $user;

    private $types;

    public function __construct(EntityManager $em, SecurityContext $context, array $types)
    {
        $this->em = $em;

        $token = $context->getToken();
        $this->user =  $token ? $token->getUser() : null;

        $this->types = $types;
    }

    protected function createCountQueryBuilder($entity)
    {
        return $this
            ->em
            ->getRepository(get_class($entity))
            ->createQueryBuilder('e')
            ->select('Count(l)');
    }

    protected function addEntityAndUserFilter(QueryBuilder $builder, LikeableInterface $entity)
    {
        $this->addEntityFilter($builder, $entity);

        $builder
            ->innerJoin('e.likes', 'l', 'WITH', 'l.user = :user')
            ->setParameter('user', $this->user);
    }

    protected function addEntityFilter(QueryBuilder $builder, LikeableInterface $entity)
    {
        $builder
            ->where('e = :entity')
            ->setParameter('entity', $entity);
    }

    public function countLikes(LikeableInterface $entity)
    {
        $qb = $this->createCountQueryBuilder($entity);
        $qb->innerJoin('e.likes', 'l');
        $this->addEntityFilter($qb, $entity);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function hasUserLike(LikeableInterface $entity)
    {
        if (!$this->user) {
            return false;
        }

        $qb = $this->createCountQueryBuilder($entity);
        $this->addEntityAndUserFilter($qb, $entity);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count >= 1;
    }

    public function getUserLike(LikeableInterface $entity)
    {
        if (!$this->user) {
            return null;
        }

        $qb = $this->em->getRepository(get_class($entity))->createQueryBuilder('e');
        $qb->select('e.id, l.id as lid');
        $this->addEntityAndUserFilter($qb, $entity);

        $like = $qb->getQuery()->getOneOrNullResult();

        return $like ? $this->em->getRepository('UndeleteLikesBundle:Like')->find($like['lid']) : null;
    }

    public function checkUser()
    {
        if (!$this->user || !($this->user instanceof UserInterface)) {
            throw new AccessDeniedHttpException;
        }
    }

    public function getType(LikeableInterface $entity)
    {
        return array_search(get_class($entity), $this->types);
    }

    public function getClassByType($type)
    {
        return isset($this->types[$type]) ? $this->types[$type] : '';
    }

    public function createLike()
    {
        $like = new Like();
        $like->setUser($this->user);

        return $like;
    }
}
