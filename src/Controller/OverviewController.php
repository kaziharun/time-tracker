<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\OverviewServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;

class OverviewController extends AbstractController
{
    public function __construct(
        private OverviewServiceInterface $overviewService,
        private Security $security
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

    public function getUser(): ?User
    {
        $user = $this->security->getUser();

        assert($user instanceof User);

        return $user;
    }
}
