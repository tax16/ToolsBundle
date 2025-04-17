<?php

namespace Tax16\ToolsBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tax16\ToolsBundle\Infrastructure\Retry\CompilerPass\RetryCompilerPass;

class ToolsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container
            ->addCompilerPass(new RetryCompilerPass());
    }
}
