<?php

declare(strict_types=1);

namespace App\Exception\Http;

/**
 * Thrown if a client error is encountered (4xx codes).
 */
class ClientException extends \RuntimeException implements ExceptionInterface
{
}
