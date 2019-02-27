<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RequestRepository")
 */
class Request
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="requests")
     */
    private $userRequest;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\service", inversedBy="requests")
     */
    private $service;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    private $finish;

    /**
     * @ORM\Column(type="integer")
     *  0.- Rechazado
     *  1.- Aceptado
     *  2.- Pendiente
     */
    private $accept;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $date;

    /**
     * Contructor de la clase
     */
    public function __construct($data){
        $this->userRequest = $data['userRequest'];
        $this->service = $data['service'];
        $this->finish = false;
        $this->accept = '2';
        $this->date = date("Y-m-d");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserRequest(): ?users
    {
        return $this->userRequest;
    }

    public function setUserRequest(?users $userRequest): self
    {
        $this->userRequest = $userRequest;

        return $this;
    }

    public function getService(): ?service
    {
        return $this->service;
    }

    public function setService(?service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getFinish(): ?bool
    {
        return $this->finish;
    }

    public function setFinish(bool $finish): self
    {
        $this->finish = $finish;

        return $this;
    }

    public function getAccept(): ?int
    {
        return $this->accept;
    }

    public function setAccept(int $accept): self
    {
        $this->accept = $accept;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function __toString(){
        return (String)$this->id;
    }
}
