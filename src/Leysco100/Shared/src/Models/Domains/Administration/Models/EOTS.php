<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EOTS extends Model
{
    use HasFactory;
    //numbering series
    protected $guarded = ['id'];
    protected $table = 'e_o_t_s';
}
