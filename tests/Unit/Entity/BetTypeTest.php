<?php

namespace App\Tests\Unit\Entity;

use App\Entity\BetType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BetTypeTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    /*// liste des catégories :
    Foot :
        résultat => result
        butteur (membre) => score
        nombre de but => goals Line
        score => points
        mi-temps la plus prolifique => most prolific half-time
    Handball :
        résultat => result
        butteur (membre) => (player) ToScore
        nombre de but => goals Line
        mi-temps la plus prolifique => most prolific half-time
    Formule 1 :
        résultat => result
        podium (top 3)
        points (top10)
        termine la course => finish the race
    Tennis :
        résultat => result
        atteindre la finale => reach the final
        nombre de sets
    Tennis de table :
        résultat => result
        atteindre la finale => reach the final
        nombre de sets
    */

    //throw new \Exception($violations);
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidBetType(): BetType
    {
        $betType = new BetType();
        $betType
            ->setTarget('run');
        return $betType;
    }

    /**
     * @dataProvider targetCompatibleProvider
     */
    public function testTargetCompatible(string $target)
    {
        $betType = $this->createValidBetType();
        $betType->setTarget($target);
        $violations = $this->validator->validate($betType);
        $this->assertCount(0, $violations);
    }

    public function targetCompatibleProvider(): array
    {
        return [
            ["run"],
            ["competition"],
            ["team"],
            ["member"]
        ];
    }

    /**
     * @dataProvider targetUncompatibleProvider
     */
    public function testTargetUncompatible(string $target)
    {
        $betType = $this->createValidBetType();
        $betType->setTarget($target);
        $violations = $this->validator->validate($betType);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function targetUncompatibleProvider(): array
    {
        return [
            ["other"],
            ["event"],
            ["Team"],
            ["Member"],
            [""],
            ["  "]
        ];
    }
}
