<?php

namespace Undelete\LikesBundle\Twig;

use Undelete\LikesBundle\Entity\LikeableInterface;
use Undelete\LikesBundle\Helper\LikeHelper;

class LikeExtension extends \Twig_Extension
{
    /* @var LikeHelper */
    private $helper;

    public function __construct(LikeHelper $helper)
    {
        $this->helper = $helper;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'Likes',
                function (\Twig_Environment $twig, LikeableInterface $entity) {
                    return $twig->render('UndeleteLikesBundle::like.html.twig', [
                        'count' => $this->helper->countLikes($entity),
                        'type' => $this->helper->getType($entity),
                        'id' => $entity->getId(),
                        'active' => $this->helper->hasUserLike($entity)
                    ]);
                },
                [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            )
        ];
    }


    public function getName()
    {
        return 'like_extension';
    }
}