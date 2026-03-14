<?php

namespace App\Core\Modules\Documents\Domain\Entities\Document;

use App\Core\Modules\Documents\Domain\Entities\PaymentCode\PaymentCode;

readonly class Document
{
    public function __construct(
        public string $name,
        public PaymentCode $paymentCode,
        public \DateTimeImmutable $createdAt,
        public int $ownerId,
    ) {}
}
