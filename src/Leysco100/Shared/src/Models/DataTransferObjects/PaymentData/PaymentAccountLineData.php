<?php

namespace App\Models\DataTransferObjects\PaymentData;

use Spatie\LaravelData\Data;

class PaymentAccountLineData extends Data
{
    public function __construct(

        public int $o_a_c_t_s_id,
        public float $SumApplied,
    ) {
    }
}
