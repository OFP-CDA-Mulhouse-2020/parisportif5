<?php
namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private function userInit(): User
    {
        return new User();
    }

    public function testIsUserExists(): void
    {
        $user = $this->userInit();
        $this->assertNotNull($user, "user is not null");
    }
    
    /**
     * @dataProvider civilityProvider
     */
    public function testCivilityUnexpectedValue($civ): void
    {
        $user = $this->userInit();
        $this->expectException(\InvalidArgumentException::class);
        $user->setCivility($civ);
    }

    public function civilityProvider(): array
    {
        return [
            ["x"],
            ["monsieur"],
            [0]
        ];
    }

    public function testEmailConformity(): void
    {
        $user = $this->userInit();
        $this->expectException(\InvalidArgumentException::class);
        $user->setEmailAddress("emailtest.com");
    }

}