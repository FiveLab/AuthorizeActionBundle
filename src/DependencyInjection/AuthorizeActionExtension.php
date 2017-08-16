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

namespace FiveLab\Bundle\AuthorizeActionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * The extension for extend authorize action with Symfony application.
 *
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class AuthorizeActionExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'fivelab_authorize_action';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setAlias('fivelab.authorize_action.user_provider', $mergedConfig['user_provider']);
        $container->setAlias('fivelab.authorize_action.denormalizer', $mergedConfig['denormalizer']);
        $container->setAlias('fivelab.authorize_action.property_info', $mergedConfig['property_info']);
    }
}
