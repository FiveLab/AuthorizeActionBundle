<?php

/*
 * This file is part of the FiveLab AuthorizeActionBundle package
 *
 * (c) FiveLab
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FiveLab\Bundle\AuthorizeActionBundle\Tests\Request\ParamConverter;

use FiveLab\Component\AuthorizeAction\Action\AuthorizeActionInterface;

/**
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class TestedAuthorizeAction implements AuthorizeActionInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var \stdClass
     */
    public $relation;
}
