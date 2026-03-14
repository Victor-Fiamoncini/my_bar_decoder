<?php

namespace App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase;

use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input\Input;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Output\Output;

interface ExtractPaymentCodeUseCase
{
    public function execute(Input $input): Output;
}
