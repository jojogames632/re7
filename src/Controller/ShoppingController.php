<?php

namespace App\Controller;

use App\Entity\Shopping;
use App\Repository\FoodRepository;
use App\Repository\PlanningRepository;
use App\Repository\RecipeFoodRepository;
use App\Repository\ShoppingRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingController extends AbstractController
{
    /**
     * @Route("/shopping/{planningOwner}", name="shopping")
     */
    public function index(ShoppingRepository $shoppingRepository, PlanningRepository $planningRepository, RecipeFoodRepository $recipeFoodRepository, FoodRepository $foodRepository, string $planningOwner = 'Christophe'): Response
    { 
        $owners = $planningRepository->findAllOwners();

        $shoppingRows = $shoppingRepository->findAllFilteredBySection($planningOwner);

        return $this->render('shopping/index.html.twig', [
            'shoppingRows' => $shoppingRows,
            'owners' => $owners,
            'planningOwner' => $planningOwner
        ]);
    }

    /**
     * @Route("/generate-pdf/{planningOwner}", name="generate_pdf")
     */
    public function generatePdf(string $planningOwner, PlanningRepository $planningRepository, RecipeFoodRepository $recipeFoodRepository, FoodRepository $foodRepository)
    {
        $planning = $planningRepository->getPlanningOf($planningOwner);
        $planningRecipeIdArray = [];
        $planningRecipePersonsArray = [];
        $owners = $planningRepository->findAllOwners();

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

        $html = $this->render('shopping/pdf.html.twig', [
            'shoppingArray' => $shoppingArray,
            'owners' => $owners,
            'planningOwner' => $planningOwner
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Palatino');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html->getContent());
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $fichier = 'Liste-de-courses.pdf';
        $dompdf->stream($fichier);
    }
}
