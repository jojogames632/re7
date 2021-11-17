<?php

namespace App\Controller;

use App\Entity\Shopping;
use App\Repository\FoodRepository;
use App\Repository\PlanningRepository;
use App\Repository\RecipeFoodRepository;
use App\Repository\ShoppingRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingController extends AbstractController
{
    /**
     * @Route("/shopping/{planningOwner}", name="shopping")
     */
    public function index(ShoppingRepository $shoppingRepository, PlanningRepository $planningRepository, RecipeFoodRepository $recipeFoodRepository, FoodRepository $foodRepository, string $planningOwner = 'Christophe'): Response
    { 
        $owners = $planningRepository->findAllOwners();
        $shoppingRows = $shoppingRepository->findAllFilteredBySection($planningOwner);

        return $this->render('shopping/index.html.twig', [
            'shoppingRows' => $shoppingRows,
            'owners' => $owners,
            'planningOwner' => $planningOwner
        ]);
    }

    /**
     * @Route("/generate-pdf/{planningOwner}", name="generate_pdf")
     */
    public function generatePdf(string $planningOwner, ShoppingRepository $shoppingRepository, PlanningRepository $planningRepository, RecipeFoodRepository $recipeFoodRepository, FoodRepository $foodRepository)
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
        $fichier = 'Liste-de-courses.pdf';
        $dompdf->stream($fichier);
    }
}
