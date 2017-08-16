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

use FiveLab\Bundle\AuthorizeActionBundle\DependencyInjection\Compiler\AddActionVerifierPass;
use FiveLab\Component\AuthorizeAction\Verifier\AuthorizeActionVerifierChain;
use FiveLab\Component\AuthorizeAction\Verifier\AuthorizeActionVerifierInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class AddActionVerifierPassTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var Definition
     */
    private $chainDefinition;

    /**
     * @var AddActionVerifierPass
     */
    private $compiler;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->chainDefinition = new Definition(AuthorizeActionVerifierChain::class);
        $this->container->setDefinition('fivelab.authorize_action.verifier', $this->chainDefinition);

        $this->compiler = new AddActionVerifierPass();
    }

    /**
     * @test
     */
    public function shouldSuccessCompile(): void
    {
        $verifier = $this->createMock(AuthorizeActionVerifierInterface::class);
        $verifierClass = get_class($verifier);

        $this->container->getParameterBag()->add([
            'verifier.class' => $verifierClass,
        ]);

        $verifierDefinition = (new Definition('%verifier.class%'))
            ->addTag('fivelab.authorize_action.verifier');

        $this->container->setDefinition('verifier.custom', $verifierDefinition);

        $this->compiler->process($this->container);
        $calls = $this->chainDefinition->getMethodCalls();

        self::assertEquals([
            [
                'add',
                [
                    new Reference('verifier.custom'),
                ],
            ],
        ], $calls);
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot compile authorize action verifier with service id "verifier.custom".
     */
    public function shouldFailIfVerifierNotImplementInterface(): void
    {
        $verifierDefinition = (new Definition(\stdClass::class))
            ->addTag('fivelab.authorize_action.verifier');

        $this->container->setDefinition('verifier.custom', $verifierDefinition);

        $this->compiler->process($this->container);
    }
}
