<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Score;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ScoreTest extends KernelTestCase
{
    private function initializeScore(): Score
    {
        $score =  new Score();
        $score->setRunType("race");
        $score->setValue(3);
        return $score;
    }

    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    public function testIfRunTypeIsCorrect(): void
    {
        $kernel = $this->initializeKernel();
        $score = $this->initializeScore();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($score);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider invalidRunTypeProvider
     */
    public function testIfRunTypeIsInCorrect(string $rT): void
    {
        $kernel = $this->initializeKernel();
        $score = $this->initializeScore();
        $score->setRunType($rT);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($score);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidRunTypeProvider(): array
    {
        return [
            ["Match"],
            ["rencontre"],
            ["trophy"],
            ["stage"]
        ];
    }

    public function testIfScoreIsNotNegative(): void
    {
        $kernel = $this->initializeKernel();
        $score = $this->initializeScore();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($score);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider invalidScoreProvider
     */
    public function testIfScoreIsInCorrect(int $s): void
    {
        $kernel = $this->initializeKernel();
        $score = $this->initializeScore();
        $score->setRunType($s);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($score);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidScoreProvider(): array
    {
        return [
            //[3.8],
            [-1],
            [-23561961256156]
        ];
    }
}
