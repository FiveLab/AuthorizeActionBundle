<?php

/*
 * This file is part of the FiveLab AuthorizeActionBundle package
 *
 * (c) FiveLab
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FiveLab\Bundle\AuthorizeActionBundle\Tests\DependencyInjection;

use FiveLab\Bundle\AuthorizeActionBundle\DependencyInjection\AuthorizeActionExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class AuthorizeActionExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessLoadWithDefaults(): void
    {
        $container = new ContainerBuilder();
        $extension = new AuthorizeActionExtension();

        $extension->load([], $container);

        $aliases = $container->getAliases();

        self::assertArrayHasKey('fivelab.authorize_action.user_provider', $aliases);
        self::assertArrayHasKey('fivelab.authorize_action.denormalizer', $aliases);
        self::assertArrayHasKey('fivelab.authorize_action.property_info', $aliases);

        self::assertEquals(new Alias('fivelab.authorize_action.user_provider.symfony_user'), $aliases['fivelab.authorize_action.user_provider']);
        self::assertEquals(new Alias('serializer'), $aliases['fivelab.authorize_action.denormalizer']);
        self::assertEquals(new Alias('property_info'), $aliases['fivelab.authorize_action.property_info']);
    }

    /**
     * @test
     */
    public function shouldSuccessGetAlias()
    {
        $extension = new AuthorizeActionExtension();

        self::assertEquals('fivelab_authorize_action', $extension->getAlias());
    }
}
