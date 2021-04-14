<?php

namespace App\Exception\Container;

use Psr\Container\ContainerExceptionInterface;

/**
 * Thrown if a service is undefined or some arguments are missing.
 */
class BadServiceCallException extends \LogicException implements ContainerExceptionInterface
{
}
