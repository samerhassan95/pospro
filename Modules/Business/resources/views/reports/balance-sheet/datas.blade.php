@php
    $serial = 1;
@endphp

{{-- Products (Stock) --}}
@foreach($products as $product)
    @php
        $stock_value = 0;
        if (in_array($product->product_type, ['single', 'variant'])) {
            foreach ($product->stocks as $stock) {
                $stock_value += $stock->productStock * $stock->productPurchasePrice;
            }
        } elseif ($product->product_type === 'combo') {
            foreach ($product->combo_products as $combo) {
                $childStock = $combo->stock;
                if ($childStock) {
                    $stock_value += ($childStock->productStock / $combo->quantity) * $combo->purchase_price;
                }
            }
        }
    @endphp
    <tr>
        <td>{{ $serial++ }}</td>
        <td class="text-start"><span class="badge bg-info">{{ __('Stock') }}</span></td>
        <td class="text-start">{{ $product->productName }}</td>
        <td class="text-end">{!! currency_format($stock_value, currency: business_currency()) !!}</td>
    </tr>
@endforeach

{{-- Banks & Payment Types --}}
@foreach($banks as $bank)
    <tr>
        <td>{{ $serial++ }}</td>
        <td class="text-start"><span class="badge bg-success">{{ __('Bank/Cash') }}</span></td>
        <td class="text-start">{{ $bank->name }}</td>
        <td class="text-end">{!! currency_format($bank->balance, currency: business_currency()) !!}</td>
    </tr>
@endforeach

{{-- Total Row --}}
<tr class="table-active fw-bold">
    <td colspan="3" class="text-end">{{ __('Total Assets') }}</td>
    <td class="text-end">{!! currency_format($total_asset ?? 0, currency: business_currency()) !!}</td>
</tr>
