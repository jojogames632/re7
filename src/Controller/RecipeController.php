<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeFood;
use App\Form\RecipeType;
use App\Repository\FoodRepository;
use App\Repository\PlanningRepository;
use App\Repository\RecipeFoodRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(RecipeRepository $recipeRepository, PlanningRepository $planningRepository): Response
    {
        $recipes = $recipeRepository->findAll();

        if (isset($_POST['day']) && isset($_POST['when'])) {
            $day = htmlspecialchars($_POST['day']);
            $when = htmlspecialchars($_POST['when']);
            $recipeName = htmlspecialchars($_POST['recipeName']);

            $recipe = $recipeRepository->findOneByName($recipeName);
            $planning = $planningRepository->findOneByName($day);
            if ($when === 'midi') {
                $planning->setMiddayRecipe($recipe);
            }
            else {
                $planning->setEveningRecipe($recipe);
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planning);
            $entityManager->flush();
        }

        return $this->render('recipe/recipes.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * @Route("/add-recipe", name="add_recipe")
     */
    public function addRecipe(Request $request, FoodRepository $foodRepository)
    {
        $foods = $foodRepository->findAll();

        $recipe = new Recipe();
        
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);        

        if ($form->isSubmitted() && $form->isValid()) {

            $foodFieldsCount = (count($_POST) - 1) / 3;
            $entityManager = $this->getDoctrine()->getManager();

            for ($i = 1; $i <= $foodFieldsCount; $i++) {
                $recipeFood = new RecipeFood();
                $recipeFood->setRecipe($recipe);

                $food = $foodRepository->findOneByName(htmlspecialchars($_POST['food' . $i]));
                $recipeFood->setFood($food);
                $recipeFood->setQuantity(htmlspecialchars($_POST['quantity' . $i]));
                $recipeFood->setUnit(htmlspecialchars($_POST['unit' . $i]));
                $recipeFood->setSection($food->section);
                $recipeFood->setFoodName($food->name);

                $entityManager->persist($recipeFood);
            }

            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'Recette ajoutée avec succès !');
        }

        return $this->render('recipe/addRecipe.html.twig', [
            'form' => $form->createView(),
            'foods' => $foods
        ]);
    }

    /**
     * @Route("/recipe-details/{id<\d+>}", name="recipe_details")
     */
    public function showRecipeDetails(int $id, RecipeRepository $recipeRepository, RecipeFoodRepository $recipeFoodRepository)
    {
        if (!$recipe = $recipeRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('La recette avec l\'id %s n\'existe pas', $id));
        }

        $foods = $recipeFoodRepository->findBy([
            'recipe' => $recipe
        ]);

        return $this->render('recipe/recipeDetails.html.twig', [
            'recipe' => $recipe,
            'foods' => $foods
        ]);
    }

    /**
     * @Route("/delete-food-in-recipe/{id<\d+>}/{recipeId<\d+>}", name="delete_food_in_recipe")
     */
    public function deleteFoodInRecipe(int $id, int $recipeId, RecipeFoodRepository $recipeFoodRepository)
    {
        if (!$food = $recipeFoodRepository->find($id)) {
            throw $this->createNotFoundException('Cet aliment n\'a pas été trouvé');
        } 

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($food);
        $entityManager->flush();

        return $this->redirectToRoute('recipe_details', [
            'id' => $recipeId
        ]);
    }
}
