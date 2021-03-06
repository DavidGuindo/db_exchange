<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="messages")
     */
    private $userSend;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="messages")
     */
    private $userReciving;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bodyMessage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $checkRead;

    /**
     * Contructor de la clase
     */
    public function __construct($data){
        $this->userSend = $data['userSend'];
        $this->userReciving = $data['userReciving'];
        $this->date = date("Y-m-d");
        $this->bodyMessage = $data['bodyMessage'];
        $this->checkRead = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserSend(): ?users
    {
        return $this->userSend;
    }

    public function setUserSend(?users $userSend): self
    {
        $this->userSend = $userSend;

        return $this;
    }

    public function getUserReciving(): ?users
    {
        return $this->userReciving;
    }

    public function setUserReciving(?users $userReciving): self
    {
        $this->userReciving = $userReciving;

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

    public function getBodyMessage(): ?string
    {
        return $this->bodyMessage;
    }

    public function setBodyMessage(string $bodyMessage): self
    {
        $this->bodyMessage = $bodyMessage;

        return $this;
    }

    public function getCheckRead(): ?bool
    {
        return $this->checkRead;
    }

    public function setCheckRead(bool $checkRead): self
    {
        $this->checkRead = $checkRead;

        return $this;
    }

    public function __toString(){
        return (String)$this->id;
    }

}
