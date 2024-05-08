<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

interface OverviewServiceInterface
{
    public function generateOverviewByProjects(User $user): array;
    public function exportToCsv(array $data): Response;
}

