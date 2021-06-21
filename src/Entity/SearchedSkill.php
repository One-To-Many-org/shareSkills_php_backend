<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 @ORM\Entity()
 */
class SearchedSkill extends Skills
{
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="searchedSkills")
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
