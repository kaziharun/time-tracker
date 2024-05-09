<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\TimeTrackerType;
use App\Mapper\TimeTrackerMapper;
use App\Service\TimeTrackerServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TimeTrackerController extends AbstractController
{
    public function __construct(
        private TimeTrackerServiceInterface $timeTrackerService,
        private TimeTrackerMapper           $timeTrackerMapper,
        private Security                    $security
    )
    {
    }

    public function index(): Response
    {
        $user = $this->getUser();

        $timeTrackers = $this->timeTrackerService->getTimeTrackersByUser($user);

        return $this->render('time_tracker/index.html.twig', [
            'timeTrackers' => $timeTrackers,
        ]);
    }

    public function create(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(TimeTrackerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $timeTrackerDTO = $this->timeTrackerMapper->transform($form->getData());

                $result = $this->timeTrackerService->validateAndPersist($timeTrackerDTO, $user);

                if ($result->isSuccess()) {

                    $this->addFlash('messages', $result->getMessage());
                    return $this->redirectToRoute('app_time_tracker_index');
                }

                $this->addFlash('errors', $result->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('errors', 'Error creating time tracker: ' . $e->getMessage());
            }
        }

        return $this->renderForm('time_tracker/create.html.twig', [
            'form' => $form,
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $user = $this->getUser();

        try {
            $timeTracker = $this->timeTrackerService->findTimeTrackerOrThrow($user, $id);

            $form = $this->createForm(TimeTrackerType::class, $timeTracker);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $timeTrackerDto = $this->timeTrackerMapper->transform($form->getData());

                $result = $this->timeTrackerService->updateTimeTracker($timeTrackerDto, $timeTracker, $user);

                if ($result->isSuccess()) {

                    $this->addFlash('messages', $result->getMessage());
                    return $this->redirectToRoute('app_time_tracker_index');
                }

                $this->addFlash('errors', $result->getMessage());

            }
        } catch (\Exception $e) {

            $this->addFlash('errors', 'Error creating time tracker: ' . $e->getMessage());
        }

        if (!isset($form)) {
            $form = $this->createForm(TimeTrackerType::class);
        }

        return $this->renderForm('time_tracker/edit.html.twig', [
            'form' => $form,
        ]);
    }

    public function delete(int $id): Response
    {
        $user = $this->getUser();

        try {
            $this->timeTrackerService->deleteTimeTracker($user, $id);

            $this->addFlash('messages', 'Time tracker deleted successfully.');

        } catch (AccessDeniedHttpException|NotFoundHttpException $exception) {

            $this->addFlash('errors', $exception->getMessage());
        }
        return $this->redirectToRoute('app_time_tracker_index');
    }

    public function getUser(): ?User
    {
        $user = $this->security->getUser();

        assert($user instanceof User);

        return $user;
    }
}
