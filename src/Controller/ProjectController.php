<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\ProjectType;
use App\Mapper\ProjectMapper;
use App\Service\ProjectServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends AbstractController
{
    public function __construct(
        private ProjectServiceInterface $projectService,
        private ProjectMapper           $projectMapper
    )
    {
    }

    public function index(): Response
    {
        $projects = $this->projectService->getAllProjects();

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
                $projectDto = $this->projectMapper->transform($form->getData());

                $result = $this->projectService->validateAndPersist($projectDto);

                if ($result->isSuccess()) {
                    $this->addFlash('messages', $result->getMessage());
                    return $this->redirectToRoute('app_project_index');
                }

                $this->addFlash('errors', $result->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('errors', 'Error creating time tracker: ' . $e->getMessage());
            }
        }

        return $this->renderForm('project/create.html.twig', [
            'form' => $form,
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        try {
            $project = $this->projectService->findProjectOrThrow($id);

            $form = $this->createForm(ProjectType::class, $project);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $projectDto = $this->projectMapper->transform($form->getData());

                $result = $this->projectService->updateProject($projectDto, $project);

                if ($result->isSuccess()) {
                    $this->addFlash('messages', $result->getMessage());
                    return $this->redirectToRoute('app_project_index');
                }

                $this->addFlash('errors', $result->getMessage());

            }
        } catch (\Exception $e) {
            $this->addFlash('errors', 'Error creating time tracker: ' . $e->getMessage());
        }

        if (!isset($form)) {
            $form = $this->createForm(ProjectType::class);
        }
        return $this->renderForm('project/edit.html.twig', [
            'form' => $form,
        ]);
    }

    public function delete(int $id): Response
    {
        try {
            $this->projectService->deleteProject($id);
            $this->addFlash('messages', 'Project deleted successfully.');
        } catch (ConflictHttpException|NotFoundHttpException $exception) {
            $this->addFlash('errors', $exception->getMessage());
        }

        return $this->redirectToRoute('app_project_index');
    }
}
