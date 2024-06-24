<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

abstract class AbstractBaseController extends AbstractController
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function getUser(): User
    {
        $user = $this->security->getUser();

        assert($user instanceof User);

        return $user;
    }
}
