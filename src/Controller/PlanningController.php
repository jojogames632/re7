<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Form\PlanningType;
use App\Repository\PlanningRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    /**
     * @Route("/planning/{planningOwner}", name="planning")
     */
    public function index(PlanningRepository $planningRepository, Request $request, string $planningOwner = 'Christophe')
    {
        $owners = $planningRepository->findAllOwners();

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
        
        $days = $planningRepository->getPlanningOf($planningOwner);

        return $this->render('planning/index.html.twig', [
            'days' => $days,
            'owners' => $owners,
            'planningOwner' => $planningOwner
        ]);
    }

    /**
     * @Route("/delete-recipe/{day}/{when}/{owner}", name="delete_recipe_in_planning")
     */
    public function deleteRecipe(string $day, string $when, string $owner, PlanningRepository $planningRepository)
    {
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

    /**
     * @Route("create-planning", name="create_planning")
     */
    public function createPlanning(Request $request)
    {
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $planning = new Planning();
        $entityManager = $this->getDoctrine()->getManager();

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

        return $this->render('planning/createPlanning.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
