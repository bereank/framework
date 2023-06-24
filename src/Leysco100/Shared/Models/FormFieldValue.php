<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\GatePassManagementModule\Models\FormField;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
