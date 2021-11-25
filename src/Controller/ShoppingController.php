<?php

namespace App\Controller;

use App\Entity\Bonus;
use App\Entity\Shopping;
use App\Form\BonusType;
use App\Form\UpdateBonusType;
use App\Repository\BonusRepository;
use App\Repository\FoodRepository;
use App\Repository\PlanningRepository;
use App\Repository\RecipeFoodRepository;
use App\Repository\ShoppingRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingController extends AbstractController
{
    /**
     * @Route("/shopping/{planningOwner}", name="shopping")
     */
    public function index(Request $request, BonusRepository $bonusRepository, ShoppingRepository $shoppingRepository, PlanningRepository $planningRepository, RecipeFoodRepository $recipeFoodRepository, FoodRepository $foodRepository, string $planningOwner = 'Christophe'): Response
    { 
        $owners = $planningRepository->findAllOwners();
        $entityManager = $this->getDoctrine()->getManager();

        // clean shopping list
        $allShoppingRows = $shoppingRepository->findAll();
        foreach ($allShoppingRows as $shoppingRow) {
            $entityManager->remove($shoppingRow);
        }
        $entityManager->flush();

        foreach ($owners as $owner) {
            $planningRows = $planningRepository->findByOwner($owner['owner']);
            $recipesArray = [];
            $personsArray = [];

            foreach ($planningRows as $planningRow) {
                if ($planningRow->getMiddayRecipe()) {
                    $recipesArray[] = $planningRow->getMiddayRecipe();
                    $personsArray[] = $planningRow->getMiddayPersons();
                }
                if ($planningRow->getEveningRecipe()) {
                    $recipesArray[] = $planningRow->getEveningRecipe();
                    $personsArray[] = $planningRow->getEveningPersons();
                }
            }

            $index = 0;
            foreach ($recipesArray as $currentRecipe) {

                $currentRecipeFoods = $recipeFoodRepository->findBy(['recipe' => $currentRecipe]);

                // for each food in current recipe
                foreach ($currentRecipeFoods as $food) {

                    // get multiplier
                    $defaultRecipePersons = $food->getPersons();
                    $for = $personsArray[$index];
                    $multiplier = $for / $defaultRecipePersons;

                    $foodId = $food->food->getId();

                    $shoppingFoodsId = [];
                    $shoppingRows = $shoppingRepository->findAll();
                    foreach ($shoppingRows as $row) {
                        $shoppingFoodsId[] = $row->getFood()->getId();
                    }
                    // stop doublon for addition
                    if (in_array($foodId, $shoppingFoodsId)) {

                        $shoppingRow = $shoppingRepository->findOneByFoodUnitAndOwner($foodId, $food->getUnit(), $owner['owner']);
                        
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
                            $shopping->setOwner($owner['owner']);

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
                        $shopping->setOwner($owner['owner']);

                        $entityManager->persist($shopping);
                        $entityManager->flush();
                    }
                }
                $index++;
            }

            $allBonus = $bonusRepository->findByOwner($owner['owner']);

            // Add bonus in shopping table
            foreach ($allBonus as $bonusRow) {
                $shoppingFoodsId = [];
                $shoppingRows = $shoppingRepository->findAll();
                foreach ($shoppingRows as $row) {
                    $shoppingFoodsId[] = $row->getFood()->getId();
                }
                if (in_array($bonusRow->getFood()->getId(), $shoppingFoodsId)) {
                    $shoppingRow = $shoppingRepository->findOneByFoodUnitAndOwner($bonusRow->getFood(), $bonusRow->getUnit(), $owner['owner']);
                    // line found --> addition
                    if ($shoppingRow) {
                        $shoppingRow->setQuantity($shoppingRow->getQuantity() + $bonusRow->getQuantity());
                        $entityManager->persist($shoppingRow);
                        $entityManager->flush();
                    }
                    else {
                        // differents units -> new line
                        $shopping = new Shopping();
                        $shopping->setSection($bonusRow->getSection());
                        $shopping->setFood($bonusRow->getFood());
                        $shopping->setQuantity($bonusRow->getQuantity());
                        $shopping->setUnit($bonusRow->getUnit());
                        $shopping->setOwner($bonusRow->getOwner());

                        $entityManager->persist($shopping);
                        $entityManager->flush();
                    }
                }
                else {
                    // no doublon -> new line
                    $shopping = new Shopping();
                    $shopping->setSection($bonusRow->getSection());
                    $shopping->setFood($bonusRow->getFood());
                    $shopping->setQuantity($bonusRow->getQuantity());
                    $shopping->setUnit($bonusRow->getUnit());
                    $shopping->setOwner($bonusRow->getOwner());

                    $entityManager->persist($shopping);
                    $entityManager->flush();
                }
            }
        }

        $shoppingRows = $shoppingRepository->findAllFilteredBySection($planningOwner);
        $allBonus = $bonusRepository->findByOwner($planningOwner);

        $bonus = new Bonus();
        $form = $this->createForm(BonusType::class, $bonus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $foodId = $bonus->getFood()->getId();
            $bonusFoodsId = [];
            $bonusRows = $bonusRepository->findByOwner($planningOwner);
            foreach ($bonusRows as $row) {
                $bonusFoodsId[] = $row->getFood()->getId();
            }
            if (in_array($foodId, $bonusFoodsId)) {
                $bonusRow = $bonusRepository->findOneByFoodUnitAndOwner($bonus->getFood(), $bonus->getUnit(), $planningOwner);
                // line found --> addition            
                if ($bonusRow) {
                    $bonusRow->setQuantity($bonusRow->getQuantity() + $bonus->getQuantity());
                    $entityManager->persist($bonusRow);
                    $entityManager->flush();
                }
                else {
                    // differents units --> new line
                    $bonus->setOwner($planningOwner);
                    $section = $foodRepository->find($bonus->getFood())->getSection();
                    $bonus->setSection($section);
                    $entityManager->persist($bonus);
                    $entityManager->flush(); 
                }                
            }
            else {
                // no doublon --> new line
                $bonus->setOwner($planningOwner);
                $section = $foodRepository->find($bonus->getFood())->getSection();
                $bonus->setSection($section);
                $entityManager->persist($bonus);
                $entityManager->flush();
            }

            return $this->redirect($this->generateUrl('shopping', [
                'planningOwner' => $planningOwner
            ]));
        }

        return $this->render('shopping/index.html.twig', [
            'shoppingRows' => $shoppingRows,
            'owners' => $owners,
            'planningOwner' => $planningOwner,
            'allBonus' => $allBonus,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/generate-pdf/{planningOwner}", name="generate_pdf")
     */
    public function generatePdf(string $planningOwner, ShoppingRepository $shoppingRepository, PlanningRepository $planningRepository): Response
    {
        $shoppingRows = $shoppingRepository->findAllFilteredBySection($planningOwner);
        $owners = $planningRepository->findAllOwners();

        $html = $this->render('shopping/pdf.html.twig', [
            'shoppingRows' => $shoppingRows,
            'owners' => $owners,
            'planningOwner' => $planningOwner
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Palatino');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html->getContent());
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Liste-de-courses.pdf', [
            'Attachment' => true
        ]);
        
        return new Response();
    }

    /**
     * @Route("/update-bonus/{id<\d+>}", name="update_bonus")
     */
    public function updateBonus(int $id, Request $request, BonusRepository $bonusRepository)
    {
        if (!$bonus = $bonusRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le supplément avec l\'id %s n\'a pas été trouvé', $id));
        }

        $form = $this->createForm(UpdateBonusType::class, $bonus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bonus);
            $entityManager->flush();
            
            return $this->redirect($this->generateUrl('shopping', [
                'planningOwner' => $bonus->getOwner()
            ]));
        }

        return $this->render('shopping/UpdateBonus.html.twig', [
            'form' => $form->createView(),
            'bonus' => $bonus
        ]);
    }

    /**
     * @Route("/delete-bonus/{id<\d+>}", name="delete_bonus")
     */
    public function deleteBonus(int $id, BonusRepository $bonusRepository) 
    {
        if (!$bonus = $bonusRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le supplément avec l\'id %s n\'a pas été trouvé', $id));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($bonus);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('shopping', [
            'planningOwner' => $bonus->getOwner()
        ]));
    }

    /**
     * @Route("/clean-bonus/{bonusOwner}", name="clean_bonus")
     */
    public function cleanBonus(string $bonusOwner, BonusRepository $bonusRepository) 
    {
        if (!$bonusRows = $bonusRepository->findByOwner($bonusOwner)) {
            throw $this->createNotFoundException(sprintf('Le possésseur %s n\'a pas de supplément ou n\'existe pas', $bonusOwner));
        }

        foreach ($bonusRows as $row) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($row);
        }

        $entityManager->flush();

        return $this->redirect($this->generateUrl('shopping', [
            'planningOwner' => $bonusOwner
        ]));
    }
}
