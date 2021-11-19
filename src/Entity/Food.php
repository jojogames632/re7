<?php

namespace App\Entity;

use App\Repository\FoodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=FoodRepository::class)
 * @UniqueEntity(
 *      fields={"name"},
 *      message="Cet aliment a déjà été créé"
 * )
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    public $name;

    /**
     * @ORM\OneToMany(targetEntity=RecipeFood::class, mappedBy="food", orphanRemoval=true)
     */
    private $recipeFoods;

    /**
     * @ORM\OneToMany(targetEntity=Shopping::class, mappedBy="food", orphanRemoval=true)
     */
    private $shoppings;

    /**
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="food")
     * @ORM\JoinColumn(nullable=false)
     */
    private $section;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->recipeFoods = new ArrayCollection();
        $this->shoppings = new ArrayCollection();
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
            $shopping->setFood($this);
        }

        return $this;
    }

    public function removeShopping(Shopping $shopping): self
    {
        if ($this->shoppings->removeElement($shopping)) {
            // set the owning side to null (unless already changed)
            if ($shopping->getFood() === $this) {
                $shopping->setFood(null);
            }
        }

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
}
