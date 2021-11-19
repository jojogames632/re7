<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParameterController extends AbstractController
{
    /**
     * @Route("/parameters", name="parameters")
     */
    public function index(): Response
    {
        return $this->render('parameter/index.html.twig', [
           
        ]);
    }
}
