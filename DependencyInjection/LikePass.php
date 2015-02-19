<?php

namespace Undelete\LikesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LikePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(
            'undelete.likes.event.dispatcher'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'like_listener'
        );

        foreach ($taggedServices as $id => $tags) {
            $onLike = isset($tags[0]['onLike']) ? $tags[0]['onLike'] : false;
            $onLikeRemove = isset($tags[0]['onLikeRemove']) ? $tags[0]['onLike'] : false;

            $definition->addMethodCall(
                'addListener',
                array(new Reference($id), $onLike, $onLikeRemove)
            );
        }
    }
}
