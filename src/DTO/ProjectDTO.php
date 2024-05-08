<?php
declare(strict_types=1);
namespace App\DTO;

class ProjectDTO implements DTOInterface
{
    private ?string $name;
    private ?string $description;

    public function __construct(array $data)
    {
        $this->name = $data['project']['name'] ?? null;
        $this->description = $data['project']['description'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}

