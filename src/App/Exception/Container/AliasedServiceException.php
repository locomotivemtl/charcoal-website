<?php

declare(strict_types=1);

namespace App\Exception\Container;

use Psr\Container\ContainerExceptionInterface;

/**
 * An attempt to modify an aliased service was made.
 */
class AliasedServiceException extends \RuntimeException implements ContainerExceptionInterface
{
    /**
     * @param string $id Identifier of the aliased service.
     */
    public function __construct(string $id)
    {
        parent::__construct(
            \sprintf('Cannot override aliased service: "%s"', $id)
        );
    }
}
