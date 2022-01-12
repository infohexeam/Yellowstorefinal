@extends('admin.layouts.app')
@section('content')
<div class="container">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>
            <div class="card-body">
               @if ($message = Session::get('status'))
               <div class="alert alert-success">
                  <p>{{ $message }}</p>
               </div>
               @endif
            </div>
            <div class="col-lg-12">
               @if ($errors->any())
               <div class="alert alert-danger">
                  <strong>Whoops!</strong> There were some problems with your input.<br><br>
                  <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif

                 <input type="hidden" class="form-control" name="order_id" value="{{$order->order_id}}">

                   <div class="row" >
                     <div class="col-md-12">
                        <div class="card">
                           <div class="card-header">
                              <div class="card-title">Order Details</div>
                           </div>
                           <div class="card-body">
                         <div class="table-responsive">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                  <tr>
                                    <td><strong>Order Date :</strong> {{ \Carbon\Carbon::parse(@$order->created_at)->format('M d, Y')}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Order Number :</strong> {{@$order->order_number}}</td>
                                 </tr>
                                   <tr>
                                    <td><strong>Order Status :</strong> {{ @$order->status['status']}}</td>
                                 </tr>

                                 {{-- <tr>
                                    <td><strong>Quantity:</strong> {{$order->quantity}}</td>
                                 </tr> --}}

                              </tbody>

                           </table>
                           </div>
                        </div>{{-- card body end --}}
                     </div><!-- COL END -->
                  </div>
               </div>


               @if (isset($order->delivery_boy['delivery_boy_name']))
                <div class="row" >
                     <div class="col-md-12">
                        <div class="card">
                           <div class="card-header">
                              <div class="card-title">Delivery Boy Details</div>
                           </div>
                           <div class="card-body">
                         <div class="table-responsive">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Delivery Name :</strong> {{@$order->delivery_boy['delivery_boy_name']}}</td>
                                 </tr>

                                 <tr>
                                    <td><strong>Delivery Phone :</strong> {{@$order->delivery_boy['delivery_boy_mobile']}}</td>
                                 </tr>

                                <tr>
                                    <td><strong>Delivery Date :</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y')}}</td>
                                 </tr>
                              </tbody>
                           </table>
                           </div>
                        </div>{{-- card body end --}}
                     </div><!-- COL END -->
                  </div>
               </div>
               @endif


                <div class="row" >
                     <div class="col-md-12">
                        <div class="card">
                           <div class="card-header">
                              <div class="card-title">Store Details</div>
                           </div>
                           <div class="card-body">
                         <div class="table-responsive">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Store Name :</strong> {{@$order->store['store_name']}}</td>
                                 </tr>

                                 <tr>
                                    <td><strong>Store Contact Person :</strong> {{@$order->store['store_contact_person_name']}}</td>
                                 </tr>
                                     <tr>
                                    <td><strong>Store Phone :</strong> {{@$order->store['store_contact_person_phone_number']}}</td>
                                 </tr>

                              </tbody>
                           </table>
                           </div>
                        </div>{{-- card body end --}}
                     </div><!-- COL END -->
                  </div>
               </div>

               @if (isset($order->subadmin['name']))
               <div class="row" >
                     <div class="col-md-12">
                        <div class="card">
                           <div class="card-header">
                              <div class="card-title">Sub Admin Details</div>
                           </div>
                           <div class="card-body">
                         <div class="table-responsive">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Sub Admin Name :</strong> {{@$order->subadmin['name']}}</td>
                                 </tr>
                                   <tr>
                                    <td><strong>Sub Admin Phone :</strong> {{@$order->subadmin->subadmins['phone']}}</td>
                                 </tr>

                              </tbody>
                           </table>
                           </div>
                        </div>{{-- card body end --}}
                     </div><!-- COL END -->
                  </div>
               </div>
               @endif


                  <div class="row">
                     <div class="col-md-12">
                        <div class="card">
                           <div class="card-header">
                              <div class="card-title">Customer Details</div>
                           </div>
                           <div class="card-body">
                         <div class="table-responsive">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Name :</strong> {{@$order->customer['customer_first_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Mobile :</strong> (+91){{ @$order->customer['customer_mobile_number']}}</td>
                                 </tr>
                                  {{-- <tr>
                                    <td><strong>Location :</strong> {{ @$order->customer['customer_location']}}</td>
                                 </tr> --}}
                                 {{-- <tr>
                                    <td><strong>Address :</strong>
                                       
                                       {{ @$order->customer['customer_address']}}
                                    
                                       {{@$order->customerAddress['address']}} <br>
                                       @if (isset($order->customerAddress['pincode']))
                                          Pincode: {{@$order->customerAddress['pincode']}}<br>
                                       @endif

                                       @if (isset($order->customerAddress['phone']))
                                       Phone: {{@$order->customerAddress['phone']}}<br>
                                       @endif
                                    
                                    </td>
                                 </tr> --}}
                              </tbody>

                           </table>
                           </div>
                        </div>
                     </div><!-- COL END -->
                  </div>
               </div>
                  <div class="row">
                      <div class="col-md-6">
                        <div class="card">
                           <div class="card-header">
                              <div class="card-title">Delivery Address </div>
                           </div>
                           <div class="card-body">
                         <div class="table-responsive">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Payment Mode :</strong> {{@$order->payment_type['payment_type']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Address :</strong> 
                                       {{ @$order->customer['customer_address']}}
                                    
                                       {{@$order->customerAddress['address']}} <br>
                                       @if (isset($order->customerAddress['pincode']))
                                          Pincode: {{@$order->customerAddress['pincode']}}<br>
                                       @endif

                                       @if (isset($order->customerAddress['phone']))
                                       Phone: {{@$order->customerAddress['phone']}}<br>
                                       @endif                                    
                                    </td>
                                 </tr>

                              </tbody>

                           </table>
                           </div>
                        </div>
                     </div>
                     </div><!-- COL END -->

                      {{-- <div class="col-md-6">
                        <div class="card">
                           <div class="card-header">
                              <div class="card-title">Shipping Address</div>
                           </div>
                           <div class="card-body">
                         <div class="table-responsive">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Address :</strong> {{$order->shipping_address}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Landmark :</strong>{{ $order->shipping_landmark}}</td>
                                 </tr>

                              </tbody>

                           </table>
                           </div>
                        </div>
                     </div><!-- COL END -->
                  </div> --}}

               </div>
            </div>
            <br>
            <div class="col-md-12">
            <div class="table-responsive push">
                                
                                 <table class="table table-bordered table-hover mb-0 text-nowrap">
                                 <thead>
                                    <tr>
                                       <td>Item Name</td>
                                       <td>Qty</td>
                                       <td>Rate</td>
                                       <td>Discount Amount</td>
                                       <td>Tax</td>
                                       <td>Total</td>
                                       <!--<td></td>-->
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach ($order_items as $order_item)
                                    @php
                                       // dd($order_item);
                                    @endphp
                                       <tr>
                                          <td>
                                              <table>
                                                <tr>
                                                   <td><img src="{{asset('/assets/uploads/products/base_product/base_image/'.@$order_item->product_varient->product_varient_base_image)}}"  width="50" ></td>
                                                   <td>{{@$order_item->product->product_name}}
                                                      @if (isset($order_item->product_varient_id) && $order_item->product_varient_id != 0 )
                                                      -
                                                      {{ @$order_item->product_varient->variant_name }}
                                                      @endif
                                                      <td>
                                                </tr>
                                              </table>
                                          </td>
                                          <td>{{@$order_item->quantity}} </td>
                                          <td>{{@$order_item->unit_price}} </td>
                                          <td>
                                             @if (isset($order_item->discount_amount))
                                             {{@$order_item->discount_amount}} 
                                                @else
                                                0
                                             @endif
                                           </td>
                                          <td>{{@$order_item->tax_amount}} </td>
                                          <td>{{@$order_item->total_amount}} </td>
                                          <!--<td>-->
                                          <!--   <input type="hidden" name="order_item_id[{{@$order_item->order_item_id}}]" value="{{ @$order_item->order_item_id }}">-->
                                          <!--   <input type="hidden" name="product[{{@$order_item->order_item_id}}]" value=0>-->
                                          <!--   <input type="checkbox" @if ($order_item->tick_status)-->
                                          <!--   checked-->
                                          <!--   @endif name="product[{{@$order_item->order_item_id}}]" value=1>-->
                                          <!--</td>-->
                                       </tr>
                                    @endforeach

                                 </tbody>
                               </table>
                                
                                
                              </div>
                              <br>
                           </div>

                  <center>
                     <button type="button" class="btn btn-cyan" onclick="history.back()">Cancel</button>

                           </center>
                        </br>
             {{--   </div>
            </div> --}}
         </div>
      </div>
   </div>
   @endsection
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript">
$('#print').click(function(){
$('#print'). hide();
});

</script>

