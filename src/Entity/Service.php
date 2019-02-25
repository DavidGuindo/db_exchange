<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 */
class Service
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="services")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\users", inversedBy="services")
     */
    private $userOffer;

    /**
     * Contructor de la clase
     */
    public function __construct($data){
        $this->name=$data['name'];
        $this->img=$data['img'];
        $this->category=$data['category'];
        $this->userOffer=$data['userOffer'];
    }


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

        return $this;
    }

    public function getCategory(): ?category
    {
        return $this->category;
    }

    public function setCategory(?category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getUserOffer(): ?users
    {
        return $this->userOffer;
    }

    public function setUserOffer(?users $userOffer): self
    {
        $this->userOffer = $userOffer;

        return $this;
    }
}
