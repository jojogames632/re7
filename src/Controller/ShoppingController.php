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
        
        // get planning recipe id's
        foreach ($planning as $day) {
            if ($day->getMiddayRecipe() !== null) {
                $planningRecipeIdArray[] = $day->getMiddayRecipe();
            }
            if ($day->getEveningRecipe() !== null) {
                $planningRecipeIdArray[] = $day->getEveningRecipe();
            }
        }

        // get foods of alls recipes
        $foods = [];
        foreach ($planningRecipeIdArray as $id) {
            $currentRecipeFoods = $recipeFoodRepository->findBy([
                'recipe' => $id
            ]);
            foreach ($currentRecipeFoods as $currentRecipeFood) {
                $foods[] = $currentRecipeFood;
            }
        }

        $sections = [];
        $foodNames = [];
        $quantities = [];
        $units = [];

        // Fill previous arrays
        foreach ($foods as $food) {
            $foodName = $foodRepository->find($food->food)->name;
            // stop doublon for addition
            if (in_array($foodName, $foodNames)) {
                $index = array_search($foodName, $foodNames);
                // check unit
                if ($food->unit === $units[$index]) {
                    $quantities[$index] += $food->quantity;
                }
                // different units
                else {
                    $foodNames[] = $foodName;
                    $sections[] = $food->section;
                    $quantities[] = $food->quantity;
                    $units[] = $food->unit;
                }
            }
            // no doublon -> add to arrays
            else {
                $foodNames[] = $foodName;
                $sections[] = $food->section;
                $quantities[] = $food->quantity;
                $units[] = $food->unit;
            }
        }

        // create double array
        $shoppingArray = [];
        for ($i = 0; $i < count($foodNames); $i++) {
            $shoppingArray[] = [
                $sections[$i],
                $foodNames[$i],
                $quantities[$i],
                $units[$i]            
            ];
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
