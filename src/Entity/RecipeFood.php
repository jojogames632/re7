<?php

namespace App\Entity;

use App\Repository\RecipeFoodRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecipeFoodRepository::class)
 */
class RecipeFood
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    public $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="recipeFoods")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    /**
     * @ORM\ManyToOne(targetEntity=Food::class, inversedBy="recipeFoods")
     * @ORM\JoinColumn(nullable=false)
     */
    public $food;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $foodName;

    /**
     * @ORM\Column(type="integer")
     */
    private $persons;

    /**
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="recipeFood")
     * @ORM\JoinColumn(nullable=false)
     */
    private $section;

    /**
     * @ORM\ManyToOne(targetEntity=Unit::class, inversedBy="recipeFood")
     */
    private $unit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getRecipe(): ?recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getFood(): ?Food
    {
        return $this->food;
    }

    public function setFood(?Food $food): self
    {
        $this->food = $food;

        return $this;
    }

    public function getFoodName(): ?string
    {
        return $this->foodName;
    }

    public function setFoodName(string $foodName): self
    {
        $this->foodName = $foodName;

        return $this;
    }

    public function getPersons(): ?int
    {
        return $this->persons;
    }

    public function setPersons(int $persons): self
    {
        $this->persons = $persons;

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
}
