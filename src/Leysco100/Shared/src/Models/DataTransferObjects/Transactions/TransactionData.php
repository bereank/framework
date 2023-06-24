<?php

namespace App\Models\DataTransferObjects\Transactions;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use App\Models\DataTransferObjects\Transactions\TransactionRowData;

class TransactionData extends Data
{
    public function __construct(

        public int $ObjType,
        public string $DocType,
        public int $DocNum,
        public int $Series,
        public string $CardCode,
        public string $Requester,
        public string $ReqName,
        public int $ReqType,
        public string $Department,
        public ?string $CardName,
        public int $SlpCode,
        public int $OwnerCode,
        public string $NumAtCard,
        public string $CurSource,
        public float $DocTotal,
        public float $VatSum,
        #[Date]
        public ?CarbonImmutable $DocDate,
        #[Date]
        public  ?CarbonImmutable $TaxDate,
        #[Date]
        public  ?CarbonImmutable $DocDueDate,
        #[Date]
        public ?CarbonImmutable  $ReqDate,
        public ?int $CntctCode,
        public ?int $AgrNo,
        public ?string $LicTradNum,
        public ?int $BaseEntry,
        public ?int $BaseType,
        public string $Ref2,
        public ?int $GroupNum,
        public ?float $DiscPrcnt,
        public ?float $DiscSum,
        public ?string $BPLId,
        public ?string $Comments,
        public ?string $NumAtCard2,
        public ?string $JrnlMemo,
        public ?string $UseShpdGd,
        public ?float $Rounding,
        public ?float $RoundDif,

        public string $U_CashMail,
        public string $U_CashName,
        public string $U_CashNo,
        public string $U_IDNo,
        public string $U_SalePipe,
        public string $U_ServiceCall,
        public string $U_DemoLocation,
        public string $U_Technician,
        public string $U_Location,
        public string $U_MpesaRefNo,
        public string $U_PCash,
        public string $U_transferType,
        public string $U_SSerialNo,
        public string $U_TypePur,
        public string $U_NegativeMargin,
        public string $U_BaseDoc,
        public string $U_SaleType,
        public string $ExtRef,
        public string $ExtRefDocNum,
        public string $ExtDocTotal,

        #[DataCollectionOf(TransactionRowData::class)]
        public DataCollection $rows,
        #[DataCollectionOf(OutSourceItemData::class)]
        public ?DataCollection $outRows,
    ) {
    }


    public static function rules(): array
    {
        return [
            // 'objType' => ['required', 'int'],
            // 'seriesId' => ['required', 'int'],
            // 'cardCode' => ['required', 'int'],
            // 'docDate' => ['required', 'string'],
        ];
    }
}
