<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\OverviewServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class OverviewController extends AbstractBaseController
{
    public function __construct(
        private readonly OverviewServiceInterface $overviewService,
        private readonly Security $security
    ) {
        parent::__construct($this->security);
    }

    public function index(): Response
    {
        $overviews = $this->overviewService->generateOverviewByProjects($this->getUser());

        return $this->render('overview/index.html.twig', [
            'overviews' => $overviews,
        ]);
    }
}
