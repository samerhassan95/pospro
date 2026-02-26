<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'duration',
        'offerPrice',
        'subscriptionName',
        'subscriptionPrice',
        'visibility',
        'features',
        'affiliate_commission',
        'allow_multibranch',
        'addon_domain_limit',
        'subdomain_limit',
        'allow_purchases',
        'allow_products',
        'allow_warehouses',
        'warehouse_limit',
        'branch_limit',
        'allow_stock',
        'allow_customers',
        'allow_suppliers',
        'allow_vat_settings',
        'allow_due_list',
        'allow_finance',
        'allow_commission',
        'allow_hrm',
        'allow_reports',
        'allow_pos_app',
        'allow_store',
        'allow_sales',
    ];

    public function planSubscribes()
    {
        return $this->hasMany(PlanSubscribe::class, 'plan_id');
    }

    /**
     * Check if plan allows a specific permission
     */
    public function allows($permission)
    {
        $field = 'allow_' . $permission;
        return $this->$field ?? false;
    }

    /**
     * Check if plan has reached warehouse limit
     */
    public function canAddWarehouse($currentCount)
    {
        if (!$this->allow_warehouses) {
            return false;
        }
        
        if ($this->warehouse_limit === null) {
            return true; // Unlimited
        }
        
        return $currentCount < $this->warehouse_limit;
    }

    /**
     * Check if plan has reached branch limit
     */
    public function canAddBranch($currentCount)
    {
        if (!$this->allow_multibranch) {
            return false;
        }
        
        if ($this->branch_limit === null) {
            return true; // Unlimited
        }
        
        return $currentCount < $this->branch_limit;
    }

    /**
     * Get warehouse limit text
     */
    public function getWarehouseLimitText()
    {
        if (!$this->allow_warehouses) {
            return 'Not Allowed';
        }
        
        return $this->warehouse_limit === null ? 'Unlimited' : $this->warehouse_limit;
    }

    /**
     * Get branch limit text
     */
    public function getBranchLimitText()
    {
        if (!$this->allow_multibranch) {
            return 'Single Branch Only';
        }
        
        return $this->branch_limit === null ? 'Unlimited' : $this->branch_limit;
    }


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'features' => 'json',
        'duration' => 'integer',
        'offerPrice' => 'double',
        'status' => 'integer',
        'visibility' => 'json',
        'subscriptionPrice' => 'double',
        'allow_multibranch' => 'integer',
        'addon_domain_limit' => 'integer',
        'subdomain_limit' => 'integer',
        'allow_purchases' => 'boolean',
        'allow_products' => 'boolean',
        'allow_warehouses' => 'boolean',
        'warehouse_limit' => 'integer',
        'branch_limit' => 'integer',
        'allow_stock' => 'boolean',
        'allow_customers' => 'boolean',
        'allow_suppliers' => 'boolean',
        'allow_vat_settings' => 'boolean',
        'allow_due_list' => 'boolean',
        'allow_finance' => 'boolean',
        'allow_commission' => 'boolean',
        'allow_hrm' => 'boolean',
        'allow_reports' => 'boolean',
        'allow_pos_app' => 'boolean',
        'allow_store' => 'boolean',
        'allow_sales' => 'boolean',
    ];
}
