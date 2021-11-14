<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\RecipeFood;
use App\Entity\Shopping;
use App\Form\AddFoodToRecipeType;
use App\Form\CategoryType;
use App\Form\RecipeType;
use App\Form\UpdateFoodInRecipeType;
use App\Form\UpdateRecipeType;
use App\Repository\CategoryRepository;
use App\Repository\FoodRepository;
use App\Repository\PlanningRepository;
use App\Repository\RecipeFoodRepository;
use App\Repository\RecipeRepository;
use App\Repository\ShoppingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(ShoppingRepository $shoppingRepository, RecipeFoodRepository $recipeFoodRepository, RecipeRepository $recipeRepository, PlanningRepository $planningRepository, CategoryRepository $categoryRepository, Request $request)
    { 
        $categories = $categoryRepository->findAll();
        $owners = $planningRepository->findAllOwners();

        $category = $request->get('category');
        $cookingType = $request->get('cookingType');
        $type = $request->get('type');
        $title = $request->get('title');

        if ($title != null) {
            $recipes = $recipeRepository->getRecipesWithTitle($title);
        }
        else {
            $recipes = $recipeRepository->getRecipesWithFilters($category, $cookingType, $type); 
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

            // add recipe to planning /////////////////////////////////////////////// script part 1

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

            // add recipe to shopping list /////////////////////////////////////////// script part 2

            $foods = [];
            $currentRecipeFoods = $recipeFoodRepository->findBy([
                'recipe' => $recipe
            ]);
            foreach ($currentRecipeFoods as $currentRecipeFood) {
                $foods[] = $currentRecipeFood;
            }

            // for each food in current recipe
            foreach ($foods as $food) {

                // get multiplier
                $defaultRecipePersons = $food->getPersons();
                $multiplier = $for / $defaultRecipePersons;

                $foodId = $food->food->getId();

                $shoppingFoodsId = [];
                $shoppingRows = $shoppingRepository->findAll();
                foreach ($shoppingRows as $row) {
                    $shoppingFoodsId[] = $row->getFood()->getId();
                }
                // stop doublon for addition
                if (in_array($foodId, $shoppingFoodsId)) {

                    $shoppingRow = $shoppingRepository->findOneByFoodAndUnit($foodId, $food->unit, $owner);
                    
                    // line found --> addition
                    if ($shoppingRow) {
                        $currentQuantity = $shoppingRow->getQuantity();
                        $shoppingRow->setQuantity($currentQuantity + $food->quantity * $multiplier);
                        $entityManager->persist($shoppingRow);
                        $entityManager->flush();
                    }
                    // different units --> new line
                    else {
                        $shopping = new Shopping();
                        $shopping->setSection($food->getSection());
                        $shopping->setFood($food->getFood());
                        $shopping->setQuantity($food->getQuantity() * $multiplier);
                        $shopping->setUnit($food->getUnit());
                        $shopping->setOwner($owner);

                        $entityManager->persist($shopping);
                        $entityManager->flush();
                    }
                }
                // no doublon -> new line
                else {
                    $shopping = new Shopping();
                    $shopping->setSection($food->getSection());
                    $shopping->setFood($food->getFood());
                    $shopping->setQuantity($food->getQuantity() * $multiplier);
                    $shopping->setUnit($food->getUnit());
                    $shopping->setOwner($owner);

                    $entityManager->persist($shopping);
                    $entityManager->flush();
                }
            }
        }

        return $this->render('recipe/recipes.html.twig', [
            'recipes' => $recipes,
            'categories' => $categories,
            'owners' => $owners
        ]);
    }

    /**
     * @Route("/add-recipe", name="add_recipe")
     */
    public function addRecipe(Request $request, FoodRepository $foodRepository)
    {
        $foods = $foodRepository->getSortedFoods();

        $recipe = new Recipe();
        
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);        

        if ($form->isSubmitted() && $form->isValid()) {

            $foodFieldsCount = (count($_POST) - 1) / 3;
            $entityManager = $this->getDoctrine()->getManager();

            for ($i = 1; $i <= $foodFieldsCount; $i++) {
                if (htmlspecialchars($_POST['quantity' . $i]) > 0) {
                    $recipeFood = new RecipeFood();
                    $recipeFood->setRecipe($recipe);

                    $food = $foodRepository->findOneByName(htmlspecialchars($_POST['food' . $i]));
                    $recipeFood->setFood($food);
                    $recipeFood->setQuantity(htmlspecialchars($_POST['quantity' . $i]));
                    $recipeFood->setUnit(htmlspecialchars($_POST['unit' . $i]));
                    $recipeFood->setSection($food->getSection());
                    $recipeFood->setFoodName($food->name);
                    $recipeFood->setPersons($recipe->getPersons());

                    $entityManager->persist($recipeFood);
                }
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
    public function showRecipeDetails(int $id, RecipeRepository $recipeRepository, RecipeFoodRepository $recipeFoodRepository, request $request)
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

            return $this->redirectToRoute('recipe_details', [
                'id' => $id
            ]);
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
     * @Route("/add-category", name="add_category")
     */
    public function addCategory(Request $request)
    {   
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('add_recipe');
        }

        return $this->render('recipe/addCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
