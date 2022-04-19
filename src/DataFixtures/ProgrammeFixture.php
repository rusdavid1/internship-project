<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Programme;
use App\Validator\Date;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProgrammeFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $programme = new Programme();
        $programme->name = 'Fotbal';
        $programme->description = 'Play football';
        $programme->setStartDate(new \DateTime('now'));
        $programme->setEndDate(new \DateTime('+2 hours'));

        $manager->persist($programme);
        $manager->flush();
    }
}
