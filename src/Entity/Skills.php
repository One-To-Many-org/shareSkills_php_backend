<?php

namespace App\Entity;

use App\Repository\SkillsRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=SkillsRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="status", type="string", length=150)
 * @ORM\DiscriminatorMap({"skills" = "Skills","ownSkill" = "OwnSkill", "searchedSkill" = "SearchedSkill"})
 * @ORM\HasLifecycleCallbacks
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
     * @var Level
     * @ORM\ManyToOne(targetEntity=Level::class, inversedBy="skills")
     */
    private $level;
    /**
     * @Assert\Count(
     *      min = 1,
     *      max = 10,
     *      minMessage = " Jacob You must specify at least one field",
     *      maxMessage = "Jacob You cannot specify more than {{ limit }} fields"
     * )
     * @var Field[]
     * @ORM\ManyToMany(targetEntity=Field::class, inversedBy="skills")
     */
    private $fields;

    /**
     * @var array
     * @Groups({"full_user"})
     */
    private $fieldsDescription=[];

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
     * @ORM\Column(type="datetime",nullable=true,options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime",nullable=true,options={"default": "CURRENT_TIMESTAMP"})
     * @Groups({"full_user"})
     */
    private $updatedAt;


    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $visibility;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;


    public function __construct()
    {
        $this->createdAt=new \DateTime();;
        $this->updatedAt=new \DateTime();
        $this->fields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Field[]
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(Field $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
        }

        return $this;
    }

    public function removeField(Field $field): self
    {
        $this->fields->removeElement($field);

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(?string $visibility): self
    {
        $this->visibility = $visibility;

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

    /**
     * @return array
     */
    public function getFieldsDescription()
    {
        if(empty($this -> fieldsDescription) && count($this->fields)>0){
            foreach ($this->fields as $field){
                $this->fieldsDescription[]= $field->getDescription ();
            }

        }
        return $this -> fieldsDescription;
    }

    /**
     * @param array|Collection $fieldsDescription
     */
    public function setFieldsDescription($fieldsDescription):self
    {
        $this -> fieldsDescription = $fieldsDescription instanceof Collection? $fieldsDescription: new ArrayCollection($fieldsDescription) ;
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

    public function __toString() {
        return $this->getTitle ();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdatedAt (new \DateTime());
    }

    public function getTitle(): ?string
    {
        return empty($this->title)?$this->getFields ()->current ():$this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
