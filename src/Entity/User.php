<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Project::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ownedProjects;

    #[ORM\ManyToMany(mappedBy: 'members', targetEntity: Project::class)]
    private Collection $memberProjects;

    public function __construct()
    {
        $this->ownedProjects = new ArrayCollection();
        $this->memberProjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getOwnedProjects(): Collection
    {
        return $this->ownedProjects;
    }

    public function addOwnedProject(Project $project): self
    {
        if (!$this->ownedProjects->contains($project)) {
            $this->ownedProjects[] = $project;
            $project->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedProject(Project $project): self
    {
        if ($this->ownedProjects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getOwner() === $this) {
                $project->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getMemberProjects(): Collection
    {
        return $this->memberProjects;
    }

    public function addMemberProject(Project $project): self
    {
        if (!$this->memberProjects->contains($project)) {
            $this->memberProjects[] = $project;
            $project->addMember($this);
        }

        return $this;
    }

    public function removeMemberProject(Project $project): self
    {
        if ($this->memberProjects->removeElement($project)) {
            $project->removeMember($this);
        }

        return $this;
    }

    public function getProjects(): Collection
    {
        $allProjects = new ArrayCollection();

        foreach ($this->ownedProjects as $project) {
            if (!$allProjects->contains($project)) {
                $allProjects->add($project);
            }
        }

        foreach ($this->memberProjects as $project) {
            if (!$allProjects->contains($project)) {
                $allProjects->add($project);
            }
        }

        return $allProjects;
    }

}
