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

    //throw new \Exception($violations);
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidBetCategory(): BetCategory
    {
        $betCategory = new BetCategory();
        $betCategory
            ->setTitle("result")
            ->setItems(["winner", "null"]);
        return $betCategory;
    }

    public function testTitleUncompatible()
    {
        $title1 = "";
        $title2 = "   ";
        $betCategory = $this->createValidBetCategory();
        $betCategory->setTitle($title1);
        $violations = $this->validator->validate($betCategory);
        $this->assertCount(1, $violations);
        $betCategory->setTitle($title2);
        $violations = $this->validator->validate($betCategory);
        $this->assertCount(1, $violations);
    }

    public function testTitleCompatible()
    {
        $title = "result";
        $betCategory = $this->createValidBetCategory();
        $betCategory->setTitle($title);
        $violations = $this->validator->validate($betCategory);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider itemsCompatibleProvider
     */
    public function testItemsCompatible(array $items)
    {
        $betCategory = $this->createValidBetCategory();
        $betCategory->setItems($items);
        $violations = $this->validator->validate($betCategory);
        $this->assertCount(0, $violations);
    }

    public function itemsCompatibleProvider(): array
    {
        return [
            [["winner", "null"]],
            [["toscore"]]
        ];
    }

    public function testItemsUncompatible()
    {
        $betCategory = $this->createValidBetCategory();
        $betCategory->setItems([]);
        $violations = $this->validator->validate($betCategory);
        $this->assertCount(1, $violations);
    }
}
