<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ExporterDTO;
use App\Service\ExporterServiceInterface;
use App\Service\OverviewServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ExporterController extends AbstractBaseController
{
    public function __construct(
        private readonly OverviewServiceInterface $overviewService,
        private readonly ExporterServiceInterface $exportService,
        private readonly Security $security
    ) {
        parent::__construct($this->security);
    }

    public function export(string $format): Response
    {
        $exportData = $this->overviewService->generateOverviewByProjects($this->getUser());

        return $this->exportService->export(
            new ExporterDTO($exportData),
            $format
        );
    }
}
