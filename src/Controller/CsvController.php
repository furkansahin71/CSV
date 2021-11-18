<?php

namespace App\Controller;

use App\Entity\Import;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CsvController extends AbstractController
{
    /**
     * @Route("/", name="csv")
     */
    public function index(Request $request)
    {
        $personnes=$this->getDoctrine()->getRepository(Import::class)->findAll();

        return $this->render('csv/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }
}
