<?php

namespace App\Tests;

use App\Entity\Positive;
use PHPUnit\Framework\TestCase;

class PositiveTest extends TestCase
{

    public function testIsPositive()
    {
        $nombrePositif = new Positive();
        $nombrePositif->setNumber(8);
        $this->assertGreaterThan(0, $nombrePositif->getNumber());
    }
}
