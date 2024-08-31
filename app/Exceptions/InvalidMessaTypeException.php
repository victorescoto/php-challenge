<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InvalidMessageTypeException extends Exception
{
    public function __construct(array $messageData)
    {
        $messageType = $messageData['type'] ?? 'unknown';
        parent::__construct("Invalid message type: " . json_encode($messageData));
    }
}
