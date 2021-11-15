<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Form\PlanningType;
use App\Repository\PlanningRepository;
use App\Repository\RecipeFoodRepository;
use App\Repository\ShoppingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    /**
     * @Route("/planning/{planningOwner}", name="planning")
     */
    public function index(PlanningRepository $planningRepository, ShoppingRepository $shoppingRepository, Request $request, string $planningOwner = 'Christophe')
    {
        $owners = $planningRepository->findAllOwners();
        $entityManager = $this->getDoctrine()->getManager();

        if ($owner = $request->get('owner')) {
            $days = $planningRepository->getPlanningOf($owner);
        }

        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('planning/_planning.html.twig', [
                   'days' => $days,
                    'owners' => $owners 
                ])
            ]);
        }

        // create planning
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $planning = new Planning();

        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($days as $day) {
                $planning = new Planning();
                $planning->setName($day);
                $planning->setOwner($form['owner']->getData());
                
                $entityManager->persist($planning);
            }
            $entityManager->flush();

            return $this->redirectToRoute('planning');
        }

        // delete planning
        if (isset($_POST['deletePlanningOf'])) {
            $owner = htmlspecialchars($_POST['deletePlanningOf']);
            $planningRows = $planningRepository->findBy(['owner' => $owner]);
            $shoppingRows = $shoppingRepository->findBy(['owner' => $owner]);

            // planning
            foreach ($planningRows as $planningRow) {
                $entityManager->remove($planningRow);
            }
            // shopping
            foreach ($shoppingRows as $shoppingRow) {
                $entityManager->remove($shoppingRow);  
            }
            $entityManager->flush();

            return $this->redirect($this->generateUrl('planning', ['planningOwner' => $planningOwner]));
        }
        
        $days = $planningRepository->getPlanningOf($planningOwner);

        return $this->render('planning/index.html.twig', [
            'days' => $days,
            'owners' => $owners,
            'planningOwner' => $planningOwner,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete-recipe/{day}/{when}/{persons<\d+>}{owner}", name="delete_recipe_in_planning")
     */
    public function deleteRecipe(string $day, string $when, int $persons, string $owner, PlanningRepository $planningRepository, RecipeFoodRepository $recipeFoodRepository, ShoppingRepository $shoppingRepository)
    {
        $recipe = null;
        // delete in planning ////////////////////////////////////////////////////// phase 1

        $planningDay = $planningRepository->findOneByNameAndOwner($day, $owner);

        if ($when === 'midi') {
            $recipe = $planningDay->getMiddayRecipe();
            $planningDay->setMiddayRecipe(null);
            $planningDay->setMiddayPersons(null);
        }
        else {
            $recipe = $planningDay->getMiddayRecipe();
            $planningDay->setEveningRecipe(null);
            $planningDay->setEveningPersons(null);
        } 

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($planningDay);
        $entityManager->flush();

        // delete in shopping ////////////////////////////////////////////////////// phase 2

        $multiplier = $persons / $recipe->getPersons();

        // for each foods of the recipe
        $recipeFoods = $recipeFoodRepository->findBy(['recipe' => $recipe]);
        foreach ($recipeFoods as $recipeFood) {
            // get the line and substract
            $shoppingRow = $shoppingRepository->findOneByFoodUnitAndOwner($recipeFood->getFood(), $recipeFood->getUnit(), $owner);
            $shoppingRow->setQuantity($shoppingRow->getQuantity() - $recipeFood->getQuantity() * $multiplier);
            $entityManager->persist($shoppingRow);
            $entityManager->flush();
            // del if 0 quantity
            if ($shoppingRow->getQuantity() == 0) {
                $entityManager->remove($shoppingRow);
                $entityManager->flush();
            }
        }

        return $this->redirect($this->generateUrl('planning', [
            'planningOwner' => $owner
        ]));
    }
}
