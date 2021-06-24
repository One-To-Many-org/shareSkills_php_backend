<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Experience;
use App\Entity\Field;
use App\Entity\Level;
use App\Entity\OwnSkill;
use App\Entity\SearchedSkill;
use App\Entity\Training;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('FR_fr');
        /**
         * @var City[]
         */
       $cities=$manager->getRepository (City::class)->findAll ();

        /**
         * @var Field[] $fields
         */
        $fields=$manager->getRepository (Field::class)->findAll ();

        /**
         * @var Level[] $levels
         */
        $levels=$manager->getRepository (Level::class)->findAll ();

        for($i=0; $i<mt_rand (6,10); $i++){

            $user=new User();
            $user
                ->setEmail ($faker->email)
                ->setFirstName ($faker->firstName)
                ->setLastName ($faker->lastName)
                ->setBirthDate (new \DateTime($faker->date ('1970-01-01','2020-01-01')))
                ->setPassword ($faker->password (8,10))
                ->setGender (mt_rand (6,15)>9?'Mr':'Mme')
                ->setProfileDescription ($faker->paragraphs (mt_rand (3,7),true))
                ->setUpdatedAt (new \DateTime($faker->date ('2021-05-01','now')))
                ->setCreatedAt (new \DateTimeImmutable($faker->date ('2019-01-01','2021-05-01')))
                ->setCity ($cities[mt_rand (0,count($cities)-1)]->getFullName())
                ->setUserName ($faker->name.'-'.mt_rand (10,125))
                ->setPhone ($faker->phoneNumber)
                ->setAdresse ($faker->address)
                ->setRoles ([]);
            ;
          if($i<4){
              $user->setPassword ("toor");
              if($i<2){
                  $user->setRoles (['ROLE_ADMIN']);
              }

          }

            for($j=0; $j<mt_rand (4,8); $j++){
                $random=mt_rand (0,20);
                $training= $random>10 ?new Training(): new Experience();
                $training
                    ->setCity ($faker->city)
                    ->setCountry ($faker->country)
                    ->setDescription ($faker->paragraph (5,true))
                    ->setInstitution ($faker->company)
                    ->setQualification ($faker->jobTitle)
                    ->setAdresse ($faker->address)
                    ->setStartedDate (new \DateTime($faker->date ('2008-05-01','2020-12-31')))
                    ->setEndDate (new \DateTime($faker->date ('2018-01-01','now')))
                    ->setUpdatedAt (new \DateTime($faker->date ('2021-05-01','now')))
                    ->setCreatedAt (new \DateTimeImmutable($faker->date ('2019-01-01','2021-05-01')))

                ;
                $user=  $random>10? $user->addTrainings ($training):$user->addExperience ($training);
            }

            for($k=0; $k<mt_rand (4,8); $k++){
                $random=mt_rand (0,20);
                $skills=$random>10? new OwnSkill(): new SearchedSkill();
                $skills
                    ->setDescription ($faker->paragraphs (5,true))
                    ->setField ($fields[mt_rand (0,count($fields)-1)])
                    ->setLevel ($levels[mt_rand (0,count($levels)-1)])
                    ->setUpdatedAt (new \DateTime($faker->date ('2021-05-01','now')))
                    ->setCreatedAt (new \DateTimeImmutable($faker->date ('2019-01-01','2021-05-01')))
                ;
                $user= $random>10? $user->addOwnSkill ($skills):$user->addSearchedSkill ($skills);
            }
            $manager->persist($user);
        }
        $manager->flush();
    }
}
