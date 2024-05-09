<?php
declare(strict_types=1);

namespace App\Service;

class Result
{
    public function __construct(
        private bool   $success,
        private string $message
    )
    {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public static function Failed(string $message): self
    {
        return new self(false, $message);
    }

    public static function Success(string $message): self
    {
        return new self(true, $message);
    }
}
