@if(isset($cart_contents))
    @foreach($cart_contents as $cart)
    <tr data-row_id="{{ $cart->rowId }}" data-update_route="{{ route('business.carts.update', $cart->rowId) }}" data-destroy_route="{{ route('business.carts.destroy', $cart->rowId) }}">
        <td>
            <img class="table-img" src="{{ asset($cart->options->product_image ?? 'assets/images/products/box.svg') }}" alt="{{ $cart->name }}">
        </td>
        <td class="text-start">{{ $cart->name }}</td>
        <td>{{ $cart->options->product_code }}</td>
        <td>{{ $cart->options->batch_no ?? '-' }}</td>
        <td>{{ $cart->options->product_unit_name }}</td>
        <td>
            <input class="text-center form-control cart-price" type="number" step="any" min="0" value="{{ $cart->price }}" placeholder="0" style="width: 90px;">
        </td>
        <td>
            <div class="d-flex gap-2 align-items-center justify-content-center">
                <button type="button" class="incre-decre minus-btn"><i class="fas fa-minus icon"></i></button>
                <input type="number" step="any" value="{{ $cart->qty }}" class="form-control cart-qty text-center" placeholder="0" style="width: 60px;">
                <button type="button" class="incre-decre plus-btn"><i class="fas fa-plus icon"></i></button>
            </div>
        </td>
        <td class="cart-subtotal">{!! currency_format($cart->subtotal, currency: business_currency()) !!}</td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-btn"><i class="fas fa-trash-alt"></i></button>
        </td>
    </tr>
    @endforeach
@endif
