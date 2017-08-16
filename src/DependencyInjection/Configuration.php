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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * The configuration for authorize action.
 *
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fivelab_authorize_action');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('user_provider')
                    ->info('The service identifier of user provider.')
                    ->defaultValue('fivelab.authorize_action.user_provider.symfony_user')
                ->end()

                ->scalarNode('property_info')
                    ->info('The service identifier of property type extractor.')
                    ->defaultValue('property_info')
                ->end()

                ->scalarNode('denormalizer')
                    ->info('The service identifier of denormalizer.')
                    ->defaultValue('serializer')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
