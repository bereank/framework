<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OLCT;

class GPMGate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'gates';

    public function location(): BelongsTo
    {
        return $this->belongsTo(OLCT::class, 'location_id');
    }
}
