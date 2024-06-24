<?php

declare(strict_types=1);

namespace App\Mapper;

use App\DTO\ProjectDTO;
use App\Entity\Project;

class ProjectMapper
{
    public function transform(Project $project): ProjectDTO
    {
        return new ProjectDTO(
            name: $project->getName(),
            description: $project->getDescription(),
        );
    }
}
