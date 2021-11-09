<?php

namespace App\Controller;

use App\Repository\FoodRepository;
use App\Repository\PlanningRepository;
use App\Repository\RecipeFoodRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

class ShoppingController extends AbstractController
{
    /**
     * @Route("/shopping", name="shopping")
     */
    public function index(PlanningRepository $planningRepository, RecipeRepository $recipeRepository, RecipeFoodRepository $recipeFoodRepository, FoodRepository $foodRepository): Response
    { 
        $planning = $planningRepository->findAll();
        $planningRecipeIdArray = [];
        $planningRecipePersonsArray = [];

        $shoppingArray = [];

        // get planning recipe id's and persons
        foreach ($planning as $day) {
            if ($day->getMiddayRecipe() !== null) {
                $planningRecipeIdArray[] = $day->getMiddayRecipe();
                $planningRecipePersonsArray[] = $day->getMiddayPersons();
            }
            if ($day->getEveningRecipe() !== null) {
                $planningRecipeIdArray[] = $day->getEveningRecipe();
                $planningRecipePersonsArray[] = $day->getEveningPersons();
            }
        }

        $allFoodNames = [];

        $planningRecipeCount = 0;
        // for each recipe in planning
        foreach ($planningRecipeIdArray as $id) {
            $foods = [];
            $currentRecipeFoods = $recipeFoodRepository->findBy([
                'recipe' => $id
            ]);
            foreach ($currentRecipeFoods as $currentRecipeFood) {
                $foods[] = $currentRecipeFood;
            }
            // TEST
            $sections = [];
            $foodNames = [];
            $quantities = [];
            $units = [];

            foreach ($foods as $food) {
                // get multiplier
                $defaultRecipePersons = $food->getPersons();
                $currentRecipePersons = $planningRecipePersonsArray[$planningRecipeCount];
                $multiplier = $currentRecipePersons / $defaultRecipePersons;

                $foodName = $foodRepository->find($food->food)->name;
                // stop doublon for addition
                if (in_array($foodName, $allFoodNames)) {
                    $index = array_search($foodName, $allFoodNames);
                    // check unit
                    if ($food->unit === $shoppingArray[$index][3]) {
                        $shoppingArray[$index][2] += $food->quantity * $multiplier;
                    }
                    // different units
                    else {
                        $foodNames[] = $foodName;
                        $sections[] = $food->section;
                        $quantities[] = $food->quantity * $multiplier;
                        $units[] = $food->unit;
                    }
                }
                // no doublon -> add to arrays
                else {
                    $foodNames[] = $foodName;
                    $sections[] = $food->section;
                    $quantities[] = $food->quantity * $multiplier;
                    $units[] = $food->unit;
                    // Add to all food array
                    $allFoodNames[] = $foodName;
                }
            }

            // fill double array
            for ($i = 0; $i < count($foodNames); $i++) {
                $shoppingArray[] = [
                    $sections[$i],
                    $foodNames[$i],
                    $quantities[$i],
                    $units[$i]            
                ];
            }
            $planningRecipeCount++;
        }

        // sort double array
        for ($j = count($shoppingArray) - 2; $j >= 0; $j--) {
            for ($i = 0; $i <= $j; $i++) {
                if ($shoppingArray[$i][0] > $shoppingArray[$i+1][0]) {
                    $temp = $shoppingArray[$i][0];
                    $shoppingArray[$i][0] = $shoppingArray[$i+1][0];
                    $shoppingArray[$i+1][0] = $temp;
                }
            }
        }

        return $this->render('shopping/index.html.twig', [
            'shoppingArray' => $shoppingArray
        ]);
    }
}
