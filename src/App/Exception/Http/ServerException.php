<?php

declare(strict_types=1);

namespace App\Exception\Http;

/**
 * Thrown if a server error is encountered (5xx codes).
 */
class ServerException extends \RuntimeException implements ExceptionInterface
{
}
