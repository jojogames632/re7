<?php

namespace App\Entity;

use App\Repository\PlanningRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanningRepository::class)
 */
class Planning
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="middayPlannings")
     */
    private $middayRecipe;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="eveningPlannings")
     */
    private $eveningRecipe;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $middayPersons;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $eveningPersons;

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

    public function getMiddayRecipe(): ?Recipe
    {
        return $this->middayRecipe;
    }

    public function setMiddayRecipe(?Recipe $middayRecipe): self
    {
        $this->middayRecipe = $middayRecipe;

        return $this;
    }

    public function getEveningRecipe(): ?Recipe
    {
        return $this->eveningRecipe;
    }

    public function setEveningRecipe(?Recipe $eveningRecipe): self
    {
        $this->eveningRecipe = $eveningRecipe;

        return $this;
    }

    public function getMiddayPersons(): ?int
    {
        return $this->middayPersons;
    }

    public function setMiddayPersons(?int $middayPersons): self
    {
        $this->middayPersons = $middayPersons;

        return $this;
    }

    public function getEveningPersons(): ?int
    {
        return $this->eveningPersons;
    }

    public function setEveningPersons(?int $eveningPersons): self
    {
        $this->eveningPersons = $eveningPersons;

        return $this;
    }
}
