<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TeamTest extends KernelTestCase
{
    private function initializeTeam(): Team
    {
        $team =  new Team();
        $team->setName("RC Strasbourg Alsace");
        $team->setCountry("FR");
        return $team;
    }

    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    /**
     * @dataProvider validTeamProvider
     */
    public function testIfTeamIsValid(string $t): void
    {
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $team->setName($t);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertCount(0, $violations);
    }

    public function validTeamProvider(): array
    {
        return [
            ["Paris Saint-Germain Football Club"],
            ["Västerås Hockey"],
            ["Fenerbahçe 1907"],
            ["Székesfehérvár Futball"],
            ["Cartagena 1444"],
            ["A.E.K"],
            ["OLA"]
        ];
    }

    /**
     * @dataProvider invalidTeamProvider
     */
    public function testIfTeamIsInvalid(string $t): void
    {
        $kernel = $this->initializeKernel();
        $status = $this->initializeTeam();
        $status->setName($t);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($status);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidTeamProvider(): array
    {
        return [
            ["W@tkins Glen"],
            ["/\/ercedes"],
            ["Mon€yTeam"],
            ["_"],
            ["..."],
            ["---"],
            ["'''"],
            ["nom   d'equipe"],
            ["A"],
            [""]
        ];
    }

    public function testIfCountryIsValid(): void
    {
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider invalidCountryProvider
     */
    public function testIfCountryIsInvalid(string $c): void
    {
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $team->setCountry($c);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidCountryProvider(): array
    {
        return [
            ["La France, mais pas n'importe laquelle, celle du général De Gaulle"],
            ["huit"],
            ["KZK"],
            ["Almagne"]
        ];
    }
}
