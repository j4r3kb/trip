<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\DataFixtures;

use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubsistenceAllowanceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $manager->persist(SubsistenceAllowance::create('pl', 10, 'PLN'));
        $manager->persist(SubsistenceAllowance::create('de', 50, 'PLN'));
        $manager->persist(SubsistenceAllowance::create('gb', 75, 'PLN'));

        $manager->flush();
    }
}