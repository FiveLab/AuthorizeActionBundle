<?php

declare(strict_types = 1);

/*
 * This file is part of the FiveLab AuthorizeActionBundle package
 *
 * (c) FiveLab
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FiveLab\Bundle\AuthorizeActionBundle;

use FiveLab\Bundle\AuthorizeActionBundle\DependencyInjection\AuthorizeActionExtension;
use FiveLab\Bundle\AuthorizeActionBundle\DependencyInjection\Compiler\AddActionVerifierPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The bundle for integrate authorize action with you Symfony application
 *
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class FiveLabAuthorizeActionBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddActionVerifierPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): AuthorizeActionExtension
    {
        if (!$this->extension) {
            $this->extension = new AuthorizeActionExtension();
        }

        return $this->extension;
    }
}
