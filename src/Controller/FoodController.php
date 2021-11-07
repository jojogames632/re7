<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\FoodType;
use App\Repository\FoodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FoodController extends AbstractController
{
    /**
     * @Route("/foods", name="foods")
     */
    public function index(FoodRepository $foodRepository, Request $request): Response
    {
        $foods = $foodRepository->getSortedFoods();

        $food = new Food();

        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($food);
            $entityManager->flush();

            $this->addFlash('success', 'Aliment ajouté avec succès');
        }

        return $this->render('food/index.html.twig', [
            'foods' => $foods,
            'form' => $form->createView()
        ]);
    }
}
