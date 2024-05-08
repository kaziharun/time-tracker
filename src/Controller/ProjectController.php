<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\ProjectDTO;
use App\Entity\Project;
use App\Factory\ProjectFactory;
use App\Form\ProjectType;
use App\Service\ProjectService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProjectService $projectService,
        private ProjectFactory $projectFactory,
    )
    {
    }

    public function index(): Response
    {
        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    public function create(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        return $this->render('project/create.html.twig', [
            'form' => $form->createView(),
            'submit_route' => 'app_project_store'
        ]);
    }

    public function store(Request $request): Response
    {
        $projectDto = new ProjectDTO($request->request->all());
        $project = $this->projectFactory->create($projectDto);

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->projectService->validateAndPersist($project);
            if ($result->isSuccess()) {
                $this->addFlash('messages', $result->getMessage());
                return $this->redirectToRoute('app_project_index');
            }
            $this->addFlash('errors', $result->getMessage());
        }
        return $this->render('project/create.html.twig', [
            'form' => $form->createView(),
            'submit_route' => 'app_project_store'
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        try {
            $project = $this->projectService->findProjectOrThrow($id);
            $form = $this->createForm(ProjectType::class, $project);
            $form->handleRequest($request);

            return $this->render('project/edit.html.twig', [
                'form' => $form->createView(),
                'submit_route' => 'app_project_update',
                'id' => $id
            ]);
        } catch (NotFoundHttpException $exception) {
            $this->addFlash('errors', $exception->getMessage());
        }

        return $this->redirectToRoute('app_project_index');
    }

    public function update(Request $request, int $id): Response
    {
        try {
            $project = $this->projectService->findProjectOrThrow($id);
            $form = $this->createForm(ProjectType::class, $project);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $result = $this->projectService->validateAndPersist($project);
                if ($result->isSuccess()) {
                    $this->addFlash('messages', $result->getMessage());
                    return $this->redirectToRoute('app_project_index');
                }

                $this->addFlash('errors', $result->getMessage());
            }
        } catch (NotFoundHttpException $exception) {
            $this->addFlash('errors', $exception->getMessage());
        }
        return $this->render('project/edit.html.twig', [
            'form' => $form->createView(),
            'submit_route' => 'app_project_update'
        ]);
    }

    public function delete(int $id): Response
    {
        try {
            $this->projectService->deleteProject($id);
            $this->addFlash('messages', 'Project deleted successfully.');
        } catch (NotFoundHttpException $exception) {
            $this->addFlash('errors', $exception->getMessage());
        }
        return $this->redirectToRoute('app_project_index');
    }

}
