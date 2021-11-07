<?php

namespace App\Controller;

use App\Repository\PlanningRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    /**
     * @Route("/planning", name="planning")
     */
    public function index(PlanningRepository $planningRepository): Response
    {
        $days = $planningRepository->findAll();

        return $this->render('planning/index.html.twig', [
            'days' => $days
        ]);
    }

    /**
     * @Route("/delete-recipe/{day}/{when}", name="delete_recipe_in_planning")
     */
    public function deleteRecipe(string $day, string $when, PlanningRepository $planningRepository)
    {
        $planningDay = $planningRepository->findOneByName($day);

        if ($when === 'midi') {
            $planningDay->setMiddayRecipe(null);
        }
        else {
            $planningDay->setEveningRecipe(null);
        } 

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($planningDay);
        $entityManager->flush();

        return $this->redirectToRoute('planning');
    }
}
