<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
    @ORM\Entity()
 */
class OwnSkill extends Skills
{
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ownSkills")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
