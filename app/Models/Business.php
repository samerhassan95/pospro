<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\AffiliateAddon\App\Models\Affiliate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_subscribe_id',
        'business_category_id',
        'companyName',
        'address',
        'phoneNumber',
        'pictureUrl',
        'will_expire',
        'subscriptionDate',
        'remainingShopBalance',
        'shopOpeningBalance',
        'vat_name',
        'vat_no',
        'commercial_registration',
        'additional_id',
        'bank_account_number',
        'bank_name',
        'affiliator_id',
        'email',
        'status',
        'meta',
        'zatca_setting',
        'moyasar_setting',
        'building_number',
        'street_name',
        'district',
        'city',
        'postal_code',
        'country_code',
        'additional_address',
    ];

    public function enrolled_plan()
    {
        return $this->belongsTo(PlanSubscribe::class, 'plan_subscribe_id');
    }

    public function category()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function affiliator(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class, 'affiliator_id');
    }

    /**
     * Get the plan associated with this business
     */
    public function plan()
    {
        return $this->enrolled_plan?->plan;
    }

    /**
     * Check if business plan allows a specific permission
     */
    public function allows($permission)
    {
        $plan = $this->plan();
        return $plan ? $plan->allows($permission) : false;
    }

    /**
     * Check if business can add more warehouses
     */
    public function canAddWarehouse()
    {
        $plan = $this->plan();
        if (!$plan) {
            return false;
        }

        // Use DB::table to bypass any global scopes
        $currentCount = \DB::table('warehouses')
            ->where('business_id', $this->id)
            ->count();

        return $plan->canAddWarehouse($currentCount);
    }

    /**
     * Check if business can add more branches
     */
    public function canAddBranch()
    {
        $plan = $this->plan();
        if (!$plan) {
            return false;
        }

        // Use DB::table to bypass any global scopes
        $currentCount = \DB::table('branches')
            ->where('business_id', $this->id)
            ->count();

        return $plan->canAddBranch($currentCount);
    }

    /**
     * Get warehouse limit for this business
     */
    public function getWarehouseLimit()
    {
        $plan = $this->plan();
        return $plan ? $plan->warehouse_limit : 0;
    }

    /**
     * Get branch limit for this business
     */
    public function getBranchLimit()
    {
        $plan = $this->plan();
        return $plan ? $plan->branch_limit : 0;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'plan_subscribe_id' => 'integer',
        'business_category_id' => 'integer',
        'remainingShopBalance' => 'double',
        'shopOpeningBalance' => 'double',
        'status' => 'integer',
        'meta' => 'json',
        'zatca_setting' => 'json',
        'moyasar_setting' => 'json'
    ];
}
