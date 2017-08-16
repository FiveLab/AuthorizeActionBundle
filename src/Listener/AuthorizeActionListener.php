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

namespace FiveLab\Bundle\AuthorizeActionBundle\Listener;

use FiveLab\Component\AuthorizeAction\Action\AuthorizeActionInterface;
use FiveLab\Component\AuthorizeAction\AuthorizationCheckerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Listen for verify the action before call controller.
 *
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class AuthorizeActionListener
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Call to this method on "kernel.controller_arguments" event for check right to execute actions.
     *
     * @param FilterControllerArgumentsEvent $event
     *
     * @throws AccessDeniedException
     */
    public function authorizeActionsOnControllerArguments(FilterControllerArgumentsEvent $event): void
    {
        foreach ($event->getArguments() as $argument) {
            if ($argument instanceof AuthorizeActionInterface) {
                $this->authorizationChecker->verify($argument);
            }
        }
    }
}
