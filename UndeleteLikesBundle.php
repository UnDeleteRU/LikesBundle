<?php

namespace Undelete\LikesBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Undelete\LikesBundle\DependencyInjection\LikePass;

class UndeleteLikesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LikePass());
    }
}
