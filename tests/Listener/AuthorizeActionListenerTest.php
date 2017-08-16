<?php

/*
 * This file is part of the FiveLab AuthorizeActionBundle package
 *
 * (c) FiveLab
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FiveLab\Bundle\AuthorizeActionBundle\Tests\Listener;

use FiveLab\Bundle\AuthorizeActionBundle\Listener\AuthorizeActionListener;
use FiveLab\Component\AuthorizeAction\Action\AuthorizeActionInterface;
use FiveLab\Component\AuthorizeAction\AuthorizationCheckerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class AuthorizeActionListenerTest extends TestCase
{
    /**
     * @var AuthorizationCheckerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $authorizationChecker;

    /**
     * @var AuthorizeActionListener
     */
    private $listener;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->listener = new AuthorizeActionListener($this->authorizationChecker);
    }

    /**
     * @test
     */
    public function shouldSuccessListen(): void
    {
        $argument1 = new \stdClass();
        $argument2 = $this->createMock(AuthorizeActionInterface::class);
        $argument3 = new \stdClass();

        $arguments = [$argument1, $argument2, $argument3];

        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new FilterControllerArgumentsEvent($kernel, function () {
        }, $arguments, new Request(), HttpKernelInterface::MASTER_REQUEST);

        $this->authorizationChecker->expects(self::once())
            ->method('verify')
            ->with($argument2);

        $this->listener->authorizeActionsOnControllerArguments($event);
    }
}
