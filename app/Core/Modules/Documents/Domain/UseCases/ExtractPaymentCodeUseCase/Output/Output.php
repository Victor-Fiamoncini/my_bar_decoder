<?php

namespace App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Output;

use App\Core\Modules\Documents\Domain\Entities\PaymentCode\PaymentCode;

readonly class Output
{
    public function __construct(public PaymentCode $paymentCode) {}
}
