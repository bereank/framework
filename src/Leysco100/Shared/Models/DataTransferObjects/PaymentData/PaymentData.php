<?php

namespace App\Models\DataTransferObjects\PaymentData;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class PaymentData extends Data
{
    public function __construct(

        public ?int $seriesId,
        public ?int $CardCode,
        public string $DocDate,
        public int $store_id,
        public float $DocTotal,
        public int $DocEntry, // invoice id
        public ?string $DebtPayment,
        public ?string $PaymentRemarks,
        public ?int $container_id,

        #[DataCollectionOf(PaymentAccountLineData::class)]
        public DataCollection $paymentAccountLines,
    ) {
    }
}
