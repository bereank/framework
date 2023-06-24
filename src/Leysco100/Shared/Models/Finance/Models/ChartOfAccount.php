<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class ChartOfAccount extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'chart_of_accounts';
    public function childrenAll()
    {
        return $this->hasMany(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function children()
    {
        return $this->childrenAll()->with('children');
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive');
    }

    public function getAllChildren()
    {
        $sections = new Collection();

        foreach ($this->childrenAll as $section) {
            $sections->push($section);
            $sections = $sections->merge($section->getAllChildren());
        }

        return $sections;
    }

    // public function chartOfAccounts()
    // {
    //     return $this->hasMany(ChartOfAccount::class);
    // }

    // public function childrenChartOfAccount()
    // {
    //     return $this->hasMany(ChartOfAccount::class)->with('chartOfAccounts');
    // }
}
