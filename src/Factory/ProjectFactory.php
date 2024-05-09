<?php

namespace App\Factory;

use App\DTO\ProjectDTO;
use App\Entity\Project;

class ProjectFactory
{
    public function createOrUpdate(ProjectDTO $dto, ?Project $project = null): Project
    {
        $project = $project ?? new Project();

        $project->setName($dto->getName());
        $project->setDescription($dto->getDescription());

        return $project;
    }
}
