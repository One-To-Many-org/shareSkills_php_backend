<?php


namespace App\Controller;


use App\Entity\City;
use App\Entity\Country;
use App\Entity\Trainning;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class TestCityController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("/city", name="test_city")
     */
    public function index(CityRepository $rep, CountryRepository $cr): Response
    {
        /**
        $country= $cr->findOneBy (['id'=>1]);
        $city=new City();
        $city->setCountry ($country);
        $city->setName ("Cotonou");
        $city2=new City();
        $city2->setCountry ($country);
        $city2->setName ("Parakou");
        $this->getDoctrine ()->getManager ()->persist ($city2);
        $this->getDoctrine ()->getManager ()->persist ($city);
        $this->getDoctrine ()->getManager ()->flush ();
         *  $allTraining= $rep->findAll ();
         */
        $allTraining= $cr->findOneBy (['id'=>1]);
       // $allTraining->onSerialize ();
       //dd($allTraining);
        return $this->json ($allTraining,200,[], [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getName();
            },
        ]);
    }

}
