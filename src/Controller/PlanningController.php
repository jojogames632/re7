<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Form\PlanningType;
use App\Repository\PlanningRepository;
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
    public function index(PlanningRepository $planningRepository, Request $request, string $planningOwner = 'Christophe')
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

        // clean planning
        if (isset($_POST['cleanPlanningOf'])) {
            $owner = htmlspecialchars($_POST['cleanPlanningOf']);
            $planningRows = $planningRepository->findByOwner($owner);
            $entityManager = $this->getDoctrine()->getManager();

            foreach ($planningRows as $planningRow) {
                $planningRow->setMiddayRecipe(null);
                $planningRow->setMiddayPersons(null);
                $planningRow->setEveningPersons(null);
                $planningRow->setEveningRecipe(null);
                $entityManager->persist($planningRow);
            }

            $entityManager->flush();
        }

        // delete planning
        if (isset($_POST['deletePlanningOf'])) {
            $owner = htmlspecialchars($_POST['deletePlanningOf']);
            $planningRows = $planningRepository->findBy(['owner' => $owner]);

            // planning
            foreach ($planningRows as $planningRow) {
                $entityManager->remove($planningRow);
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
     * @Route("/delete-recipe/{day}/{when}/{owner}", name="delete_recipe_in_planning")
     */
    public function deleteRecipe(string $day, string $when, string $owner, PlanningRepository $planningRepository)
    {
        // delete in planning
        $planningDay = $planningRepository->findOneByNameAndOwner($day, $owner);

        if ($when === 'midi') {
            $planningDay->setMiddayRecipe(null);
            $planningDay->setMiddayPersons(null);
        }
        else {
            $planningDay->setEveningRecipe(null);
            $planningDay->setEveningPersons(null);
        } 

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($planningDay);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('planning', [
            'planningOwner' => $owner
        ]));
    }
}
