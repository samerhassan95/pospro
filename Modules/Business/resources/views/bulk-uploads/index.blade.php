@extends('layouts.business.master')

@section('title')
    {{ __('Bulk Upload') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="border-0">
                <div class="card-bodys">
                    <form action="{{ route('business.bulk-uploads.store') }}" method="post" enctype="multipart/form-data"
                        class="ajaxform_instant_reload">
                        <div class="bulk-upload-container">
                            <div class="d-flex justify-content-between align-items-center ">
                                <div class="bulk-input">
                                    <input class="form-control" type="file" name="file" required>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                @usercan('bulk-uploads.create')
                                <button type="submit" class="add-order-btn rounded-2 border-0 submit-btn mt-3">Submit</button>
                                @endusercan
                                <a href="{{ asset('assets/POSpro_bulk_product_upload.xlsx') }}" download="POSpro_bulk_product_upload.xlsx" class="download-file-btn mt-3"><i class="fas fa-download"></i>{{ __('Download File') }}</a>
                            </div>
                        </div>
                        <div class="bulk-upload-container mt-3">
                            <div class="instruction-header">
                                <h5>Instructions</h5>
                                <div class="mt-3">
                                    <h6><strong>Note: </strong> Please follow the instructions below to upload your file.</h6>
                                    <ul>
                                        <li><b>1.</b> Download the sample file first and add all your products to it.</li>
                                        <li><b>2.</b> <span class="text-danger">*</span> Indicates a required field. If you do not provide the required fields, the system will ignore the product.</li>
                                        <li><b>3.</b> After adding all your products, please save the file and then upload the updated version.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="responsive-table mt-4">
                                <table class="table" id="datatable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('SL') }}.</th>
                                            <th class="text-start">{{ __('Field Name') }}</th>
                                            <th class="text-start">{{ __('Description') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="business-category-data">
                                        <tr>
                                            <td>1</td>
                                            <td class="text-start">Product Name <span class="text-danger fw-bold">*</span></td>
                                            <td class="text-start"> The name of the product you are adding (e.g., "Banana").</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td class="text-start">Product Category <span class="text-danger fw-bold">*</span></td>
                                            <td class="text-start">
                                                The category under which the product falls (e.g., Beverages, Electronics).
                                                <br>
                                                <small>If the system does not find this, it will automatically create a new one.</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td class="text-start">Unit Name</td>
                                            <td class="text-start">
                                                The measurement unit used for the product (e.g., Piece, Kg, Litre, Box).
                                                <br>
                                                <small>If the system does not find this, it will automatically create a new one.</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td class="text-start">Brand Name</td>
                                            <td class="text-start">
                                                The brand or manufacturer associated with the product (e.g., Samsung, Nestlé).
                                                <br>
                                                <small>If the system does not find this, it will automatically create a new one.</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td class="text-start">Stock Quantity</td>
                                            <td class="text-start"> The available quantity of the product currently in inventory.</td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td class="text-start">Product Code <span class="text-danger fw-bold">*</span></td>
                                            <td class="text-start">
                                                A unique code for the product, often used for barcodes or easy search.
                                                <br>
                                                <small>Product codes must be unique. If a code is already in use and you include a non-unique product code in the file, the system will ignore that product.</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td class="text-start">Purchase Price <span class="text-danger fw-bold">*</span></td>
                                            <td class="text-start">The cost price of the product without including VAT.</td>
                                        </tr>
                                        <tr>
                                            <td>8</td>
                                            <td class="text-start">Sale Price(MRP) <span class="text-danger fw-bold">*</span></td>
                                            <td class="text-start">Maximum Retail Price — the highest price allowed to sell the product to customers.</td>
                                        </tr>
                                        <tr>
                                            <td>9</td>
                                            <td class="text-start">Dealer Price</td>
                                            <td class="text-start">Special discounted price for resellers or dealers who buy in large quantities.</td>
                                        </tr>
                                        <tr>
                                            <td>10</td>
                                            <td class="text-start">Wholesale Price</td>
                                            <td class="text-start">The price offered for bulk purchases, typically lower than the MRP.</td>
                                        </tr>
                                        <tr>
                                            <td>11</td>
                                            <td class="text-start">VAT Name</td>
                                            <td class="text-start">
                                                The name of the VAT (Value Added Tax) applied to the product.
                                                <br>
                                                <small>If the system does not find an existing VAT entry, it will automatically create a new one and set the rate based on the VAT (%) value you have provided in the column.</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>12</td>
                                            <td class="text-start">VAT (%)</td>
                                            <td class="text-start">The percentage of VAT (Value Added Tax) applied to the product.</td>
                                        </tr>
                                        <tr>
                                            <td>13</td>
                                            <td class="text-start">Vat Type (exclusive / inclusive)</td>
                                            <td class="text-start">Whether the VAT is added on top of the price (exclusive) or already included in the price (inclusive).</td>
                                        </tr>
                                        <tr>
                                            <td>14</td>
                                            <td class="text-start">Low Stock Qty</td>
                                            <td class="text-start">The quantity threshold to alert when stock is low (e.g., if stock drops below 10 units).</td>
                                        </tr>
                                        <tr>
                                            <td>15</td>
                                            <td class="text-start">Manufacturer</td>
                                            <td class="text-start">The name of the company or entity that produces the product.</td>
                                        </tr>
                                        <tr>
                                            <td>16</td>
                                            <td class="text-start">Expire Date</td>
                                            <td class="text-start">The expiry date for the product (e.g., YYYY-MM-DD).</td>
                                        </tr>
                                        <tr>
                                            <td>17</td>
                                            <td class="text-start">Batch No</td>
                                            <td class="text-start">The batch number associated with the current stock.</td>
                                        </tr>
                                        <tr>
                                            <td>18</td>
                                            <td class="text-start">Model</td>
                                            <td class="text-start">The specific model name of the product.</td>
                                        </tr>
                                        <tr>
                                            <td>19</td>
                                            <td class="text-start">Manufacturing Date</td>
                                            <td class="text-start">The date when the product was produced.</td>
                                        </tr>
                                        <tr>
                                            <td>20</td>
                                            <td class="text-start">Product Type (single / variant)</td>
                                            <td class="text-start">Whether the product is a standard single item or part of a variation group.</td>
                                        </tr>
                                        <tr>
                                            <td>21</td>
                                            <td class="text-start">Variations Text</td>
                                            <td class="text-start">For 'variant' products, specify details like: "Color:Black|Size:M".</td>
                                        </tr>
                                        <tr>
                                            <td>22</td>
                                            <td class="text-start">Picture URL</td>
                                            <td class="text-start">A full URL to an image (e.g., https://example.com/image.jpg). The system will download it.</td>
                                        </tr>
                                        <tr>
                                            <td>23</td>
                                            <td class="text-start">Size</td>
                                            <td class="text-start">The physical dimensions or size of the product.</td>
                                        </tr>
                                        <tr>
                                            <td>24</td>
                                            <td class="text-start">Color</td>
                                            <td class="text-start">The color of the product.</td>
                                        </tr>
                                        <tr>
                                            <td>25</td>
                                            <td class="text-start">Weight</td>
                                            <td class="text-start">The weight of the product (e.g., 500g, 1kg).</td>
                                        </tr>
                                        <tr>
                                            <td>26</td>
                                            <td class="text-start">Capacity</td>
                                            <td class="text-start">The capacity or volume (e.g., 256GB, 1.5L).</td>
                                        </tr>
                                        <tr>
                                            <td>27</td>
                                            <td class="text-start">Rack Name</td>
                                            <td class="text-start">The warehouse rack where the product is stored.</td>
                                        </tr>
                                        <tr>
                                            <td>28</td>
                                            <td class="text-start">Shelf Name</td>
                                            <td class="text-start">The specific shelf number or ID within the rack.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
