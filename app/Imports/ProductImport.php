<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Vat;
use App\Models\Rack;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Shelf;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductModel;
use App\Models\Variation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ProductImport implements ToCollection
{
    protected $businessId;
    protected $branchId;
    protected $categories = [];
    protected $brands = [];
    protected $units = [];
    protected $vats = [];
    protected $models = [];
    protected $racks = [];
    protected $shelves = [];
    protected $existingProductCodes = [];
    protected $excelProductCodes = [];

    public function __construct($businessId, $branchId = null)
    {
        $this->businessId = $businessId;
        $this->branchId = $branchId;
        $this->existingProductCodes = Product::where('business_id', $businessId)
            ->pluck('productCode')
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header row

                // Read Excel columns
                $productName       = trim($row[0]);
                $categoryName      = trim($row[1]);
                $unitName          = trim($row[2]);
                $brandName         = trim($row[3]);
                $stockQty          = $row[4] ?? 0;
                $productCode       = trim($row[5]);
                $purchasePrice     = (float)($row[6] ?? 0);
                $salePrice         = (float)($row[7] ?? 0);
                $dealerPrice       = (float)($row[8] ?? $salePrice);
                $wholesalePrice    = (float)($row[9] ?? $salePrice);
                $vatName           = trim($row[10]);
                $vatPercent        = (float)($row[11] ?? 0);
                $vatType           = $row[12] ?? 'exclusive';
                $alertQty          = (int)($row[13] ?? 0);
                $manufacturer      = $row[14] ?? null;
                $expireDate        = $this->parseExcelDate($row[15]);
                $batchNo           = $row[16] ?? null;
                $model             = trim($row[17]);
                $manufacturingDate = $row[18] ?? null;
                $productType       = strtolower(trim($row[19] ?? 'single'));
                $variationsText    = trim($row[20] ?? ''); // Example: "Color:Black|Size:M"
                $pictureUrl        = trim($row[21] ?? '');
                $size              = trim($row[22] ?? '');
                $color             = trim($row[23] ?? '');
                $weight            = trim($row[24] ?? '');
                $capacity          = trim($row[25] ?? '');
                $rackName          = trim($row[26] ?? '');
                $shelfName         = trim($row[27] ?? '');

                if (!$productName || !$productCode || !$categoryName) continue;
                if (in_array($productCode, $this->existingProductCodes)) continue;
                if (in_array($productCode, $this->excelProductCodes)) continue;

                // --- VAT and profit ---
                $vatAmount = ($purchasePrice * $vatPercent) / (100 + $vatPercent); // Extract VAT calculation for inclusive
                $grandPurchasePrice = $purchasePrice; // In our POS, we store the full price as entered
                
                $exclusivePurchasePrice = $vatType === 'inclusive' 
                    ? $purchasePrice / (1 + ($vatPercent / 100))
                    : $purchasePrice;

                $profitPercent = $exclusivePurchasePrice > 0
                    ? round((($salePrice - $exclusivePurchasePrice) / $exclusivePurchasePrice) * 100, 3)
                    : 0.0;
                $this->excelProductCodes[] = $productCode;

                // --- Related models ---
                $categoryId = $this->categories[$categoryName] ??= Category::firstOrCreate(
                    ['categoryName' => $categoryName, 'business_id' => $this->businessId],
                    ['categoryName' => $categoryName]
                )->id;

                $brandId = $this->brands[$brandName] ??= Brand::firstOrCreate(
                    ['brandName' => $brandName, 'business_id' => $this->businessId],
                    ['brandName' => $brandName]
                )->id;

                $unitId = $this->units[$unitName] ??= Unit::firstOrCreate(
                    ['unitName' => $unitName, 'business_id' => $this->businessId],
                    ['unitName' => $unitName]
                )->id;

                $vatId = $this->vats[$vatName] ??= Vat::firstOrCreate(
                    ['name' => $vatName, 'business_id' => $this->businessId],
                    ['name' => $vatName, 'rate' => $vatPercent]
                )->id;

                $modelId = $this->models[$model] ??= ProductModel::firstOrCreate(
                    ['name' => $model, 'business_id' => $this->businessId],
                    ['name' => $model]
                )->id;

                // --- Parse variations ---
                $variationData = [];
                $variantNameParts = [];
                $variationIds = [];

                if ($productType === 'variant' && $variationsText) {
                    $variationParts = explode('|', $variationsText);
                    foreach ($variationParts as $part) {
                        [$name, $value] = array_map('trim', explode(':', $part));

                        if ($name && $value) {
                            $variationData[] = [$name => $value];
                            $variantNameParts[] = $value;

                            // Find existing variation ID by name and value
                            $variation = Variation::where('business_id', $this->businessId)
                                ->where('name', $name)
                                ->first();

                            if ($variation && in_array($value, $variation->values)) {
                                $variationIds[] = (string)$variation->id;
                            }
                        }
                    }
                }

                // --- Handle Image ---
                $productPicture = null;
                if ($pictureUrl && filter_var($pictureUrl, FILTER_VALIDATE_URL)) {
                    try {
                        $contents = file_get_contents($pictureUrl);
                        if ($contents !== false) {
                            $ext = pathinfo(parse_url($pictureUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                            $filename = now()->timestamp . '-' . rand(1, 1000) . '.' . $ext;
                            $path = 'uploads/' . date('y') . '/' . date('m') . '/' . $filename;
                            Storage::disk(config('filesystems.default'))->put($path, $contents);
                            $productPicture = $path;
                        }
                    } catch (\Exception $e) {
                        // Silent fail for image
                    }
                }

                // --- Rack and Shelf ---
                $rackId = null;
                if ($rackName) {
                    $rackId = $this->racks[$rackName] ??= Rack::firstOrCreate(
                        ['name' => $rackName, 'business_id' => $this->businessId],
                        ['name' => $rackName]
                    )->id;
                }

                $shelfId = null;
                if ($shelfName) {
                    $shelfId = $this->shelves[$shelfName] ??= Shelf::firstOrCreate(
                        ['name' => $shelfName, 'business_id' => $this->businessId],
                        ['name' => $shelfName, 'rack_id' => $rackId]
                    )->id;
                }

                $variantName = implode(' - ', $variantNameParts);

                // --- Create Product ---
                $product = Product::create([
                    'productName'   => $productName,
                    'business_id'   => $this->businessId,
                    'unit_id'       => $unitId,
                    'brand_id'      => $brandId,
                    'category_id'   => $categoryId,
                    'model_id'      => $modelId,
                    'rack_id'       => $rackId,
                    'shelf_id'      => $shelfId,
                    'productCode'   => $productCode,
                    'vat_id'        => $vatId,
                    'vat_type'      => $vatType,
                    'vat_amount'    => $vatAmount,
                    'alert_qty'     => $alertQty,
                    'expire_date'   => $expireDate,
                    'manufacturer'  => $manufacturer,
                    'product_type'  => $productType,
                    'variation_ids' => $variationIds,
                    'productPicture' => $productPicture,
                    'size'          => $size,
                    'color'         => $color,
                    'weight'        => $weight,
                    'capacity'      => $capacity,
                ]);

                // --- Create Stock ---
                Stock::updateOrCreate(
                    [
                        'batch_no'    => $batchNo,
                        'business_id' => $this->businessId,
                        'product_id'  => $product->id,
                    ],
                    [
                        'expire_date'            => $expireDate,
                        'productStock'           => $stockQty,
                        'productPurchasePrice'   => $grandPurchasePrice,
                        'branch_id'              => $this->branchId,
                        'profit_percent'         => $profitPercent,
                        'productSalePrice'       => $salePrice,
                        'productWholeSalePrice'  => $wholesalePrice,
                        'productDealerPrice'     => $dealerPrice,
                        'mfg_date'               => $manufacturingDate,
                        'variation_data'         => $variationData,
                        'variant_name'           => $variantName,
                    ]
                );
            }
        });
    }

    protected function parseExcelDate($value)
    {
        if (empty($value)) return null;

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $value = trim($value);
        foreach (['m/d/Y', 'd/m/Y', 'Y-m-d', 'd-m-Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }
        return null;
    }
}
