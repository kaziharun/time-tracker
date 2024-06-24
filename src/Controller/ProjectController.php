<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Mapper\ProjectMapper;
use App\Service\ProjectServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProjectController extends AbstractBaseController
{
    public function __construct(
        private readonly ProjectServiceInterface $projectService,
        private readonly ProjectMapper $projectMapper,
        private readonly Security $security
    ) {
        parent::__construct($this->security);
    }

    public function index(): Response
    {
        $projects = $this->projectService->getAll();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    public function create(Request $request): Response
    {
        $form = $this->createForm(ProjectType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var Project $project */
                $project = $form->getData();
                $projectDto = $this->projectMapper->transform($project);

                $result = $this->projectService->validateAndPersist($projectDto);

                if ($result->isSuccess()) {
                    $this->addFlash('messages', $result->getMessage());

                    return $this->redirectToRoute('app_project_index');
                }

                $this->addFlash('errors', $result->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('errors', 'Error creating time tracker: '.$e->getMessage());
            }
        }

        return $this->renderForm('project/create.html.twig', [
            'form' => $form,
        ]);
    }

    public function edit(Request $request, int $projectId): Response
    {
        try {
            $project = $this->projectService->findOrThrow($projectId);

            $form = $this->createForm(ProjectType::class, $project);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Project $project */
                $project = $form->getData();

                $projectDto = $this->projectMapper->transform($project);

                $result = $this->projectService->update($projectDto, $project);

                if ($result->isSuccess()) {
                    $this->addFlash('messages', $result->getMessage());

                    return $this->redirectToRoute('app_project_index');
                }

                $this->addFlash('errors', $result->getMessage());
            }
        } catch (\Exception $e) {
            $this->addFlash('errors', 'Error creating time tracker: '.$e->getMessage());
            $form = $this->createForm(ProjectType::class);
        }

        return $this->renderForm('project/edit.html.twig', [
            'form' => $form,
        ]);
    }

    public function delete(int $projectId): Response
    {
        try {
            $this->projectService->delete($projectId);
            $this->addFlash('messages', 'Project deleted successfully.');
        } catch (ConflictHttpException|NotFoundHttpException $exception) {
            $this->addFlash('errors', $exception->getMessage());
        }

        return $this->redirectToRoute('app_project_index');
    }
}
