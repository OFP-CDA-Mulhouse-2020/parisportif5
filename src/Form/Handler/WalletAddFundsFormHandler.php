<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\Persistence\ObjectManager;
use App\Form\Model\WalletAddFundsFormModel;

final class WalletAddFundsFormHandler
{
    private WalletAddFundsFormModel $walletAddFundsFormModel;

    public function __construct(WalletAddFundsFormModel $walletAddFundsFormModel)
    {
        $this->walletAddFundsFormModel = $walletAddFundsFormModel;
    }

    public function handleForm(
        User $user,
        Wallet $wallet,
        ObjectManager $entityManager
    ): void {
        $amount = $this->walletAddFundsFormModel->getAmountAdd();
        if ($wallet->isValidAddition($amount) === true) {
            $wallet->addAmount($amount);
        }
        // Save modification
        $entityManager->flush();
    }
}
