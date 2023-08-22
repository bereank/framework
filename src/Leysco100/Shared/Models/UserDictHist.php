<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class UserDictHist extends Model
{
    use HasFactory,UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'user_dict_hists';
}
