<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LS100Module extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'l_s100_modules';
}
