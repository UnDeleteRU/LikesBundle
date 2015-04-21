<?php

namespace Undelete\LikesBundle\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Undelete\LikesBundle\Entity\Like;
use Undelete\LikesBundle\Entity\LikeableInterface;
use Undelete\LikesBundle\Exception\NoLikeAssociationException;

class LikeHelper
{
    /* @var EntityManager */
    private $em;

    private $types;

    private $context;

    public function __construct(EntityManager $em, SecurityContext $context, array $types)
    {
        $this->em = $em;
        $this->context = $context;
        $this->types = $types;
    }

    protected function getUser()
    {
        $token = $this->context->getToken();

        return $token ? $token->getUser() : null;
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
            ->setParameter('user', $this->getUser());
    }

    protected function addEntityFilter(QueryBuilder $builder, LikeableInterface $entity)
    {
        $builder
            ->where('e = :entity')
            ->setParameter('entity', $entity);
    }

    public function countLikes(LikeableInterface $entity)
    {
        $this->checkAssociation($entity);

        $qb = $this->createCountQueryBuilder($entity);
        $qb->innerJoin('e.likes', 'l');
        $this->addEntityFilter($qb, $entity);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function hasUserLike(LikeableInterface $entity)
    {
        if (!$this->getUser()) {
            return false;
        }

        $this->checkAssociation($entity);

        $qb = $this->createCountQueryBuilder($entity);
        $this->addEntityAndUserFilter($qb, $entity);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count >= 1;
    }

    public function getUserLike(LikeableInterface $entity)
    {
        if (!$this->getUser()) {
            return null;
        }

        $this->checkAssociation($entity);

        $qb = $this->em->getRepository(get_class($entity))->createQueryBuilder('e');
        $qb->select('e.id, l.id as lid');
        $this->addEntityAndUserFilter($qb, $entity);

        $like = $qb->getQuery()->getOneOrNullResult();

        return $like ? $this->em->getRepository('UndeleteLikesBundle:Like')->find($like['lid']) : null;
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
        $like->setUser($this->getUser());

        return $like;
    }

    protected function checkAssociation(LikeableInterface $entity)
    {
        $metadata = $this->em->getClassMetadata(get_class($entity));
        $mapping = false;

        if ($metadata->hasAssociation('likes')) {
            $mapping = $metadata->getAssociationMapping('likes');
        }

        if (!$mapping || ($mapping['targetEntity'] != 'Undelete\LikesBundle\Entity\Like')) {
            throw new NoLikeAssociationException(
                sprintf('Association with like entity not found in entity %s', get_class($entity))
            );
        }
    }
}
