<?php

namespace App\Factory;


use App\DTO\DTOInterface;
use App\DTO\ProjectDTO;
use App\Entity\Project;

class ProjectFactory implements FactoryInterface
{
    public function create(ProjectDTO $dto): Project
    {
        $project = new Project();
        $project->setName($dto->getName());
        $project->setDescription($dto->getDescription());

        return $project;
    }
}
