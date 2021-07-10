<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 * @UniqueEntity(fields={"name","country"}, message="It looks like this city already exist!")
 */
class City
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="cities",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=150,nullable=true)
     */
    private $fullName;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        $this->setFullName ();
        return $this;
    }

    public function __toString() {
        return $this->name;
    }

    public function getCountry(): ?Country
    {
        return $this->country;

    }

    public function setCountry(?Country $country): self
    {

        $this->country = $country;
        $this->setFullName ();
        return $this;
    }

    public function getFullName(): ?string
    {
        $this->setFullName ();
        return $this->fullName;
    }

    public function setFullName()
    {
        \Locale::setDefault('en');
        if(!$this->fullName && $this->name && $this->country){
            $this->fullName =$this->name.' ('.strtoupper (Countries::getName($this->country->getName())).')';
        }
        return $this;
    }


}
