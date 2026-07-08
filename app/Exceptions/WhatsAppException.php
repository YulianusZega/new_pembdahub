<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Base exception for WhatsApp API related errors.
 */
class WhatsAppException extends RuntimeException
{
    protected string $phone;
    protected ?string $context;

    public function __construct(string $message, string $phone = '', ?string $context = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->phone = $phone;
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }
}
