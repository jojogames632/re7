<?php

namespace App\Entity;

use App\Repository\FoodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FoodRepository::class)
 */
class Food
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
     * @ORM\Column(type="string", length=255)
     */
    private $section;

    /**
     * @ORM\OneToMany(targetEntity=RecipeFood::class, mappedBy="foodId", orphanRemoval=true)
     */
    private $recipeFoods;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->recipeFoods = new ArrayCollection();
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

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(string $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return Collection|RecipeFood[]
     */
    public function getRecipeFoods(): Collection
    {
        return $this->recipeFoods;
    }

    public function addRecipeFood(RecipeFood $recipeFood): self
    {
        if (!$this->recipeFoods->contains($recipeFood)) {
            $this->recipeFoods[] = $recipeFood;
            $recipeFood->setFood($this);
        }

        return $this;
    }

    public function removeRecipeFood(RecipeFood $recipeFood): self
    {
        if ($this->recipeFoods->removeElement($recipeFood)) {
            // set the owning side to null (unless already changed)
            if ($recipeFood->getFood() === $this) {
                $recipeFood->setFood(null);
            }
        }

        return $this;
    }
}
