<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\Persistence\ObjectManager;
use App\Form\Model\WalletRetireFundsFormModel;

final class WalletRetireFundsFormHandler
{
    private WalletRetireFundsFormModel $walletRetireFundsFormModel;

    public function __construct(WalletRetireFundsFormModel $walletRetireFundsFormModel)
    {
        $this->walletRetireFundsFormModel = $walletRetireFundsFormModel;
    }

    public function handleForm(
        User $user,
        Wallet $wallet,
        ObjectManager $entityManager
    ): void {
        $amount = $this->walletRetireFundsFormModel->getAmountRetire();
        if ($wallet->isValidSubtraction($amount) === true) {
            $wallet->subtractAmount($amount);
        }
        // Save modification
        $entityManager->flush();
    }
}
