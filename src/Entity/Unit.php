<?php

namespace App\Entity;

use App\Repository\UnitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UnitRepository::class)
 * @UniqueEntity(
 *      fields={"name"},
 *      message="Cet unité a déjà été créée" 
 * )
 */
class Unit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=RecipeFood::class, mappedBy="unit")
     */
    private $recipeFood;

    /**
     * @ORM\OneToMany(targetEntity=Shopping::class, mappedBy="unit")
     */
    private $shoppings;

    /**
     * @ORM\OneToMany(targetEntity=Bonus::class, mappedBy="unit")
     */
    private $bonuses;

    public function __construct()
    {
        $this->recipeFood = new ArrayCollection();
        $this->shoppings = new ArrayCollection();
        $this->bonuses = new ArrayCollection();
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

    /**
     * @return Collection|RecipeFood[]
     */
    public function getRecipeFood(): Collection
    {
        return $this->recipeFood;
    }

    public function addRecipeFood(RecipeFood $recipeFood): self
    {
        if (!$this->recipeFood->contains($recipeFood)) {
            $this->recipeFood[] = $recipeFood;
            $recipeFood->setUnit($this);
        }

        return $this;
    }

    public function removeRecipeFood(RecipeFood $recipeFood): self
    {
        if ($this->recipeFood->removeElement($recipeFood)) {
            // set the owning side to null (unless already changed)
            if ($recipeFood->getUnit() === $this) {
                $recipeFood->setUnit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Shopping[]
     */
    public function getShoppings(): Collection
    {
        return $this->shoppings;
    }

    public function addShopping(Shopping $shopping): self
    {
        if (!$this->shoppings->contains($shopping)) {
            $this->shoppings[] = $shopping;
            $shopping->setUnit($this);
        }

        return $this;
    }

    public function removeShopping(Shopping $shopping): self
    {
        if ($this->shoppings->removeElement($shopping)) {
            // set the owning side to null (unless already changed)
            if ($shopping->getUnit() === $this) {
                $shopping->setUnit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Bonus[]
     */
    public function getBonuses(): Collection
    {
        return $this->bonuses;
    }

    public function addBonus(Bonus $bonus): self
    {
        if (!$this->bonuses->contains($bonus)) {
            $this->bonuses[] = $bonus;
            $bonus->setUnit($this);
        }

        return $this;
    }

    public function removeBonus(Bonus $bonus): self
    {
        if ($this->bonuses->removeElement($bonus)) {
            // set the owning side to null (unless already changed)
            if ($bonus->getUnit() === $this) {
                $bonus->setUnit(null);
            }
        }

        return $this;
    }
}
