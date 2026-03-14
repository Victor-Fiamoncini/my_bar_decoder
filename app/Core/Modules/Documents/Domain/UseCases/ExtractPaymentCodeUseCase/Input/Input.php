<?php

namespace App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input;

readonly class Input
{
    public function __construct(public File $file, public PaymentCodeOwner $paymentCodeOwner) {}
}
