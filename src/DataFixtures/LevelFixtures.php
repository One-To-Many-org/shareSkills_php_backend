<?php

namespace App\DataFixtures;

use App\Entity\level;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LevelFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $levels=['Terminal','Terminal Scientifique','Terminal Littéraire','Terminal','Licence','Master','Doctorat'];

        foreach ($levels as $levelName){
            $level = new Level();
            $level->setDescription ($levelName);
            $level->setCreatedAt (new \DateTime());
            $level->setUpdatedAt  (new \DateTime());
            $manager->persist($level);
        }

        $manager->flush();
    }
}
