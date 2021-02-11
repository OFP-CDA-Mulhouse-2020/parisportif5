<?php

namespace App\DataFixtures;

use App\Entity\Billing;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class BillingFixtures extends Fixture implements DependentFixtureInterface
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'order' => 12365563,
                'invoice' => 12365563,
                'amount' => 1000,
                'commission' => '0.075',
                'designation' => "Bet",
                'firstname' => "Tintin",
                'lastname' => "Dupont",
                'address' => "1 avenue Hergé",
                'city' => "COLMAR",
                'postcode' => "68000",
                'country' => "FR",
                'issue' => "2021-04-05",
                'delivery' => "2021-04-06",
                'operationType' => "debit",
                'user' => ''
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $billing = new Billing();
            $billingUser = null;
            if (!empty($testData[$i]['user'])) {
                $billingUser = $this->userRepository->findOneByEmail($testData[$i]['user']);
            }
            $billing
                ->setOrderNumber($testData[$i]['order'])
                ->setInvoiceNumber($testData[$i]['invoice'])
                ->setAmount($testData[$i]['amount'])
                ->setCommissionRate($testData[$i]['commission'])
                ->setDesignation($testData[$i]['designation'])
                ->setFirstName($testData[$i]['firstname'])
                ->setLastName($testData[$i]['lastname'])
                ->setAddress($testData[$i]['address'])
                ->setCity($testData[$i]['city'])
                ->setPostcode($testData[$i]['postcode'])
                ->setCountry($testData[$i]['country'])
                ->setIssueDate(new \DateTimeImmutable($testData[$i]['issue'], new \DateTimeZone("UTC")))
                ->setDeliveryDate(new \DateTimeImmutable($testData[$i]['delivery'], new \DateTimeZone("UTC")))
                ->setUser($billingUser)
                ->setOperationType($testData[$i]['operationType'])
                ;
            $manager->persist($billing);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
