@php 
$feeTotal = 0;
$customTaxTotal = 0;
$customTax = !empty($customTax) ? json_decode($customTax->value) : null;
@endphp
<div class="card-block calculations_div" id="calculations_div">
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table invoice-detail-table">
                    <thead>
                        <tr class="thead-default">
                            <th class="w-5"></th>
                            <th>{{ __('Products') }}</th>
                            <th class="align-center">{{ __('Status') }}</th>
                            <th class="align-center">{{ __('Cost') }}</th>
                            <th class="align-center">{{ __('Qty') }}</th>
                            <th class="align-center">{{ __('Total') }}</th>
                            <th class="align-center">{{ __('Tax') }}</th>
                            @if(isset($customTax))
                                @foreach($customTax->product as $customKey => $cut)
                                    <th class="align-center">{{ __('Tax') }}
                                        @if($order->orderStatus->payment_scenario != 'paid')
                                        <a href="javascript:void(0)" class="delete_custom_tax" data-key="{{ $customKey }}" data-label="Delete" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="feather icon-trash"></i></a>
                                        @endif
                                    </th>
                                @endforeach
                            @endif
                            @if($order->orderStatus->payment_scenario != 'paid')
                            <th></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderDetails as $details)
                            @foreach ($details as $dtkey => $detail)
                            @php
                                if (isActive('Refund')) {
                                    $orderDeliverId = \App\Models\Order::getFinalOrderStatus();
                                }

                                $opName = '';
                                if ($detail->payloads != null) {
                                    $option = (array)json_decode($detail->payloads);
                                    $itemCount = count($option);
                                    $i = 0;
                                    foreach ($option as $key => $value) {
                                        $opName .= $key . ': ' . $value . (++$i == $itemCount ? '' : ', ');
                                    }
                                }

                                $productInfo = $orderAction->getProductInfo($detail);
                                $totalRefund = $detail->refunds()->where('status','Accepted')->sum('quantity_sent')
                            @endphp
                            <tr>
                                <td class="text-right align-middle">
                                    <img class="rounded" src="{{ $productInfo['image'] }}" alt="image" width="45" height="45">
                                </td>
                                <td class="text-left align-middle">

                                    <h6>
                                        <a href="{{ $productInfo['url'] }}" title="{{ $detail->product_name }}">
                                            {{ trimWords($detail->product_name, 50) }}
                                        </a>
                                    </h6>
                                    <p class="p-0 mb-0">
                                        {{ !is_null($details[0]->vendor_id) ? __('Vendor')." : ". optional($details[0]->vendor)->name . " | " : '' }}
                                        {{ __('SKU') }} : {{ optional($detail->product)->sku }} {{ !empty($opName) ? " | " . $opName : '' }}
                                    </p>
                                </td>

                                <td class="pb-1">
                                    @if ($totalRefund != $detail->quantity)
                                @if($detail->is_delivery == 1)
                                    <p class="align-center mt-3">{{ __('Completed') }}</p>
                                @else
                                    <select class="form-control align-center mt-1 status order-status {{ $detail->is_delivery == 1 ? 'delivery' : '' }}" name="status[{{ $detail->id }}]" data-id = "{{ $detail->id }}" {{ $detail->is_delivery == 1 ? 'disabled' : '' }}>
                                        @foreach ($orderStatus as $status)
                                            @if (strtolower(optional($detail->orderStatus)->payment_scenario) == 'unpaid' && $status->payment_scenario == 'unpaid')
                                                <option value="{{ $status->id }}" {{ $status->id == $detail->order_status_id ? 'selected' : '' }}>{{ $status->name }}</option>
                                            @endif
                                            @if ($status->payment_scenario == 'paid')
                                                <option value="{{ $status->id }}" {{ $detail->order_status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                @endif
                                @else
                                    <p class="align-center mt-3">{{ __('Refunded') }}</p>
                                @endif
                                </td>
                                <td class="pb-1" id="product_price_td_{{ $detail->product_id }}">
                                    <p class="align-center mt-3 product_price back_action" id="product_price_{{ $detail->product_id }}">{{ formatCurrencyAmount($detail->price) }}</p>
                                </td>
                                <td class="pb-1" id="product_qty_td_{{ $detail->product_id }}">
                                    <p class="align-center mt-3 product_qty back_action" id="product_qty_{{ $detail->product_id }}">{{ formatCurrencyAmount($detail->quantity) }}</p>
                                </td>
                                <td class="pb-1"><p class="align-center mt-3">{{ formatNumber($detail->price * $detail->quantity, optional($order->currency)->symbol) }}</p></td>
                                <td class="pb-1" id="product_tax_td_{{ $detail->product_id }}">
                                    <p class="align-center mt-3 back_action" id="product_tax_{{ $detail->product_id }}">{{ formatNumber($detail->tax_charge, optional($order->currency)->symbol) }}</p> 
                                </td>
                                @if(isset($customTax))
                                    @foreach($customTax->product as $key12 => $cut)
                                        @foreach($cut as $customTaxProduct)
                                            @if($customTaxProduct->product_id == $detail->product_id)
                                                <td class="pb-1" data-key="{{ $key12 }}" data-amount="{{ $customTaxProduct->tax }}">
                                                    <p class="align-center mt-3 back_action custom_tax_{{ $detail->product_id }} ">{{ formatNumber($customTaxProduct->tax, optional($order->currency)->symbol) }}</p>
                                                </td>
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endif
                                @if($order->orderStatus->payment_scenario != 'paid')
                                <td class="pb-1 text-right align-middle">
                                    <a href="javascript:void(0)" title="{{ __('Edit') }}" class="edit_product back_action" data-productId="{{ $detail->product_id }}" data-price = "{{ $detail->price }}" data-qty = "{{ $detail->quantity }}" data-tax = "{{ $detail->tax_charge }}"><i class="feather icon-edit-1"></i></a>
                                    <a href="javascript:void(0)" title="{{ __('Delete') }}" class="delete_product" data-productId="{{ $detail->product_id }}" data-label="Delete" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="feather icon-trash"></i></a>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        @endforeach
                        
                    @if(!empty($customFee))
                        @php $feeData = json_decode($customFee->value) @endphp
                        @foreach($feeData as $feeKey => $fee)
                            <tr>
                                <td class="pb-1">
                                    <i class="fas fa-plus-circle mt-3"></i>
                                </td>
                                <td class="pb-1">
                                    <p class="align-center mt-3">{{ $fee->type != 'percent' ? formatNumber($fee->amount, optional($order->currency)->symbol) : formatCurrencyAmount($fee->amount) }}{{ $fee->type == 'percent' ? '%' : '' }} <span class="back_action" id="order_fee_lbl_{{ $fee->key }}">{{ $fee->label }}</span></p> 
                                </td>
                                <td id="order_fee_lbl_td_{{ $fee->key }}"></td>
                                <td></td>
                                <td></td>
                                @php
                                    $feeTotal += $fee->calculated_amount;
                                    $customTaxTotal += $fee->tax;
                                @endphp
                                <td class="pb-1" id="order_fee_td_{{ $fee->key }}"><p class="align-center mt-3 back_action" id="order_fee_{{ $fee->key }}">{{ formatNumber($fee->calculated_amount, optional($order->currency)->symbol) }}</p></td>
                                <td id="order_fee_tax_td_{{ $fee->key }}"><p class="align-center mt-3 back_action" id="order_fee_tax_{{ $fee->key }}">{{ formatNumber($fee->tax, optional($order->currency)->symbol) }}</p></td>
                                @if(isset($customTax))
                          
                                    @foreach($customTax->fee as $key2345 => $cut)
                                        @foreach($cut as $customTaxFee)
                                            @if($customTaxFee->key == $fee->key)
                                                <td class="pb-1" data-key="{{ $fee->key }}" data-index="{{ $key2345 }}" data-amount="{{ $customTaxFee->tax }}">
                                                    <p class="align-center mt-3 back_action custom_fee_{{ $fee->key }}">{{ formatNumber($customTaxFee->tax, optional($order->currency)->symbol) }}</p>
                                                </td> 
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endif
                                @if($order->orderStatus->payment_scenario != 'paid')
                                <td class="pb-1 text-right align-middle">
                                    <a href="javascript:void(0)" title="{{ __('Edit') }}" class="edit_fee back_action" data-key="{{ $fee->key }}" data-amount = "{{ $fee->calculated_amount }}" data-lbl="{{ $fee->label }}" data-tax="{{ $fee->tax }}"><i class="feather icon-edit-1"></i></a>
                                    <a href="javascript:void(0)" title="{{ __('Delete') }}" class="delete_fee" data-key="{{ $fee->key }}" data-label="Delete" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="feather icon-trash"></i></a>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                    
                    @foreach($order->couponRedeems as $redeem)
                        <tr>
                            <td class="pb-1">
                                <i class="fas fa-bullhorn mt-3"></i>
                            </td>
                            <td class="pb-1">
                                <p class="align-center mt-3">{{ $redeem->coupon_code }} ({{ __('Coupon') }})</p>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><p class="align-center mt-3">-{{ formatNumber($redeem->discount_amount, optional($order->currency)->symbol) }}</p></td>
                            <td></td>
                            @if(isset($customTax))
                                @foreach($customTax->product as $customKey => $cut)
                                    <td></td>
                                @endforeach
                            @endif
                            @if($order->orderStatus->payment_scenario != 'paid')
                            <td class="pb-1 text-right align-middle">
                                <a href="javascript:void(0)" title="{{ __('Delete') }}" class="delete_coupon" data-key="{{ $redeem->id }}" data-label="Delete" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="feather icon-trash"></i></a>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-responsive invoice-table invoice-total">
                <tbody class="mt-3 mb-3">
                    @php
                    $couponOffer = isset($order->couponRedeems) && $order->couponRedeems->sum('discount_amount') > 0 && isActive('Coupon') ? $order->couponRedeems->sum('discount_amount') : 0;
                    @endphp
                    <tr>
                        <th>{{ __('Sub Total') }} :</th>
                        <td>{{ formatNumber(($order->total + $order->other_discount_amount + $couponOffer) - ($order->shipping_charge + $order->tax_charge), optional($order->currency)->symbol) }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Fees') }} :</th>
                        <td>{{ formatNumber(($feeTotal), optional($order->currency)->symbol) }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Shipping') }} {{ !is_null($order->shipping_title) ? "( ". $order->shipping_title . " )" : null }} :</th>
                        <td>{{ formatNumber($order->shipping_charge, optional($order->currency)->symbol) }}</td>
                    </tr>
                    @php 
                        $totalCustomTax =  $order->updatedCustomTaxFee($order, true);
                    @endphp
                    <tr>
                        <th>{{ __('Tax') }} :</th>
                        <td>{{ formatNumber($order->tax_charge + $customTaxTotal + $totalCustomTax, optional($order->currency)->symbol) }}</td>
                    </tr>
                    @if($couponOffer > 0)
                    <tr>
                        <th>{{ __('Coupon offer') }} :</th>
                        <td>-{{ formatNumber($couponOffer, optional($order->currency)->symbol) }}</td>
                    </tr>
                    @endif

                    @if($order->other_discount_amount > 0)
                    <tr>
                        <th>{{ __('Discount') }} :</th>
                        <td>{{ formatNumber($order->other_discount_amount, optional($order->currency)->symbol) }}</td>
                    </tr>
                    @endif
                
                    <tr class="text-info">
                        <td>
                            <hr />
                            <h5 class="text-primary m-r-10">{{ __('Grand Total') }} :</h5>
                        </td>
                        <td>
                            <hr />
                            <h5 class="text-primary">{{ formatNumber($order->total + $customTaxTotal + $totalCustomTax + $feeTotal, optional($order->currency)->symbol) }}</h5>
                        </td>
                    </tr>
                </tbody>
            </table>
            @if($order->orderStatus->payment_scenario != 'paid')
            <div class="float-left" id="custom_item_btn">
                <div class="row">
                    <div class="col-md-4">
                        <a class="add-files-button" id="add_item">{{ __('Add item') }}</a>
                    </div>
                    <div class="col-md-4">
                        <a class="add-files-button" id="apply_coupon" data-label="Coupon" data-bs-toggle="modal" data-bs-target="#coupon_modal">{{ __('Apply Coupon') }}</a>
                    </div>
                </div>
            </div>
            <div class="float-right display_none" id="custom_item_list">
                <div class="row">
                    <div class="col-md-2">
                        <a href="javascript:void(0)" id="add_product" class="add-files-button" data-label="Delete" data-bs-toggle="modal" data-bs-target="#product_modal">{{ __('Product') }}</a>
                    </div>
                    <div class="col-md-3">
                        <a href="javascript:void(0)" id="add_fee" class="add-files-button" data-label="Fee" data-bs-toggle="modal" data-bs-target="#fee_modal">{{ __('Fee/shipping') }}</a>
                    </div>
                    <div class="col-md-2">
                        <a href="javascript:void(0)" id="add_tax" class="add-files-button" data-label="Fee" data-bs-toggle="modal" data-bs-target="#add_tax_modal" class="add-files-button">{{ __('Add Tax') }}</a>
                    </div>
                    <div class="col-md-2">
                        <a class="add-files-button" id="cancel_btn">{{ __('Cancel') }}</a>
                    </div>
                    <div class="col-md-2">
                        <a href="javascript:void(0)" id="save_custom" class="add-files-button">{{ __('Save') }}</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
