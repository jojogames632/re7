<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SectionRepository::class)
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
     * @ORM\Column(type="string", length=255)
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

    public function __construct()
    {
        $this->shoppings = new ArrayCollection();
        $this->food = new ArrayCollection();
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
}
