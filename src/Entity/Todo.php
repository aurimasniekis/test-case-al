<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Todo
 *
 * @package App\Entity
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 *
 * @ORM\Table(name="todo")
 * @ORM\Entity(repositoryClass="App\Repository\TodoRepository")
 */
class Todo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="todos")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=32)
     */
    private $title;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Todo
     */
    public function setUser(User $user): Todo
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Todo
     */
    public function setTitle(string $title): Todo
    {
        $this->title = $title;

        return $this;
    }
}