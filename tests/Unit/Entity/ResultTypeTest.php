<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ResultType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \ResultType
 */
final class ResultTypeTest extends KernelTestCase
{
    private function initializeResultType(): ResultType
    {
        $result =  new ResultType();
        $result->setName("loss");
        return $result;
    }

    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    /**
     * @dataProvider validResultTypeProvider
     */
    public function testIfResultTypeIsCorrect(string $rT): void
    {
        $kernel = $this->initializeKernel();
        $result = $this->initializeResultType();
        $result->setName($rT);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($result);
        $this->assertCount(0, $violations);
    }

    public function validResultTypeProvider(): array
    {
        return [
            ["loss"],
            ["win"],
            ["draw"],
            ["first"],
            ["second"],
            ["third"]
        ];
    }

    /**
     * @dataProvider invalidResultTypeProvider
     */
    public function testIfResultTypeIsInCorrect(string $rT): void
    {
        $kernel = $this->initializeKernel();
        $result = $this->initializeResultType();
        $result->setName($rT);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($result);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidResultTypeProvider(): array
    {
        return [
            ["jaj@"],
            ["winner"]
        ];
    }
}
