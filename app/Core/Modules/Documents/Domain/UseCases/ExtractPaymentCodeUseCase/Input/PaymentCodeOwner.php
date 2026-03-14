<?php

namespace App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input;

readonly class PaymentCodeOwner
{
    public function __construct(public int $id) {}
}
