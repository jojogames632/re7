<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 */
class Recipe
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
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="integer")
     */
    private $persons;

    /**
     * @ORM\OneToMany(targetEntity=Planning::class, mappedBy="middayRecipe")
     */
    private $middayPlannings;
    
    /**
     * @ORM\OneToMany(targetEntity=Planning::class, mappedBy="eveningRecipe")
     */
    private $eveningPlannings;

    /**
     * @ORM\OneToMany(targetEntity=RecipeFood::class, mappedBy="recipe", orphanRemoval=true)
     */
    private $recipeFoods;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=CookingType::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cookingType;

    public function __construct()
    {
        $this->middayPlannings = new ArrayCollection();
        $this->eveningPlannings = new ArrayCollection();
        $this->foodId = new ArrayCollection();
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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

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

    /**
     * @return Collection|Planning[]
     */
    public function getMiddayPlannings(): Collection
    {
        return $this->middayPlannings;
    }

    public function addMiddayPlanning(Planning $middayPlanning): self
    {
        if (!$this->middayPlannings->contains($middayPlanning)) {
            $this->middayPlannings[] = $middayPlanning;
            $middayPlanning->setMiddayRecipe($this);
        }

        return $this;
    }

    public function removeMiddayPlanning(Planning $middayPlanning): self
    {
        if ($this->middayPlannings->removeElement($middayPlanning)) {
            // set the owning side to null (unless already changed)
            if ($middayPlanning->getMiddayRecipe() === $this) {
                $middayPlanning->setMiddayRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Planning[]
     */
    public function getEveningPlannings(): Collection
    {
        return $this->eveningPlannings;
    }

    public function addEveningPlanning(Planning $eveningPlanning): self
    {
        if (!$this->eveningPlannings->contains($eveningPlanning)) {
            $this->eveningPlannings[] = $eveningPlanning;
            $eveningPlanning->setEveningRecipe($this);
        }

        return $this;
    }

    public function removeEveningPlanning(Planning $eveningPlanning): self
    {
        if ($this->eveningPlannings->removeElement($eveningPlanning)) {
            // set the owning side to null (unless already changed)
            if ($eveningPlanning->getEveningRecipe() === $this) {
                $eveningPlanning->setEveningRecipe(null);
            }
        }

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
            $recipeFood->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeFood(RecipeFood $recipeFood): self
    {
        if ($this->recipeFoods->removeElement($recipeFood)) {
            // set the owning side to null (unless already changed)
            if ($recipeFood->getRecipe() === $this) {
                $recipeFood->setRecipe(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCookingType(): ?CookingType
    {
        return $this->cookingType;
    }

    public function setCookingType(?CookingType $cookingType): self
    {
        $this->cookingType = $cookingType;

        return $this;
    }
}
