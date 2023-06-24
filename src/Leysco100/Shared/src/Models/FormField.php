<?php

namespace Leysco100\Shared\Models;



use Illuminate\Database\Eloquent\Model;
use Leysco\GatePassManagementModule\Models\FormFieldType;
use Leysco\GatePassManagementModule\Models\FormFieldValue;

class FormField extends Model
{
    protected $guarded = [];

    protected $table = "form_fields";


    public function type()
    {
        return $this->belongsTo(FormFieldType::class, 'type_id');
    }
    public function dropDownValues()
    {
        return $this->hasMany(FormFieldValue::class, 'field_id');
    }
}
