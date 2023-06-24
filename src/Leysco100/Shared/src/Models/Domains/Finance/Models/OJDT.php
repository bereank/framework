<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Finance\Models;

use App\Domains\Finance\Models\JDT1;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OJDT extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_j_d_t_s';

    public function jdt1()
    {
        return $this->hasMany(JDT1::class, 'TransId');
    }
}
