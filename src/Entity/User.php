<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks as HasLifecycleCallbacks;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Cette email est déja utilisé")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @method string getUserIdentifier()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"full_user", "short_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $apiToken;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     * @Groups({"full_user", "short_user"})
     */
    private $userName;

    /**
     * @Assert\NotBlank(message="Veillez fournir un mot de pass")
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=150)
     * @Groups({"full_user", "short_user"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=150)
     * @Groups({"full_user", "short_user"})
     */
    private $lastName;

    /**
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     * @Groups({"full_user", "short_user"})
     */

    private $email;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"full_user", "short_user"})
     */
    private $phone;

    /**
     * @ORM\Column(type="date")
     * @Groups({"full_user", "short_user"})
     */
    private $birthDate;

    /**
     * @ORM\Column(type="json", nullable=true,columnDefinition="ENUM('ROLE_ADMIN','ROLE_USER')")
     * @Groups({"full_user", "short_user"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"full_user", "short_user"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Groups({"full_user", "short_user"})
     */
    private $adresse;

    /**
     * @Assert\Choice(
     *     choices = {"Mr", "Mme","other"},
     *     message = "Choose a valid genre : Mr, Mme,other"
     * )
     * @ORM\Column(type="string", length=100,columnDefinition="ENUM('Mr', 'Mme','other')")
     * @Groups({"full_user", "short_user"})
     */
    private $gender;

    /**
     * @Vich\UploadableField(mapping="pictures", fileNameProperty="filename")
     * @var File
     */
    private $picture;

    /**
     * @ORM\Column (type="string",length=100,nullable=true,options={"defaults":"60ddc260d7796765955212.jpg"})
     * * @Groups({"full_user", "short_user"})
     * @var string
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full_user", "short_user"})
     */
    private $picturesPath;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"full_user", "short_user"})
     */
    private $profileDescription;

    /**
     * @ORM\OneToMany(targetEntity=Training::class, mappedBy="user",cascade={"remove","persist"})
     * @Groups({"full_user"})
     */
    private $trainings;

    /**
     * @ORM\OneToMany(targetEntity=Experience::class, mappedBy="user",cascade={"remove","persist"})
     * @Groups({"full_user"})
     */
    private $experiences;

    /**
     * @ORM\OneToMany(targetEntity=OwnSkill::class, mappedBy="user",cascade={"remove","persist"})
     * @Groups({"full_user"})
     */
    private $ownSkills;

    /**
     * @ORM\OneToMany(targetEntity=SearchedSkill::class, mappedBy="user",cascade={"remove","persist"})
     * @Groups({"full_user"})
     */
    private $searchedSkills;

    /**
     * @ORM\Column(type="datetime_immutable",nullable=true,options={"default": "CURRENT_TIMESTAMP"})
     *  @Groups({"full_user"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime",nullable=true,options={"default": "CURRENT_TIMESTAMP"})
     * @Groups({"full_user","short_user"})
     */
    private $updatedAt;

    public function __construct()
    {
        $this->trainings = new ArrayCollection();
        $this->experiences = new ArrayCollection();
        $this->ownSkills = new ArrayCollection();
        $this->searchedSkills = new ArrayCollection();
        $this->createdAt=new \DateTimeImmutable();
        $this->updatedAt=new \DateTime();
        $this->roles[]='ROLE_USER';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = \password_hash ($password,PASSWORD_ARGON2I);

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $LastName): self
    {
        $this->lastName = $LastName;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthDate(): ?DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return File
     */
    public function getPicture()
    {
        return $this -> picture;
    }

    /**
     * @param File|null $picture
     * @return $this
     */
    public function setPicture(File $picture = null)
    {
        $this->picture = $picture;
        if ($picture) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }


    /**
     * @param string $filename
     */
    public function setFilename(?string $filename)
    {
        $this -> filename = $filename;
        $this->setPicturesPath ('profiles/pictures/'.$this->filename);
        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): ?string
    {
        return $this -> filename;
    }

    public function getPicturesPath(): ?string
    {
        return $this->picturesPath;
    }

    public function setPicturesPath(?string $picturesPath): self
    {
        $this->picturesPath = $picturesPath;

        return $this;
    }

    public function getProfileDescription(): ?string
    {
        return $this->profileDescription;
    }

    public function setProfileDescription(?string $profileDescription): self
    {
        $this->profileDescription = $profileDescription;

        return $this;
    }

    /**
     * @return Collection|Training[]
     */
    public function getTrainings(): Collection
    {
        return $this->trainings;
    }

    public function addTrainings(Training $training): self
    {
        if (!$this->trainings->contains($training)) {
            $this->trainings[] = $training;
            $training->setUser($this);
        }

        return $this;
    }

    public function removeTrainings(Training $training): self
    {
        if ($this->trainings->removeElement($training)) {
            // set the owning side to null (unless already changed)
            if ($training->getUser() === $this) {
                $training->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @param Collection |  Training [] $trainings
     */
    public function setTrainings($trainings)
    {
        $this -> trainings = $trainings instanceof Collection ?$trainings:new ArrayCollection($trainings);

        foreach ($this->trainings as $training){
            /**
             * @var Training $training
             */
            $training->setUser ($this);
        }
        return $this;
    }

    /**
     * @return Collection|Experience[]
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experience $experience): self
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences[] = $experience;
            $experience->setUser($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): self
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getUser() === $this) {
                $experience->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OwnSkill[]
     */
    public function getOwnSkills(): Collection
    {
        return $this->ownSkills;
    }

    public function addOwnSkill(OwnSkill $ownSkill): self
    {
        if (!$this->ownSkills->contains($ownSkill)) {
            $this->ownSkills[] = $ownSkill;
            $ownSkill->setUser($this);
        }

        return $this;
    }

    public function removeOwnSkill(OwnSkill $ownSkill): self
    {
        if ($this->ownSkills->removeElement($ownSkill)) {
            // set the owning side to null (unless already changed)
            if ($ownSkill->getUser() === $this) {
                $ownSkill->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SearchedSkill[]
     */
    public function getSearchedSkills(): Collection
    {
        return $this->searchedSkills;
    }

    public function addSearchedSkill(SearchedSkill $searchedSkill): self
    {
        if (!$this->searchedSkills->contains($searchedSkill)) {
            $this->searchedSkills[] = $searchedSkill;
            $searchedSkill->setUser($this);
        }

        return $this;
    }

    public function removeSearchedSkill(SearchedSkill $searchedSkill): self
    {
        if ($this->searchedSkills->removeElement($searchedSkill)) {
            // set the owning side to null (unless already changed)
            if ($searchedSkill->getUser() === $this) {
                $searchedSkill->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface $updatedAt
     * @return $this
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString() {
        //return $this->firstName.''.$this->lastName;
        return $this->email;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        if($roles){
            $this->roles =array_unique (array_merge ($roles,$this->roles));
        }
        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __call($name, $arguments)
    {
        return $this->$name.'('.implode (',',$arguments).')';
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdatedAt (new \DateTime());
    }

    public function isSame(Object $user){
        if($user instanceof self){
            return $user->getId ()===$this->getId ();
        }
        return false;
    }


}
