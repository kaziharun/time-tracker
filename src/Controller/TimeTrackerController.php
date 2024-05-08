<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\ProjectDTO;
use App\DTO\TimeTrackerDTO;
use App\Entity\TimeTracker;
use App\Factory\TimeTrackerFactory;
use App\Form\TimeTrackerType;
use App\Service\TimeTrackerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TimeTrackerController extends AbstractController
{
    public function __construct(
        private TimeTrackerService $timeTrackerService,
        private TimeTrackerFactory $timeTrackerFactory
    )
    {
    }

    public function index(): Response
    {
        $user = $this->getUser();

        $timeTrackers = $this->timeTrackerService->getTimeTrackersForUser($user->getId());

        return $this->render('time_tracker/index.html.twig', [
            'timeTrackers' => $timeTrackers,
        ]);
    }

    public function create(): Response
    {
        $form = $this->createForm(TimeTrackerType::class, new TimeTracker());

        return $this->render('time_tracker/create.html.twig', [
            'form' => $form->createView(),
            'submit_route' => 'app_time_tracker_store'
        ]);
    }

    public function store(Request $request): Response
    {
        $timeTrackerDto = new TimeTrackerDTO($request->request->all());
        $timeTracker = $this->timeTrackerFactory->create($timeTrackerDto, $this->getUser());

        $form = $this->createForm(TimeTrackerType::class, $timeTracker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->timeTrackerService->validateAndPersist($timeTracker);
            if ($result->isSuccess()) {
                $this->addFlash('messages', $result->getMessage());
                return $this->redirectToRoute('app_time_tracker_index');
            }

            $this->addFlash('errors', $result->getMessage());
        }

        return $this->renderForm('time_tracker/create.html.twig', [
            'form' => $form->createView(),
            'submit_route' => 'app_time_tracker_store'
        ]);
    }

    public function edit(int $id): Response
    {
        try {
            $timeTracker = $this->timeTrackerService->findTimeTrackerOrThrow($id);
            $form = $this->createForm(TimeTrackerType::class, $timeTracker);

            return $this->renderForm('time_tracker/edit.html.twig', [
                'form' => $form,
                'submit_route' => 'app_time_tracker_update',
                'id' => $id
            ]);
        } catch (NotFoundHttpException $exception) {
            $this->addFlash('errors', $exception->getMessage());
        }
        return $this->redirectToRoute('app_time_tracker_index');
    }

    public function update(Request $request, int $id): Response
    {
        try {
            $timeTracker = $this->timeTrackerService->findTimeTrackerOrThrow($id);
            $form = $this->createForm(TimeTrackerType::class, $timeTracker);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $result = $this->timeTrackerService->updateTimeTracker($timeTracker);
                if ($result->isSuccess()) {
                    $this->addFlash('success', 'Time tracker updated successfully.');
                    return $this->redirectToRoute('app_time_tracker_index');
                }

                $this->addFlash('errors', $result->getMessage());
            }
        } catch (NotFoundHttpException $exception) {
            $this->addFlash('errors', $exception->getMessage());
        }
        return $this->renderForm('time_tracker/edit.html.twig', [
            'form' => $form->createView(),
            'submit_route' => 'app_time_tracker_update',
            'id' => $id
        ]);
    }

    public function delete(int $id): Response
    {
        try {
            $this->timeTrackerService->deleteTimeTracker($id);
            $this->addFlash('messages', 'Time tracker deleted successfully.');
        } catch (NotFoundHttpException $exception) {
            $this->addFlash('errors', $exception->getMessage());
        }
        return $this->redirectToRoute('app_time_tracker_index');
    }
}
