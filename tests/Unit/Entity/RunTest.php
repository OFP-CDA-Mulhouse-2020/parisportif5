<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Run;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Run
 */
final class RunTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    //throw new \Exception($violations);
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidRun(): Run
    {
        $run = new Run();
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $run
            ->setName('run name')
            ->setEvent('event name')
            ->setStartDate($date->setTime(23, 59, 59, 1000000));
        return $run;
    }

    private function createDefaultTimeZone(): \DateTimeZone
    {
        return new \DateTimeZone('UTC');
    }

    /**
     * @dataProvider namePropertyCompatibleProvider
     */
    public function testNamePropertyCompatible(string $runName)
    {
        $run = $this->createValidRun();
        $run->setName($runName);
        $violations = $this->validator->validate($run);
        $this->assertCount(0, $violations);
    }

    public function namePropertyCompatibleProvider(): array
    {
        return [
            ["Spécial n°1 des Vosges"],
            ["Match pool n°1 France-Espagne"]
        ];
    }

    public function testNamePropertyUncompatible()
    {
        $runName1 = '';
        $runName2 = '   ';
        $run = $this->createValidRun();
        $run->setName($runName1);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
        $run->setName($runName2);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
    }

    /**
     * @dataProvider eventPropertyCompatibleProvider
     */
    public function testEventPropertyCompatible(string $event)
    {
        $run = $this->createValidRun();
        $run->setEvent($event);
        $violations = $this->validator->validate($run);
        $this->assertCount(0, $violations);
    }

    public function eventPropertyCompatibleProvider(): array
    {
        return [
            ["Championnat des Vosges"],
            ["Matchs de pool n°1"]
        ];
    }

    public function testEventPropertyUncompatible()
    {
        $event1 = '';
        $event2 = '   ';
        $run = $this->createValidRun();
        $run->setEvent($event1);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
        $run->setEvent($event2);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
    }

    /**
     * @dataProvider startDateUnconformityProvider
     */
    public function testStartDateUnconformity(\DateTimeInterface $startDate): void
    {
        $run = $this->createValidRun();
        $run->setStartDate($startDate);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
    }

    public function startDateUnconformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $startDate = new \DateTimeImmutable('now', $timezone);
        return [
            [$startDate],
            [$startDate->modify('-1 hour')],
            [$startDate->modify('-1 day')->setTime(23, 59, 59, 999999)],
            [$startDate->modify('-1 year')]
        ];
    }

    /**
     * @dataProvider startDateConformityProvider
     */
    public function testStartDateConformity(\DateTimeInterface $startDate): void
    {
        $run = $this->createValidRun();
        $run->setStartDate($startDate);
        $violations = $this->validator->validate($run);
        $this->assertCount(0, $violations);
    }

    public function startDateConformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $startDate = new \DateTimeImmutable('now', $timezone);
        return [
            [$startDate->modify("+1 day")],
            [$startDate->modify("+1 month")]
        ];
    }

       /**
     * @dataProvider endDateUnconformityProvider
     */
    public function testEndDateUnconformity(\DateTimeInterface $endDate): void
    {
        $run = $this->createValidRun();
        $run->setEndDate($endDate);
        $violations = $this->validator->validate($run);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function endDateUnconformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $endDate = new \DateTimeImmutable('now', $timezone);
        return [
            [$endDate->setTime(23, 59, 59, 1000000)],
            [$endDate->modify('-1 hour')],
            [$endDate->modify('-1 day')->setTime(23, 59, 59, 999999)],
            [$endDate->modify('-1 year')]
        ];
    }

    /**
     * @dataProvider endDateConformityProvider
     */
    public function testEndDateConformity(\DateTimeInterface $endDate): void
    {
        $run = $this->createValidRun();
        $run->setEndDate($endDate);
        $violations = $this->validator->validate($run);
        $this->assertCount(0, $violations);
    }

    public function endDateConformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $endDate = new \DateTimeImmutable('now', $timezone);
        return [
            [$endDate->modify("+1 day")->setTime(23, 59, 59, 999999)],
            [$endDate->modify("+1 month")]
        ];
    }

    public function testMethodIsFinishReturnFalse()
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $run = $this->createValidRun();
        $exist = method_exists($run, 'isFinish');
        $this->assertTrue($exist);
        $run->setEndDate($date->modify('+2 day'));
        $result = $run->isFinish();
        $this->assertFalse($result);
    }

    public function testMethodIsOngoingReturnFalse()
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $run = $this->createValidRun();
        $exist = method_exists($run, 'isOngoing');
        $this->assertTrue($exist);
        $run->setEndDate($date->modify('+2 day'));
        $result = $run->isOngoing();
        $this->assertFalse($result);
    }
}
