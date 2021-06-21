<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Entity\Section;
use App\Entity\Skills;
use App\Entity\Trainning;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class TestController extends AbstractController
{
    /**
     * @Route("/", name="test")
     */
    public function index(SectionRepository $rep,EntityManagerInterface $em): Response
    {
       // $all= $em->getRepository (Section::class)->findAll ();
       // $allExperiences= $em->getRepository (Experience::class)->findAll ();
       // $allTraining= $em->getRepository (Trainning::class)->findAll ();
        $allTraining= $em->getRepository (Skills::class)->findAll ();
        $context=[
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::ATTRIBUTES => ['id','Fields','status','description', 'Level' => ['id','description']]
        ];
        return $this->json ($allTraining,200,[],$context);
    }
}
