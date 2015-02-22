<?php

namespace Undelete\LikesBundle\Twig;

use Undelete\LikesBundle\Entity\LikeableInterface;
use Undelete\LikesBundle\Helper\LikeHelper;

class LikeExtension extends \Twig_Extension
{
    /* @var LikeHelper */
    private $helper;

    private $template;

    public function __construct(LikeHelper $helper, $template)
    {
        $this->helper = $helper;
        $this->template = $template;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'Likes',
                [$this, 'renderLikeTemplate'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            )
        ];
    }

    public function renderLikeTemplate(\Twig_Environment $twig, LikeableInterface $entity)
    {
        return $twig->render($this->template, [
            'count' => $this->helper->countLikes($entity),
            'type' => $this->helper->getType($entity),
            'id' => $entity->getId(),
            'active' => $this->helper->hasUserLike($entity)
        ]);
    }

    public function getName()
    {
        return 'like_extension';
    }
}
