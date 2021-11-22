<?php

namespace App\Controller;

use App\Form\CategoryType;
use App\Form\CookingTypeType;
use App\Form\SectionType;
use App\Form\UnitType;
use App\Entity\Category;
use App\Entity\CookingType;
use App\Entity\RecipeType;
use App\Entity\Unit;
use App\Entity\Section;
use App\Form\recipeTypeType;
use App\Form\UpdateCookingTypeType;
use App\Form\UpdateRecipeTypeType;
use App\Form\UpdateSectionType;
use App\Form\UpdateUnitType;
use App\Repository\CategoryRepository;
use App\Repository\CookingTypeRepository;
use App\Repository\FoodRepository;
use App\Repository\RecipeFoodRepository;
use App\Repository\RecipeRepository;
use App\Repository\RecipeTypeRepository;
use App\Repository\SectionRepository;
use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParameterController extends AbstractController
{
    /**
     * @Route("/parameters", name="parameters")
     */
    public function index(RecipeTypeRepository $recipeTypeRepository, CookingTypeRepository $cookingTypeRepository, CategoryRepository $categoryRepository, UnitRepository $unitRepository, SectionRepository $sectionRepository): Response
    {
        $types = $recipeTypeRepository->findAll();
        $cookingTypes = $cookingTypeRepository->findAll();
        $categories = $categoryRepository->findAll();
        $units = $unitRepository->findAll();
        $sections = $sectionRepository->findAll();

        return $this->render('parameter/index.html.twig', [
           'types' => $types,
           'cookingTypes' => $cookingTypes,
           'categories' => $categories,
           'units' => $units,
           'sections' => $sections
        ]);
    }
    /****************************************************************************************** ADD */
    /**
     * @Route("/add-category", name="add_category")
     */
    public function addCategory(Request $request)
    {   
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setName(ucfirst($form['name']->getData()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('parameters');
        }

        return $this->render('parameter/addCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/add-type", name="add_type")
     */
    public function addType(Request $request)
    {   
        $type = new RecipeType();
        $form = $this->createForm(recipeTypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type->setName(ucfirst($form['name']->getData()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($type);
            $entityManager->flush();

            return $this->redirectToRoute('parameters');
        }

        return $this->render('parameter/addType.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/add-cookingType", name="add_cookingType")
     */
    public function addCookingType(Request $request)
    {   
        $cookingType = new CookingType();
        $form = $this->createForm(CookingTypeType::class, $cookingType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cookingType->setName(ucfirst($form['name']->getData()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cookingType);
            $entityManager->flush();

            return $this->redirectToRoute('parameters');
        }

        return $this->render('parameter/addCookingType.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/add-unit", name="add_unit")
     */
    public function addUnit(Request $request)
    {   
        $unit = new Unit();
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unit->setName(ucfirst($form['name']->getData()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($unit);
            $entityManager->flush();

            return $this->redirectToRoute('parameters');
        }

        return $this->render('parameter/addUnit.html.twig', [
            'form' => $form->createView(),
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

            return $this->redirectToRoute('parameters');
        }

        return $this->render('parameter/addSection.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /************************************************************************************************* UPDATE */
    /**
     * @Route("/update-type/{id<\d+>}", name="update_type")
     */
    public function updateType(int $id, RecipeTypeRepository $recipeTypeRepository, Request $request): Response
    {
        if (!$type = $recipeTypeRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le type avec l\'id %s n\'existe pas', $id));
        }

        $form = $this->createForm(UpdateRecipeTypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($type);
            $entityManager->flush();

            return $this->redirectToRoute('parameters');
        }

        return $this->render('parameter/updateType.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update-cookingType/{id<\d+>}", name="update_cookingType")
     */
    public function updateCookingType(int $id, CookingTypeRepository $cookingTypeRepository, Request $request): Response
    {
        if (!$cookingType = $cookingTypeRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le type de cuisson avec l\'id %s n\'existe pas', $id));
        }

        $form = $this->createForm(UpdateCookingTypeType::class, $cookingType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cookingType);
            $entityManager->flush();

            return $this->redirectToRoute('parameters');
        }

        return $this->render('parameter/updateCookingType.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update-category/{id<\d+>}", name="update_category")
     */
    public function updateCategory(int $id, CategoryRepository $categoryRepository, Request $request): Response
    {
        if (!$category = $categoryRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('La catégorie avec l\'id %s n\'existe pas', $id));
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('parameters');
        }

        return $this->render('parameter/updateCategory.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update-unit/{id<\d+>}", name="update_unit")
     */
    public function updateUnit(int $id, UnitRepository $unitRepository, Request $request): Response
    {
        if (!$unit = $unitRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('L\'unité avec l\'id %s n\'existe pas', $id));
        }

        $form = $this->createForm(UpdateUnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($unit);
            $entityManager->flush();

            return $this->redirectToRoute('parameters');
        }

        return $this->render('parameter/updateUnit.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update-section/{id<\d+>}", name="update_section")
     */
    public function updateSection(int $id, SectionRepository $sectionRepository, Request $request): Response
    {
        if (!$section = $sectionRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le rayon avec l\'id %s n\'existe pas', $id));
        }

        $form = $this->createForm(UpdateSectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($section);
            $entityManager->flush();

            return $this->redirectToRoute('parameters');
        }

        return $this->render('parameter/updateSection.html.twig', [
           'form' => $form->createView()
        ]);
    }
    /************************************************************************************************* DELETE */
    /**
     * @Route("/delete-type/{id<\d+>}", name="delete_type")
     */
    public function deleteType(int $id, RecipeTypeRepository $recipeTypeRepository, RecipeRepository $recipeRepository): Response
    {
        if (!$type = $recipeTypeRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le type avec l\'id %s n\'existe pas', $id));
        }

        if ($recipeRepository->findByRecipeType($type)) {
            $this->addFlash('type', 'Ce type fait déja parti d\'une recette, vous ne pouvez pas le supprimer');
        }
        else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($type);
            $entityManager->flush();
        }

        return $this->redirectToRoute('parameters');
    }

    /**
     * @Route("/delete-cookingType/{id<\d+>}", name="delete_cookingType")
     */
    public function deleteCookingType(int $id, CookingTypeRepository $cookingTypeRepository, RecipeRepository $recipeRepository): Response
    {
        if (!$cookingType = $cookingTypeRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le type de cuisson avec l\'id %s n\'existe pas', $id));
        }

        if ($recipeRepository->findByCookingType($cookingType)) {
            $this->addFlash('cookingType', 'Ce type de cuisson fait déja parti d\'une recette, vous ne pouvez pas le supprimer');
        }
        else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cookingType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('parameters');
    }

    /**
     * @Route("/delete-category/{id<\d+>}", name="delete_category")
     */
    public function deleteCategory(int $id, CategoryRepository $categoryRepository, RecipeRepository $recipeRepository): Response
    {
        if (!$category = $categoryRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('La catégorie avec l\'id %s n\'existe pas', $id));
        }

        if ($recipeRepository->findByCategory($category)) {
            $this->addFlash('category', 'Cette catégorie fait déja parti d\'une recette, vous ne pouvez pas la supprimer');
        }
        else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('parameters');
    }

    /**
     * @Route("/delete-unit/{id<\d+>}", name="delete_unit")
     */
    public function deleteUnit(int $id, UnitRepository $unitRepository, RecipeFoodRepository $recipeFoodRepository): Response
    {
        if (!$unit = $unitRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('L\'unité avec l\'id %s n\'existe pas', $id));
        }

        if ($recipeFoodRepository->findByUnit($unit)) {
            $this->addFlash('unit', 'Cette unité fait déja parti d\'une recette, vous ne pouvez pas la supprimer');
        }
        else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($unit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('parameters');
    }

    /**
     * @Route("/delete-section/{id<\d+>}", name="delete_section")
     */
    public function deleteSection(int $id, sectionRepository $sectionRepository, FoodRepository $foodRepository): Response
    {
        if (!$section = $sectionRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le rayon avec l\'id %s n\'existe pas', $id));
        }

        if ($foodRepository->findBySection($section)) {
            $this->addFlash('section', 'Ce rayon fait déja parti d\'un aliment, vous ne pouvez pas le supprimer');
        }
        else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($section);
            $entityManager->flush();
        }

        return $this->redirectToRoute('parameters');
    }
}
