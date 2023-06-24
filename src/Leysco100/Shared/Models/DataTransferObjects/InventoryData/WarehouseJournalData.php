<?php

namespace App\Models\DataTransferObjects\InventoryData;

use Spatie\LaravelData\Data;

class WarehouseJournalData extends Data
{
    public function __construct(
        public $transType,
        public $createdBy,
        public $baseRef,
        public $docDate,
        public $docLineNum,
        public $itemCode,
        public $whsCode,
        public $inQty,
        public $outQty,
        public $userSign,
        public $userId,
    ) {
    }


    public static function rules(): array
    {
        return [
            'transType' => ['required', 'int'],
            'createdBy' => ['required', 'int'],
            'baseRef' => ['required', 'int'],
            'docDate' => ['required', 'string'],
            'docLineNum' => ['required', 'int'],
            'itemCode' => ['required', 'int'],
            'whsCode' => ['required', 'int'],
            'inQty' => ['required', 'numeric'],
            'outQty' => ['required', 'numeric'],
            'userSign' => ['required', 'int'],
            'userId' => ['required', 'int'],
        ];
    }
}
