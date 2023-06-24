<?php
namespace Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'employees';
}
