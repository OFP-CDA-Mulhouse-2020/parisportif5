<?php

namespace App\Tests;

use App\Entity\Positive;
use PHPUnit\Framework\TestCase;


class PositiveTest extends TestCase
{

    public function testIsPositive()
    {
        $nombrePositif = new Positive();
        $this->assertIsInt($nombrePositif->getNumber());
    }

}
