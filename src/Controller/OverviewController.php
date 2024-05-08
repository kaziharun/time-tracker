<?php
declare(strict_types=1);
namespace App\Controller;

use App\Service\OverviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class OverviewController extends AbstractController
{
    public function __construct(
        private OverviewService $overviewService,
    )
    {
    }

    public function index(): Response
    {
        $overviews = $this->overviewService->generateOverviewByProjects($this->getUser());
        return $this->render('overview/index.html.twig', [
            'overviews' => $overviews,
        ]);
    }

    public function exportCSV(): Response
    {
        $overviews = $this->overviewService->generateOverviewByProjects($this->getUser());

        return $this->overviewService->exportToCsv($overviews);
    }
}
