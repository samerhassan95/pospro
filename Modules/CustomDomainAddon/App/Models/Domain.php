<?php

namespace Modules\CustomDomainAddon\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'domain', 'is_verified', 'is_ssl_enabled', 'status', 'notes'];

    public function scopeAddon($query)
    {
        return $query->whereRaw('domain NOT LIKE "%' . get_root_domain() . '%"');
    }

    public function scopeSubdomain($query)
    {
        return $query->where('domain', 'like', '%' . get_root_domain() . '%');
    }
}
