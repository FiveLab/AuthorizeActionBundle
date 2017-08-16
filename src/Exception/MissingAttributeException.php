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

namespace FiveLab\Bundle\AuthorizeActionBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Throw this exception if the request parameter try getting the attribute from
 * request but the attribute not found.
 *
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class MissingAttributeException extends BadRequestHttpException
{
}
