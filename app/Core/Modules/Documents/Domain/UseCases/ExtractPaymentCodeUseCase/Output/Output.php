<?php

namespace App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Output;

use App\Core\Modules\Documents\Domain\Entities\Document\Document;

readonly class Output
{
    public function __construct(public Document $document) {}
}
