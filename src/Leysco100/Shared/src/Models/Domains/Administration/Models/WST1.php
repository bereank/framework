<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;

class WST1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'w_s_t1_s';

    public function users()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
