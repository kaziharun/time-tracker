<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TimeTracker;
use App\Entity\User;
use App\Repository\TimeTrackerRepository;

class OverviewService implements OverviewServiceInterface
{
    public function __construct(private readonly TimeTrackerRepository $timeTrackerRepository)
    {
    }

    /**
     * @return array<int, array{
     *     project_name: string,
     *     user_name: string,
     *     daily_hours: array<string, float>,
     *     weekly_hours: array<string, float>,
     *     monthly_hours: array<string, float>
     * }>
     */
    public function generateOverviewByProjects(User $user): array
    {
        $timeTracks = $this->timeTrackerRepository->findByUser($user->getId());
        $projectHours = [];

        foreach ($timeTracks as $timeTrack) {
            $timeTrackProject = $timeTrack->getProject();
            if ($timeTrack->getEndTime() && $timeTrackProject) {
                $projectId = $timeTrackProject->getId();
                $hours = $this->calculateHours($timeTrack->getStartTime(), $timeTrack->getEndTime());

                if (!isset($projectHours[$projectId])) {
                    $projectHours[$projectId] = $this->initializeProjectData($timeTrack);
                }

                $projectHours[$projectId] = $this->updateProjectHours($projectHours[$projectId], $timeTrack, $hours);
            }
        }

        return $projectHours;
    }

    private function calculateHours(\DateTimeInterface $start, \DateTimeInterface $end): float
    {
        $duration = $end->diff($start);

        return round($duration->h + ($duration->i / 60) + ($duration->days * 24), 2);
    }

    /**
     * @return array{
     *     project_name:string,
     *     user_name:string,
     *     daily_hours:array<string, float>,
     *     weekly_hours:array<string, float>,
     *     monthly_hours:array<string, float>
     * }
     */
    private function initializeProjectData(TimeTracker $timeTrack): array
    {
        return [
            'project_name' => (string) $timeTrack->getProject()?->getName(),
            'user_name' => (string) $timeTrack->getUser()?->getUsername(),
            'daily_hours' => [],
            'weekly_hours' => [],
            'monthly_hours' => [],
        ];
    }

    /**
     * @param array{
     *      project_name: string,
     *      user_name: string,
     *      daily_hours: array<string, float>,
     *      weekly_hours: array<string, float>,
     *      monthly_hours: array<string, float>
     *  } $projectData
     *
     * @return array{
     *     project_name: string,
     *     user_name: string,
     *     daily_hours:array<string, float>,
     *     weekly_hours:array<string, float>,
     *     monthly_hours:array<string, float>,
     * }
     */
    private function updateProjectHours(array $projectData, TimeTracker $timeTrack, float $hours): array
    {
        $date = clone $timeTrack->getStartDate();
        $projectData['daily_hours'] = $this->updateDailyHours($projectData['daily_hours'], $date, $hours);
        $projectData['weekly_hours'] = $this->updateWeeklyHours($projectData['weekly_hours'], $date, $hours);
        $projectData['monthly_hours'] = $this->updateMonthlyHours($projectData['monthly_hours'], $date, $hours);

        return $projectData;
    }

    /**
     * @param array<string, float> $dailyHours
     *
     * @return array<string, float>
     */
    private function updateDailyHours(array $dailyHours, \DateTimeInterface $date, float $hours): array
    {
        $dailyDate = $date->format('Y-m-d');
        $dailyHours[$dailyDate] = ($dailyHours[$dailyDate] ?? 0) + $hours;

        return $dailyHours;
    }

    /**
     * @param array<string, float> $weeklyHours
     *
     * @return array<string, float>
     */
    private function updateWeeklyHours(array $weeklyHours, \DateTimeInterface $date, float $hours): array
    {
        $weekStartDate = (new \DateTime($date->format('Y-m-d')))->modify('last sunday');
        $weeklyDate = $weekStartDate->format('Y-W');
        $weeklyHours[$weeklyDate] = ($weeklyHours[$weeklyDate] ?? 0) + $hours;

        return $weeklyHours;
    }

    /**
     * @param array<string, float> $monthlyHours
     *
     * @return array<string, float>
     */
    private function updateMonthlyHours(array $monthlyHours, \DateTimeInterface $date, float $hours): array
    {
        $monthlyDate = $date->format('Y-m');
        $monthlyHours[$monthlyDate] = ($monthlyHours[$monthlyDate] ?? 0) + $hours;

        return $monthlyHours;
    }
}
