<?php

namespace App\Controller;

use App\Entity\Shopping;
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
    public function index(ShoppingRepository $shoppingRepository, PlanningRepository $planningRepository, RecipeFoodRepository $recipeFoodRepository, string $planningOwner = 'Christophe'): Response
    { 
        $owners = $planningRepository->findAllOwners();
        $entityManager = $this->getDoctrine()->getManager();

        // clean shopping list
        $allShoppingRows = $shoppingRepository->findAll();
        foreach ($allShoppingRows as $shoppingRow) {
            $entityManager->remove($shoppingRow);
        }
        $entityManager->flush();

        foreach ($owners as $owner) {
            $planningRows = $planningRepository->findByOwner($owner['owner']);
            $recipesArray = [];
            $personsArray = [];

            foreach ($planningRows as $planningRow) {
                if ($planningRow->getMiddayRecipe()) {
                    $recipesArray[] = $planningRow->getMiddayRecipe();
                    $personsArray[] = $planningRow->getMiddayPersons();
                }
                if ($planningRow->getEveningRecipe()) {
                    $recipesArray[] = $planningRow->getEveningRecipe();
                    $personsArray[] = $planningRow->getEveningPersons();
                }
            }

            $index = 0;
            foreach ($recipesArray as $currentRecipe) {

                $currentRecipeFoods = $recipeFoodRepository->findBy(['recipe' => $currentRecipe]);

                // for each food in current recipe
                foreach ($currentRecipeFoods as $food) {

                    // get multiplier
                    $defaultRecipePersons = $food->getPersons();
                    $for = $personsArray[$index];
                    $multiplier = $for / $defaultRecipePersons;

                    $foodId = $food->food->getId();

                    $shoppingFoodsId = [];
                    $shoppingRows = $shoppingRepository->findAll();
                    foreach ($shoppingRows as $row) {
                        $shoppingFoodsId[] = $row->getFood()->getId();
                    }
                    // stop doublon for addition
                    if (in_array($foodId, $shoppingFoodsId)) {

                        $shoppingRow = $shoppingRepository->findOneByFoodUnitAndOwner($foodId, $food->unit, $owner['owner']);
                        
                        // line found --> addition
                        if ($shoppingRow) {
                            $currentQuantity = $shoppingRow->getQuantity();
                            $shoppingRow->setQuantity($currentQuantity + $food->quantity * $multiplier);
                            $entityManager->persist($shoppingRow);
                            $entityManager->flush();
                        }
                        // different units --> new line
                        else {
                            $shopping = new Shopping();
                            $shopping->setSection($food->getSection());
                            $shopping->setFood($food->getFood());
                            $shopping->setQuantity($food->getQuantity() * $multiplier);
                            $shopping->setUnit($food->getUnit());
                            $shopping->setOwner($owner['owner']);

                            $entityManager->persist($shopping);
                            $entityManager->flush();
                        }
                    }
                    // no doublon -> new line
                    else {
                        $shopping = new Shopping();
                        $shopping->setSection($food->getSection());
                        $shopping->setFood($food->getFood());
                        $shopping->setQuantity($food->getQuantity() * $multiplier);
                        $shopping->setUnit($food->getUnit());
                        $shopping->setOwner($owner['owner']);

                        $entityManager->persist($shopping);
                        $entityManager->flush();
                    }
                }
                $index++;
            }
        }

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
    public function generatePdf(string $planningOwner, ShoppingRepository $shoppingRepository, PlanningRepository $planningRepository): Response
    {
        $shoppingRows = $shoppingRepository->findAllFilteredBySection($planningOwner);
        $owners = $planningRepository->findAllOwners();

        $html = $this->render('shopping/pdf.html.twig', [
            'shoppingRows' => $shoppingRows,
            'owners' => $owners,
            'planningOwner' => $planningOwner
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html->getContent());
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Liste-de-courses.pdf', [
            'Attachment' => true
        ]);
        
        return new Response();
    }
}
