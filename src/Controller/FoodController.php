<?php

namespace App\Controller;

use App\Entity\Food;
use App\Entity\Section;
use App\Form\FoodType;
use App\Form\SectionType;
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

            // replace space by _
            $foodName = ucfirst($form['name']->getData());
            $noSpaceFoodName = str_replace(' ', '_', $foodName);
            $food->setName($noSpaceFoodName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($food);
            $entityManager->flush();

            $this->addFlash('success', 'Aliment ajouté avec succès');

            return $this->redirectToRoute('foods');
        }

        return $this->render('food/index.html.twig', [
            'foods' => $foods,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/add-section", name="add_section")
     */
    public function addSection(Request $request): Response
    {
        $section = new Section();

        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $section->setName(ucfirst($form['name']->getData()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($section);
            $entityManager->flush();

            return $this->redirectToRoute('foods');
        }

        return $this->render('food/addSection.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
