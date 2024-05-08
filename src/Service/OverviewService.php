<?php
declare(strict_types=1);

namespace App\Service;

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
            $userName = $timeTrack->getUser()->getUsername();
            $project = $timeTrack->getProject(); // Assuming 'getProject' returns the project entity
            $projectId = $project->getId();
            $projectName = $project->getName(); // Adjust this based on your actual project entity
            $date = clone $timeTrack->getStartDate();
            $start = $timeTrack->getStartTime();
            $end = $timeTrack->getEndTime();
            $duration = $end->diff($start);
            $hours = round($duration->h + ($duration->i / 60) + ($duration->days * 24), 2);

            // Initialize project array if not set
            if (!isset($projectHours[$projectId])) {
                $projectHours[$projectId] = [
                    'project_name' => $projectName,
                    'user_name' => $userName,
                    'daily_hours' => [],
                    'weekly_hours' => [],
                    'monthly_hours' => []
                ];
            }

            // Daily Hours
            $dailyDate = $date->format('Y-m-d');
            if (!isset($projectHours[$projectId]['daily_hours'][$dailyDate])) {
                $projectHours[$projectId]['daily_hours'][$dailyDate] = 0;
            }
            $projectHours[$projectId]['daily_hours'][$dailyDate] += $hours;

            // Weekly Hours
            $weekStartDate = (clone $date)->modify('last sunday');
            $weeklyDate = $weekStartDate->format('Y-W');
            if (!isset($projectHours[$projectId]['weekly_hours'][$weeklyDate])) {
                $projectHours[$projectId]['weekly_hours'][$weeklyDate] = 0;
            }
            $projectHours[$projectId]['weekly_hours'][$weeklyDate] += $hours;

            // Monthly Hours
            $monthlyDate = $date->format('Y-m');
            if (!isset($projectHours[$projectId]['monthly_hours'][$monthlyDate])) {
                $projectHours[$projectId]['monthly_hours'][$monthlyDate] = 0;
            }
            $projectHours[$projectId]['monthly_hours'][$monthlyDate] += $hours;
        }

        return $projectHours;
    }

    public function exportToCsv(array $data): Response
    {
        $timestamp = (new \DateTime())->format('Ymd_His');

        $response = new StreamedResponse(function () use ($data) {
            $output = fopen('php://output', 'w');
            fputcsv($output, [' ID', 'Type', 'User Name', 'Project Name', 'Date', 'Hours']);
            $i = 1;
            foreach ($data as $projectId => $projectData) {
                $projectName = $projectData['project_name'];
                $userName = $projectData['user_name'];
                foreach ($projectData['daily_hours'] as $date => $hours) {
                    fputcsv($output, [$i++, 'daily', $userName, $projectName, $date, $hours]);
                }
                foreach ($projectData['weekly_hours'] as $date => $hours) {
                    fputcsv($output, [$i++, 'weekly', $userName, $projectName, $date, $hours]);
                }
                foreach ($projectData['monthly_hours'] as $date => $hours) {
                    fputcsv($output, [$i++, 'monthly', $userName, $projectName, $date, $hours]);
                }
            }
            fclose($output);
        });

        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="time_tracker_export_' . $timestamp . '.csv"');

        return $response;
    }
}
