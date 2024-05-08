<?php
declare(strict_types=1);

namespace App\Service;

class Result
{
    public function __construct(
        private bool $success,
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

    public function addMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}
