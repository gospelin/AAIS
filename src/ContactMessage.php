<?php
namespace Auntyan1\Aais;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'contact_messages')]
class ContactMessage
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string')]
    private $name;

    #[ORM\Column(type: 'string')]
    private $email;

    #[ORM\Column(type: 'text')]
    private $message;

    #[ORM\Column(type: 'datetime')]
    private $submitted_at;

    public function getId() { return $this->id; }

    public function setName($name) { $this->name = $name; }
    public function getName() { return $this->name; }

    public function setEmail($email) { $this->email = $email; }
    public function getEmail() { return $this->email; }

    public function setMessage($message) { $this->message = $message; }
    public function getMessage() { return $this->message; }

    public function setSubmittedAt(\DateTime $submitted_at) { $this->submitted_at = $submitted_at; }
    public function getSubmittedAt() { return $this->submitted_at; }
}