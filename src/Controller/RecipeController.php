<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeFood;
use App\Form\AddFoodToRecipeType;
use App\Form\FullRecipeType;
use App\Form\UpdateFoodInRecipeType;
use App\Form\UpdateRecipeType;
use App\Repository\CategoryRepository;
use App\Repository\CookingTypeRepository;
use App\Repository\FoodRepository;
use App\Repository\PlanningRepository;
use App\Repository\RecipeFoodRepository;
use App\Repository\RecipeRepository;
use App\Repository\ShoppingRepository;
use App\Repository\RecipeTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(RecipeFoodRepository $recipeFoodRepository, FoodRepository $foodRepository, CookingTypeRepository $cookingTypeRepository, RecipeTypeRepository $recipeTypeRepository, RecipeRepository $recipeRepository, PlanningRepository $planningRepository, CategoryRepository $categoryRepository, Request $request)
    { 
        $categories = $categoryRepository->findAll();
        $cookingTypes = $cookingTypeRepository->findAll();
        $types = $recipeTypeRepository->findAll();
        $owners = $planningRepository->findAllOwners();
        $foods = $foodRepository->findAll();

        $category = $request->get('category');
        $cookingType = $request->get('cookingType');
        $type = $request->get('type');
        $title = $request->get('title');
        $foodId = $request->get('foodId');

        if ($foodId) {
            $recipeFoodRows = $recipeFoodRepository->findByFood($foodId);
            $recipesId = [];
            foreach ($recipeFoodRows as $row) {
                $recipesId[] = $row->getRecipe()->getId();
            }
            $distinctRecipesId = array_unique($recipesId);

            $recipes = [];
            foreach ($distinctRecipesId as $id) {
                $recipes[] = $recipeRepository->find($id);
            }
        }
        else {
            if ($title) {
                $recipes = $recipeRepository->getRecipesWithTitle($title);
            }
            else {
                $recipes = $recipeRepository->getRecipesWithFilters($category, $cookingType, $type); 
            }
        }

        if ($request->get('ajax')) {
            return new JsonResponse([
                "content" => $this->renderView('recipe/_recipesContent.html.twig', [
                    'recipes' => $recipes,
                ])
            ]);
        }
        $recipes = $recipeRepository->findAllAsc();

        if (isset($_POST['owner']) && isset($_POST['day']) && isset($_POST['when']) && isset($_POST['for'])) {
            $owner = htmlspecialchars($_POST['owner']);
            $day = htmlspecialchars($_POST['day']);
            $when = htmlspecialchars($_POST['when']);
            $for = htmlspecialchars(intval($_POST['for']));
            $recipeName = htmlspecialchars($_POST['recipeName']);

            // add recipe to planning
            $recipe = $recipeRepository->findOneByName($recipeName);
            $planning = $planningRepository->findOneByNameAndOwner($day, $owner);
            if ($when === 'midi') {
                $planning->setMiddayRecipe($recipe);
                $planning->setMiddayPersons($for);
            }
            else {
                $planning->setEveningRecipe($recipe);
                $planning->setEveningPersons($for);
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planning);
            $entityManager->flush();
        }

        return $this->render('recipe/recipes.html.twig', [
            'recipes' => $recipes,
            'categories' => $categories,
            'cookingTypes' => $cookingTypes,
            'types' => $types,
            'owners' => $owners,
            'foods' => $foods
        ]);
    }

    /**
     * @Route("/add-recipe", name="add_recipe")
     */
    public function addRecipe(Request $request)
    {
        $newRecipe = new Recipe();
        $form = $this->createForm(FullRecipeType::class, $newRecipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newRecipe->setName(ucfirst($form['name']->getData()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newRecipe);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('recipe_details', [ 'id' => $newRecipe->getId()]));
        }

        return $this->render('recipe/addRecipe.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/recipe-details/{id<\d+>}", name="recipe_details")
     */
    public function showRecipeDetails(int $id = 1, RecipeRepository $recipeRepository, RecipeFoodRepository $recipeFoodRepository, request $request)
    {
        if (!$recipe = $recipeRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('La recette avec l\'id %s n\'existe pas', $id));
        }

        $recipeFood = new RecipeFood();
        $form = $this->createForm(AddFoodToRecipeType::class, $recipeFood);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeFood->setRecipe($recipe);
            $food = $form['food']->getData();
            $recipeFood->setFoodName($food->getName());
            $recipeFood->setPersons($recipe->getPersons());
            $recipeFood->setSection($food->getSection());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recipeFood);
            $entityManager->flush();

            return $this->redirectToRoute('recipe_details', [ 'id' => $id ]);
        }

        $foods = $recipeFoodRepository->findBy(['recipe' => $recipe]);

        return $this->render('recipe/recipeDetails.html.twig', [
            'recipe' => $recipe,
            'foods' => $foods,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update-food-in-recipe/{id<\d+>}/{recipeId<\d+>}", name="update_food_in_recipe")
     */
    public function updateFoodInRecipe(int $id, int $recipeId, RecipeRepository $recipeRepository, RecipeFoodRepository $recipeFoodRepository, Request $request)
    {
        if (!$recipeFood = $recipeFoodRepository->find($id)) {
            throw $this->createNotFoundException('L\'aliment à mettre à jour n\'a pas été trouvé');
        }

        if (!$recipe = $recipeRepository->find($recipeId)) {
            throw $this->createNotFoundException('La recette concernant l\'aliment à modifier n\'a pas été trouvée');
        } 

        $form = $this->createForm(UpdateFoodInRecipeType::class, $recipeFood);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('recipe_details', [
                'id' => $recipeId
            ]);
        }

        return $this->render('recipe/updateFood.html.twig', [
            'food' => $recipeFood,
            'recipe' => $recipe,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete-food-in-recipe/{id<\d+>}/{recipeId<\d+>}", name="delete_food_in_recipe")
     */
    public function deleteFoodInRecipe(int $id, int $recipeId, RecipeFoodRepository $recipeFoodRepository, RecipeRepository $recipeRepository)
    {
        if (!$food = $recipeFoodRepository->find($id)) {
            throw $this->createNotFoundException('Cet aliment n\'a pas été trouvé');
        }

        if (!$recipe = $recipeRepository->find($recipeId)) {
            throw $this->createNotFoundException('La recette concernant l\'aliment à supprimer n\'a pas été trouvée');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($food);
        $entityManager->flush();

        return $this->redirectToRoute('recipe_details', [
            'id' => $recipeId
        ]);
    }

    /**
     * @Route("/update-recipe/{id<\d+>}", name="update_recipe")
     */
    public function updateRecipe(int $id, RecipeRepository $recipeRepository, Request $request)
    {
        if (!$recipe = $recipeRepository->find($id)) {
            throw $this->createNotFoundException('Cette recette n\'a pas été trouvée');
        }

        $form = $this->createForm(UpdateRecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('recipe_details', [
                'id' => $id
            ]);
        }

        return $this->render('recipe/updateRecipe.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe
        ]);
    }

    /**
     * @Route("/delete-recipe/{id<\d+>}", name="delete_recipe")
     */
    public function deleteRecipe(int $id, RecipeFoodRepository $recipeFoodRepository, ShoppingRepository $shoppingRepository, RecipeRepository $recipeRepository, PlanningRepository $planningRepository)
    {
        if (!$recipe = $recipeRepository->find($id)) {
            throw $this->createNotFoundException('Cette recette n\'a pas été trouvée');
        }
        
        $entityManager = $this->getDoctrine()->getManager();

        // midday
        $planningRows = $planningRepository->findBy(['middayRecipe' => $recipe]);
        if ($planningRows) {
            foreach ($planningRows as $row) {
                // clean planning
                $row->setMiddayRecipe(null);
                $row->setMiddayPersons(null);
                $entityManager->persist($row);           
            }
        }

        // evening
        $planningRows = $planningRepository->findBy(['eveningRecipe' => $recipe]);
        if ($planningRows) {
            foreach ($planningRows as $row) {
                // clean planning
                $row->setEveningRecipe(null);
                $row->setEveningPersons(null);
                $entityManager->persist($row);
            }
        }

        // delete food's recipe
        $recipeFoods = $recipeFoodRepository->findBy(['recipe' => $recipe]);
        foreach ($recipeFoods as $recipeFood) {
            $entityManager->remove($recipeFood);
        }
        
        $entityManager->remove($recipe);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
