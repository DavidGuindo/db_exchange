<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="services")
     */
    private $userOffer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="services")
     */
    private $city;

    /**
     * @ORM\Column(type="integer")
     */
    private $time;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Request", mappedBy="service")
     */
    private $requests;

    /**
     * Contructor de la clase
     */
    public function __construct($data){
        $this->name=$data['name'];
        $this->img=$data['img'];
        $this->category=$data['category'];
        $this->city=$data['city'];
        $this->userOffer=$data['userOffer'];
        $this->time=$data['time'];
        $this->requests = new ArrayCollection();
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

    public function getCity(): ?city
    {
        return $this->city;
    }

    public function setCity(?city $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return Collection|Request[]
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Request $request): self
    {
        if (!$this->requests->contains($request)) {
            $this->requests[] = $request;
            $request->setService($this);
        }

        return $this;
    }

    public function removeRequest(Request $request): self
    {
        if ($this->requests->contains($request)) {
            $this->requests->removeElement($request);
            // set the owning side to null (unless already changed)
            if ($request->getService() === $this) {
                $request->setService(null);
            }
        }

        return $this;
    }
}
