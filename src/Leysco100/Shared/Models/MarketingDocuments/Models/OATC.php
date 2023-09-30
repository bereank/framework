<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OATC extends Model
{
    use UsesTenantConnection;

    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_a_t_c_s';

    public function attachment_lines()
    {
        return $this->hasMany(ATC1::class, 'AbsEntry');
    }
}
