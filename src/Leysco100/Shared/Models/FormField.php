<?php

namespace Leysco100\Shared\Models;



use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\FormFieldType;
use Leysco100\Shared\Models\FormFieldValue;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FormField extends Model
{

    use UsesTenantConnection;
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
