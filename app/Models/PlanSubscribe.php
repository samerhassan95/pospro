<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanSubscribe extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'price',
        'notes',
        'plan_id',
        'service_code',
        'service_start_date',
        'service_end_date',
        'tax_period_start',
        'tax_period_end',
        'po_number',
        'contract_number',
        'payment_terms',
        'payment_means',
        'duration',
        'gateway_id',
        'business_id',
        'payment_status',
        'allow_multibranch',
        'addon_domain_limit',
        'subdomain_limit',
        'uuid',
        'invoice_number',
        'invoice_type',
        'zatca_status',
        'invoice_hash',
        'previous_hash',
        'cryptographic_stamp',
        'zatca_response'
    ];

    protected $casts = [
        'notes' => 'json',
        'duration' => 'integer',
        'price' => 'double',
        'plan_id' => 'integer',
        'business_id' => 'integer',
        'gateway_id' => 'integer',
        'allow_multibranch' => 'integer',
        'addon_domain_limit' => 'integer',
        'subdomain_limit' => 'integer',
        'zatca_response' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) \Illuminate\Support\Str::uuid();
            $model->invoice_number = 'SUB-' . strtoupper(\Illuminate\Support\Str::random(8));
        });
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }
}
