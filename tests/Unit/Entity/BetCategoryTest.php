<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\BetCategory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \BetCategory
 */
final class BetCategoryTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidBetCategory(): BetCategory
    {
        $betCategory = new BetCategory();
        $betCategory
            ->setName("resultw")
            ->setAllowDraw(false)
            ->setTarget("teams");
        return $betCategory;
    }

    /**
     * @dataProvider validBetCategoryProvider
     */
    public function testIfBetCategoryIsCorrect(string $name): void
    {
        $betCategory = $this->createValidBetCategory();
        $betCategory->setName($name);
        $violations = $this->validator->validate($betCategory);
        $this->assertCount(0, $violations);
    }

    public function validBetCategoryProvider(): array
    {
        return [
            ["resultw"],
            ["result-and-points"],
            ["result_points"]
        ];
    }

    /**
     * @dataProvider invalidBetCategoryProvider
     */
    public function testIfBetCategoryIsIncorrect(string $name): void
    {
        $betCategory = $this->createValidBetCategory();
        $betCategory->setName($name);
        $violations = $this->validator->validate($betCategory);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidBetCategoryProvider(): array
    {
        return [
            ["result player"],
            ["resultbenched_"],
            ["-result"],
            ["poïnts"],
            [''],
            ['  ']
        ];
    }

    public function testTargetCompatible(): void
    {
        $target1 = 'teams';
        $target2 = 'members';
        $betCategory = $this->createValidBetCategory();
        $betCategory->setTarget($target1);
        $violations = $this->validator->validate($betCategory);
        $this->assertCount(0, $violations);
        $betCategory->setTarget($target2);
        $violations = $this->validator->validate($betCategory);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider targetUncompatibleProvider
     */
    public function testTargetUncompatible(string $target): void
    {
        $betCategory = $this->createValidBetCategory();
        $betCategory->setTarget('teams');
        $betCategory->setTarget($target);
        $this->assertSame('teams', $betCategory->getTarget());
        $violations = $this->validator->validate($betCategory);
        $this->assertCount(0, $violations);
    }

    public function targetUncompatibleProvider(): array
    {
        return [
            [' '],
            [''],
            ['hibou'],
            ["Teams"],
            ["Members"]
        ];
    }
}
