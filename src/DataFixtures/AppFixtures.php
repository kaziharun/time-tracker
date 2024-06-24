<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\TimeTracker;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadProjects($manager);
        $this->loadTimeTrackers($manager);
    }

    public function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$username, $password, $email, $roles]) {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function loadProjects(ObjectManager $manager): void
    {
        foreach ($this->getProjectData() as [$name, $description]) {
            $project = new Project();
            $project->setName($name);
            $project->setDescription($description);

            $manager->persist($project);
        }

        $manager->flush();
    }

    public function loadTimeTrackers(ObjectManager $manager): void
    {
        /** @var array<User> $users */
        $users = $manager->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_USER"%')
            ->getQuery()
            ->getResult();

        /** @var array<Project> $projects */
        $projects = $manager->getRepository(Project::class)->findAll();

        $startDate = new \DateTime('-3 months');
        $endDate = new \DateTime('now');

        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($startDate, $interval, $endDate);

        foreach ($period as $date) {
            if ($date->format('N') >= 6) {
                continue;
            }
            $durationHours = mt_rand(4, 8);
            $startHour = mt_rand(8, 10);

            $timeTracking = new TimeTracker();
            $timeTracking->setName($this->getTaskData());
            $timeTracking->setStartDate($date);

            $startTime = (new \DateTime($timeTracking->getStartDate()->format('Y-m-d')))
                ->modify("+$startHour hours");
            $timeTracking->setStartTime($startTime);

            $endTime = (clone $startTime)
                ->modify("+$durationHours hours");
            $timeTracking->setEndTime($endTime);

            $randomUser = $users[array_rand($users)];
            $timeTracking->setUser($randomUser);

            $randomProject = $projects[array_rand($projects)];
            $timeTracking->setProject($randomProject);

            $manager->persist($timeTracking);
        }

        $manager->flush();
    }

    private function getTaskData(): string
    {
        $taskNames = [
            'Implement Api integration',
            'Test Transaction via post man',
            'monitoring transactions',
        ];

        return $taskNames[array_rand($taskNames)];
    }

    /**
     * @return array<int, array{string, string}>
     */
    private function getProjectData(): array
    {
        return [
            ['CardPay Credit Card Integration', 'CardPay Credit Card Integration'],
            ['Adyen Online Bank transfer Integration', 'Adyen Online Bank transfer Integration'],
            ['Klarna Integration', 'Klarna Integration'],
        ];
    }

    /**
     * @return array<int, array{string, string, string, array<int, string>}>
     */
    private function getUserData(): array
    {
        return [
            ['admin', 'admin', 'jane_admin@symfony.com', [User::ROLE_ADMIN]],
            ['tom', 'user123', 'tom_user@symfony.com', [User::ROLE_USER]],
            ['john', 'user123', 'john_user@symfony.com', [User::ROLE_USER]],
        ];
    }
}
