<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Exception for payment processing failures.
 */
class PaymentException extends RuntimeException
{
    protected ?int $studentId;
    protected ?int $billId;

    public function __construct(string $message, ?int $studentId = null, ?int $billId = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->studentId = $studentId;
        $this->billId = $billId;
        parent::__construct($message, $code, $previous);
    }

    public function getStudentId(): ?int
    {
        return $this->studentId;
    }

    public function getBillId(): ?int
    {
        return $this->billId;
    }
}
