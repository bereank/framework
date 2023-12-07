<?php

namespace Leysco100\Shared\Models\Gpm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormFieldValue extends Model
{
    use HasFactory;

    protected $table = 'form_field_values';

    protected $guarded = [];
    protected $fillable = ['id'];

    public function field()
    {
        return $this->belongsTo(FormField::class);
    }
}
