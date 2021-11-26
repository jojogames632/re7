<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\FoodType;
use App\Form\UpdateFoodType;
use App\Repository\FoodRepository;
use App\Repository\RecipeFoodRepository;
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
            // replace space by _
            $foodName = ucfirst($form['name']->getData());
            $noSpaceFoodName = str_replace(' ', '_', $foodName);
            $food->setName($noSpaceFoodName);
            
            if (!$foodRepository->findOneByName($noSpaceFoodName)) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($food);
                $entityManager->flush();

                $this->addFlash('success', 'Aliment ajouté avec succès');

                return $this->redirectToRoute('foods');
            }
            else {
                $this->addFlash('danger', 'Cet aliment a déja été créé'); 
            }
        }

        return $this->render('food/index.html.twig', [
            'foods' => $foods,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete-food/{id<\d+>}", name="delete_food")
     */
    public function deleteFood(int $id, FoodRepository $foodRepository, RecipeFoodRepository $recipeFoodRepository)
    {  
        if (!$food = $foodRepository->find($id)) {
            throw $this->createNotFoundException('Cette aliment n\'a pas été trouvée');
        }

        if ($recipeFoodRepository->findBy(['food' => $food])) {
            $this->addFlash('danger', 'Cet aliment fait déja parti d\'une recette, vous ne pouvez pas le supprimer');
        }
        else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($food);
            $entityManager->flush();
        }

        return $this->redirectToRoute('foods');
    }

    /**
     * @Route("/update-food/{id<\d+>}", name="update_food")
     */
    public function updateFood(int $id, request $request, FoodRepository $foodRepository)
    {  
        if (!$food = $foodRepository->find($id)) {
            throw $this->createNotFoundException('Cette aliment n\'a pas été trouvée');
        }

        $form = $this->createForm(UpdateFoodType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($food);
            $entityManager->flush();
            
            return $this->redirectToRoute('foods');
        }

        return $this->render('food/updateFood.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
