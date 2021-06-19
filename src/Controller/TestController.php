<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Entity\Section;
use App\Entity\Trainning;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/", name="test")
     */
    public function index(SectionRepository $rep,EntityManagerInterface $em): Response
    {
       // $all= $em->getRepository (Section::class)->findAll ();
       // $allExperiences= $em->getRepository (Experience::class)->findAll ();
        $allTraining= $em->getRepository (Trainning::class)->findAll ();

        return $this->json ($allTraining);
    }
}
