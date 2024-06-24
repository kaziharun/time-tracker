<?php

declare(strict_types=1);

namespace App\DTO;

class ResultDTO
{
    private function __construct(
        private readonly bool $success,
        private readonly string $message
    ) {
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
