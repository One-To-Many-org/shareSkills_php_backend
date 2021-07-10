<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $cities=['Parakou','Lomé','Cotonou','Porto-Novo','Ouidah','Dakar','Lagos','Abomey','Aneho','Ibadan','Thies','Enugu','Kasamance','Port-Hancourt','Aflao','Bobo Dioulasso','Lomé','Ouagadougou','Accra','Abidjan','Gagnoa','Noe'];
       // $countries=['BENIN','TOGO','GHANA','BURKINA-FASO','NIGER','COTE-D\'IVOIRE'];
        $countries=['BJ','TG','GH','BF','NE','NG','CI','SN'];
        $countriesObj=[];

        foreach ($countries as $countrieName){
            $country=new Country();
            $country->setName ($countrieName);
            $countriesObj[]=$country;
            $manager->persist($country);
        }

        foreach ($cities as $cityName){
            $city = new City();
            $city->setName  ($cityName);
            $city->setCountry ($countriesObj[mt_rand (0,count ($countriesObj)-1)]);
            $manager->persist($city);
        }

        $manager->flush();
    }
}
