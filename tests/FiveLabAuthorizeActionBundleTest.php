<?php

/*
 * This file is part of the FiveLab AuthorizeActionBundle package
 *
 * (c) FiveLab
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FiveLab\Bundle\AuthorizeActionBundle\Tests;

use FiveLab\Bundle\AuthorizeActionBundle\DependencyInjection\AuthorizeActionExtension;
use FiveLab\Bundle\AuthorizeActionBundle\DependencyInjection\Compiler\AddActionVerifierPass;
use FiveLab\Bundle\AuthorizeActionBundle\FiveLabAuthorizeActionBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class FiveLabAuthorizeActionBundleTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessGetExtension(): void
    {
        $bundle = new FiveLabAuthorizeActionBundle();
        $extension = $bundle->getContainerExtension();

        self::assertEquals(new AuthorizeActionExtension(), $extension);
    }

    /**
     * @test
     */
    public function shouldSuccessBuild(): void
    {
        /** @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject $container */
        $container = $this->createMock(ContainerBuilder::class);

        $container->expects(self::once())
            ->method('addCompilerPass')
            ->with(new AddActionVerifierPass());

        $bundle = new FiveLabAuthorizeActionBundle();
        $bundle->build($container);
    }
}
