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

namespace FiveLab\Bundle\AuthorizeActionBundle\DependencyInjection\Compiler;

use FiveLab\Component\AuthorizeAction\Verifier\AuthorizeActionVerifierInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass for add action verifiers.
 *
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class AddActionVerifierPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function process(ContainerBuilder $container): void
    {
        $chainVerifier = $container->findDefinition('fivelab.authorize_action.verifier');
        $verifiers = $container->findTaggedServiceIds('fivelab.authorize_action.verifier');

        foreach ($verifiers as $serviceId => $tags) {
            try {
                $definition = $container->getDefinition($serviceId);
                $class = $container->getParameterBag()->resolveValue($definition->getClass());

                if (!is_a($class, AuthorizeActionVerifierInterface::class, true)) {
                    throw new \RuntimeException(sprintf(
                        'The verifier should implement "%s".',
                        AuthorizeActionVerifierInterface::class
                    ));
                }

                $chainVerifier->addMethodCall('add', [
                    new Reference($serviceId),
                ]);
            } catch (\Exception $e) {
                throw new \RuntimeException(sprintf(
                    'Cannot compile authorize action verifier with service id "%s".',
                    $serviceId
                ), 0, $e);
            }
        }
    }
}
