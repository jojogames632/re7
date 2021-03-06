<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SectionRepository::class)
 * @UniqueEntity(
 *      fields={"name"},
 *      message="Ce rayon a déjà été créé"
 * )
 */
class Section
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
     * @ORM\OneToMany(targetEntity=Shopping::class, mappedBy="section")
     */
    private $shoppings;

    /**
     * @ORM\OneToMany(targetEntity=Food::class, mappedBy="section", orphanRemoval=true)
     */
    private $food;

    /**
     * @ORM\OneToMany(targetEntity=RecipeFood::class, mappedBy="section")
     */
    private $recipeFood;

    /**
     * @ORM\OneToMany(targetEntity=Bonus::class, mappedBy="section")
     */
    private $bonuses;

    public function __construct()
    {
        $this->shoppings = new ArrayCollection();
        $this->food = new ArrayCollection();
        $this->recipeFood = new ArrayCollection();
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
            $shopping->setSection($this);
        }

        return $this;
    }

    public function removeShopping(Shopping $shopping): self
    {
        if ($this->shoppings->removeElement($shopping)) {
            // set the owning side to null (unless already changed)
            if ($shopping->getSection() === $this) {
                $shopping->setSection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Food[]
     */
    public function getFood(): Collection
    {
        return $this->food;
    }

    public function addFood(Food $food): self
    {
        if (!$this->food->contains($food)) {
            $this->food[] = $food;
            $food->setSection($this);
        }

        return $this;
    }

    public function removeFood(Food $food): self
    {
        if ($this->food->removeElement($food)) {
            // set the owning side to null (unless already changed)
            if ($food->getSection() === $this) {
                $food->setSection(null);
            }
        }

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
            $recipeFood->setSection($this);
        }

        return $this;
    }

    public function removeRecipeFood(RecipeFood $recipeFood): self
    {
        if ($this->recipeFood->removeElement($recipeFood)) {
            // set the owning side to null (unless already changed)
            if ($recipeFood->getSection() === $this) {
                $recipeFood->setSection(null);
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
            $bonus->setSection($this);
        }

        return $this;
    }

    public function removeBonus(Bonus $bonus): self
    {
        if ($this->bonuses->removeElement($bonus)) {
            // set the owning side to null (unless already changed)
            if ($bonus->getSection() === $this) {
                $bonus->setSection(null);
            }
        }

        return $this;
    }
}
