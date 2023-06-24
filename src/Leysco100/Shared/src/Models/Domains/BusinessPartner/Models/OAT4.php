<?php
namespace Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;

class OAT4 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_a_t4_s';

    public function ooat()
    {
        return $this->belongsTo('App\OOAT', 'AgrNo');
    }

    public function orcp()
    {
        return $this->belongsTo('App\ORCP', 'RcpEntry');
    }
}
