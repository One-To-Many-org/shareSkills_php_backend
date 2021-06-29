<?php

namespace App\Entity;

use App\Repository\SkillsRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=SkillsRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="status", type="string", length=150)
 * @ORM\DiscriminatorMap({"skills" = "Skills","ownSkill" = "OwnSkill", "searchedSkill" = "SearchedSkill"})
 */
abstract class Skills
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"full_user"})
     */
    private $id;

    /**
     * @var Field
     * @ORM\ManyToOne(targetEntity=Field::class, inversedBy="skills")
     */
    private $field;

    /**
     * @var Level
     * @ORM\ManyToOne(targetEntity=Level::class, inversedBy="skills")
     */
    private $level;

    /**
     * @var string
     * @Groups({"full_user"})
     */
    private $fieldDescription;

    /**
     * @var string
     * @Groups({"full_user"})
     */
    private $levelDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"full_user"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
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

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function setField(?Field $field): self
    {
        $this->field = $field;

        return $this;
    }
/**
    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
 * */

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
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

    /**
     * @return string
     */
    public function getFieldDescription()
    {
        if(empty($this -> fieldDescription) && !empty($this->field)){
           $this->fieldDescription= $this->field->getDescription ();
        }
        return $this -> fieldDescription;
    }

    /**
     * @param string $fieldDescription
     */
    public function setFieldDescription(string $fieldDescription):self
    {
        $this -> fieldDescription = $fieldDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelDescription()
    {
        if(empty($this->levelDescription)&&!empty($this->level)){
            $this->levelDescription=$this->level->getDescription ();
        }
        return $this -> levelDescription;
    }

    /**
     * @param string $levelDescription
     */
    public function setLevelDescription(string $levelDescription): self
    {
        $this -> levelDescription = $levelDescription;
        return $this;
    }
}
