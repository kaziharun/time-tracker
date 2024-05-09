<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\TimeTracker;
use App\Entity\User;
use App\Repository\TimeTrackerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OverviewService implements OverviewServiceInterface
{
    public function __construct(private TimeTrackerRepository $timeTrackerRepository)
    {
    }

    public function generateOverviewByProjects(User $user): array
    {
        $timeTracks = $this->timeTrackerRepository->findByUser($user->getId());
        $projectHours = [];

        foreach ($timeTracks as $timeTrack) {
            if($timeTrack->getEndTime()){
                $projectId = $timeTrack->getProject()->getId();
                $hours = $this->calculateHours($timeTrack);

                if (!isset($projectHours[$projectId])) {
                    $projectHours[$projectId] = $this->initializeProjectData($timeTrack);
                }

                $projectHours[$projectId] = $this->updateProjectHours($projectHours[$projectId], $timeTrack, $hours);
            }
        }

        return $projectHours;
    }

    private function calculateHours(TimeTracker $timeTrack): float
    {
        $start = $timeTrack->getStartTime();
        $end = $timeTrack->getEndTime();
        $duration = $end->diff($start);
        return round($duration->h + ($duration->i / 60) + ($duration->days * 24), 2);
    }

    private function initializeProjectData(TimeTracker $timeTrack): array
    {
        return [
            'project_name' => $timeTrack->getProject()->getName(),
            'user_name' => $timeTrack->getUser()->getUsername(),
            'daily_hours' => [],
            'weekly_hours' => [],
            'monthly_hours' => []
        ];
    }

    private function updateProjectHours(array $projectData, TimeTracker $timeTrack, float $hours): array
    {
        $date = clone $timeTrack->getStartDate();
        $projectData['daily_hours'] = $this->updateDailyHours($projectData['daily_hours'], $date, $hours);
        $projectData['weekly_hours'] = $this->updateWeeklyHours($projectData['weekly_hours'], $date, $hours);
        $projectData['monthly_hours'] = $this->updateMonthlyHours($projectData['monthly_hours'], $date, $hours);

        return $projectData;
    }

    private function updateDailyHours(array $dailyHours, \DateTimeInterface $date, float $hours): array
    {
        $dailyDate = $date->format('Y-m-d');
        $dailyHours[$dailyDate] = ($dailyHours[$dailyDate] ?? 0) + $hours;
        return $dailyHours;
    }

    private function updateWeeklyHours(array $weeklyHours, \DateTimeInterface $date, float $hours): array
    {
        $weekStartDate = (clone $date)->modify('last sunday');
        $weeklyDate = $weekStartDate->format('Y-W');
        $weeklyHours[$weeklyDate] = ($weeklyHours[$weeklyDate] ?? 0) + $hours;
        return $weeklyHours;
    }

    private function updateMonthlyHours(array $monthlyHours, \DateTimeInterface $date, float $hours): array
    {
        $monthlyDate = $date->format('Y-m');
        $monthlyHours[$monthlyDate] = ($monthlyHours[$monthlyDate] ?? 0) + $hours;
        return $monthlyHours;
    }

    public function exportToCsv(array $data): Response
    {
        $timestamp = (new \DateTime())->format('Ymd_His');
        $filename = "time_tracker_export_{$timestamp}.csv";

        $response = new StreamedResponse(function () use ($data) {
            $this->writeCsv($data);
        });

        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }

    private function writeCsv(array $data): void
    {
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Type', 'User Name', 'Project Name', 'Date', 'Hours']);
        $i = 1;

        foreach ($data as $projectData) {
            $i = $this->writeProjectData($output, $projectData, $i);
        }

        fclose($output);
    }

    /**
     * @param resource $output The output stream resource.
     */
    private function writeProjectData($output, array $projectData, int $i): int
    {
        $projectName = $projectData['project_name'];
        $userName = $projectData['user_name'];

        foreach (['daily_hours', 'weekly_hours', 'monthly_hours'] as $type) {
            foreach ($projectData[$type] as $date => $hours) {
                fputcsv($output, [$i++, $type, $userName, $projectName, $date, $hours]);
            }
        }
        return $i;
    }
}
