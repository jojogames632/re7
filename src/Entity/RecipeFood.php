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
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $unit;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="recipeFoods")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Recipe;

    /**
     * @ORM\ManyToOne(targetEntity=Food::class, inversedBy="recipeFoods")
     * @ORM\JoinColumn(nullable=false)
     */
    private $food;

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

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->Recipe;
    }

    public function setRecipe(?Recipe $Recipe): self
    {
        $this->Recipe = $Recipe;

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
}
