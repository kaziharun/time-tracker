<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

interface OverviewServiceInterface
{
    /**
     * @return array<int, array{
     *     project_name: string,
     *     user_name: string,
     *     daily_hours: array<string, float>,
     *     weekly_hours: array<string, float>,
     *     monthly_hours: array<string, float>}>
     */
    public function generateOverviewByProjects(User $user): array;
}
