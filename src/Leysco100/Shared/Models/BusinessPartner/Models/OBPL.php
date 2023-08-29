<?php

namespace Leysco100\Shared\Models\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OLCT;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OBPL extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_b_p_l_s';

    protected $appends = array('branch_address');
    public function location()
    {
        return $this->belongsTo(OLCT::class, 'LocationCode');
    }

    public function getBranchAddressAttribute()
    {
        if ($this->BPLId == "Nairobi trading") {
            return [
                "reportFullName" => "NAIROBI BRANCH",
                "addressDetails" => "P.O BOX 20001",
                "address2" => "00200 NAIROBI",
                "address3" => "KENYA",
                "phoneNumber" => "Tel: +254(20) 6943000/100",
                "Mobile" => "Mobile: +254 722 209872/3/6",
            ];
        }

        if ($this->BPLId == "Kubota") {
            return [
                "reportFullName" => "KUBOTA DEPARTMENT",
                "addressDetails" => "P.O BOX 32425888",
                "phoneNumber" => "2545324324",
                "address2" => "00200 NAIROBI",
                "address3" => "KENYA",
                "phoneNumber" => "Tel: +254(20) 6943000/100",
                "Mobile" => "Mobile: +254 722 209872/3/6",
            ];
        }

        if ($this->BPLId == "Doosan") {
            return [
                "reportFullName" => "DOOSAN DEPARTMENT",
                "addressDetails" => "P.O BOX 32425888",
                "address2" => "00200 NAIROBI",
                "address3" => "KENYA",
                "phoneNumber" => "2545324324",
                "Mobile" => "Mobile: +254 722 209872/3/6",

            ];
        }
        if ($this->BPLId == "IR") {
            return [
                "reportFullName" => "IR DEPARTMENT",
                "addressDetails" => "P.O BOX 32425888",
                "address2" => "00200 NAIROBI",
                "address3" => "KENYA",
                "phoneNumber" => "2545324324",
                "Mobile" => "Mobile: +254 722 209872/3/6",

            ];
        }
        if ($this->BPLId == "Garmins") {
            return [
                "reportFullName" => "GARMINS DEPARTMENT",
                "addressDetails" => "P.O BOX 32425888",
                "address2" => "00200 NAIROBI",
                "address3" => "KENYA",
                "phoneNumber" => "2545324324",
                "Mobile" => "Mobile: +254 722 209872/3/6",

            ];
        }

        if ($this->BPLId == "Voi") {
            return [

                "addressDetails" => "P.O BOX 32425888",
                "phoneNumber" => "2545324324",

            ];
        }
        if ($this->BPLId == "Malindi") {
            return [
                "addressDetails" => "P.O BOX 32425888",
                "phoneNumber" => "2545324324",

            ];
        }
        if ($this->BPLId == "Malindi") {
            return [
                "addressDetails" => "P.O BOX 32425888",
                "phoneNumber" => "2545324324",

            ];
        }
        if ($this->BPLId == "Kisumu") {
            return [
                "addressDetails" => "P.O BOX 32425888",
                "phoneNumber" => "2545324324",

            ];
        }

        if ($this->BPLId == "Kitengela") {
            return [
                "reportFullName" => "KITENGELA BRANCH",
                "addressDetails" => "P.O BOX 20001",
                "phoneNumber" => "2545324324",
                "address2" => "00200 NAIROBI",
                "address3" => "KENYA",
                "phoneNumber" => "Tel: +254(20) 6943000/100",
                "Mobile" => "Mobile: +254 722 209872/3/6",
            ];
        }
    }
}
