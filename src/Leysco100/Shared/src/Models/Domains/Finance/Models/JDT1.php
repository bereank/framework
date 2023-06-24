<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Finance\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JDT1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'j_d_t1_s';

    public function oact()
    {
        return $this->belongsTo(ChartOfAccount::class, 'Account');
    }
}
