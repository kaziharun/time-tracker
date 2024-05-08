<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Project;

interface ProjectServiceInterface
{
    public function validateAndPersist(Project $project): Result;
    public function findProjectOrThrow(int $id): Project;
    public function deleteProject(int $id): void;
}
