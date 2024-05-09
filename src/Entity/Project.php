<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, TimeTracker>
     */
    #[ORM\OneToMany(targetEntity: TimeTracker::class, mappedBy: 'project')]
    private Collection $timeTrackers;

    public function __construct()
    {
        $this->timeTrackers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, TimeTracker>
     */
    public function getTimeTrackers(): Collection
    {
        return $this->timeTrackers;
    }

    public function addTimeTracker(TimeTracker $timeTracker): static
    {
        if (!$this->timeTrackers->contains($timeTracker)) {
            $this->timeTrackers->add($timeTracker);
            $timeTracker->setProject($this);
        }

        return $this;
    }

    public function removeTimeTracker(TimeTracker $timeTracker): static
    {
        if ($this->timeTrackers->removeElement($timeTracker)) {
            // set the owning side to null (unless already changed)
            if ($timeTracker->getProject() === $this) {
                $timeTracker->setProject(null);
            }
        }

        return $this;
    }
}
