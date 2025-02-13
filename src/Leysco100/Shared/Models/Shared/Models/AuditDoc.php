<?php

namespace Leysco100\Shared\Models\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditDoc extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'audit_docs';
}
