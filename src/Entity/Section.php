<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=SectionRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=100)
 * @ORM\DiscriminatorMap({"section" = "Section","training" = "Training", "experience" = "Experience"})
 */
 class Section
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"full_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"full_user"})
     */
    private $institution;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"full_user"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"full_user"})
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full_user"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"full_user"})
     */
    private $qualification;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full_user"})
     */
    private $title;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"full_user"})
     */
    private $startedDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"full_user"})
     */
    private $endDate;

     /**
      * @ORM\ManyToOne(targetEntity=User::class)
      * @ORM\JoinColumn(nullable=false)
      */
     private $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"full_user"})
     */
    private $description;

     /**
      * @ORM\Column(type="datetime_immutable")
      */
     private $createdAt;

     /**
      * @ORM\Column(type="datetime")
      * @Groups({"full_user"})
      */
     private $updatedAt;


     public function __construct()
     {
         $this->createdAt=new DateTimeImmutable();
         $this->updatedAt=new \DateTime();
     }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstitution(): ?string
    {
        return $this->institution;
    }

    public function setInstitution(string $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setQualification(string $qualification): self
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStartedDate(): ?\DateTimeInterface
    {
        return $this->startedDate;
    }

    public function setStartedDate(?\DateTimeInterface $startedDate): self
    {
        $this->startedDate = $startedDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
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
