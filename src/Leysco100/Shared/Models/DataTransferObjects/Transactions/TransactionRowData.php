<?php

namespace App\Models\DataTransferObjects\Transactions;


use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Date;


class TransactionRowData extends Data
{
    public function __construct(

        public string $OwnerCode,
        public int $LineNum,
        public ?int $TargetType,
        public ?int $TrgetEntry,
        public ?int $BaseType,
        public ?int $BaseRef,
        public ?int $BaseEntry,
        public ?int $BaseLine,
        public ?int $LineStatus,
        public ?string $ItemCode,
        public ?string $Dscription,
        public ?string $CodeBars,
        public ?float $VatPrcnt,
        public ?string $SerialNum,
        public ?float $Quantity,
        public ?float $DelivrdQty,
        public ?float $InvQty,
        public ?float $OpenInvQty,
        public ?float $PackQty,
        public ?float $Price,
        public ?float $DiscPrcnt,
        public ?float $Rate,
        public ?string $TaxCode,
        public ?float $PriceAfVAT,
        public ?float $PriceBefDi,
        public ?float $LineTotal,
        public ?string $WhsCode,
        #[Date]
        public ?CarbonImmutable $ShipDate,
        public ?int $SlpCode,
        public ?float $Commission,
        public ?string $AcctCode,
        public ?string $OcrCode,
        public ?string $OcrCode2,
        public ?string $OcrCode3,
        public ?string $OcrCode4,
        public ?string $OcrCode5,
        public ?float $OpenQty,
        public ?float $GrossBuyPr,
        public ?float $GPTtlBasPr,
        public ?float $VatSum,
        public ?float $GrssProfit,
        public ?string $UomCode,
        public ?string $unitMsr,
        public ?float $NumPerMsr,
        public ?string $Text,
        public ?float $GTotal,
        public ?string $CogsOcrCod,
        public ?string $CogsOcrCo2,
        public ?string $CogsOcrCo3,
        public ?string $CogsOcrCo4,
        public ?string $CogsOcrCo5,
        public ?string $BPLId,
        public ?string $NoInvtryMv,
        public ?string $U_Promotion,
        public ?string $U_StockWhse,
        public ?string $WhsName,
    
    ) {
    }
}
