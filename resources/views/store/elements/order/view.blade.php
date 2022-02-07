@extends('store.layouts.app')
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
                   <form action="{{route('store.update_order',$order->order_id)}}" method="POST">
                       @csrf
               
                 <input type="hidden" class="form-control" name="order_id" value="{{$order->order_id}}">

                  <div class="row" >
                     <div class="col-md-12">
                        <div class="card">
                           <div style="    justify-content: space-between;" class="card-header">
                              <div class="card-title">Order Details</div>
                                                           <!--<a  href="{{route('store.list_order')}}" class="btn btn-cyan float-right "  >Back</a>-->
                                                           <!--<a  href="{{ url('store/order/list') }}" class="btn btn-cyan float-right "  >Back</a>-->
                                                           <a  onclick="GoBackWithRefresh();return false;" class="btn btn-cyan float-right text-white "  >Back</a>

                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table row table-borderless">
                                    <tbody class="col-lg-12 col-xl-6 p-0">
                                       <tr>
                                          <td><strong>Order Number: </strong> </td> <td> {{$order->order_number}}</td>
                                       </tr>
                                       <tr>
                                          <td><strong>Order Date: </strong> </td> <td>{{ \Carbon\Carbon::parse(@$order->created_at)->format('d/m/Y')}}</td>
                                       </tr>
                                       <tr>
                                          <td><strong>Customer Name: </strong> </td> <td>{{ @$order->customer->customer_first_name}} {{ @$order->customer->customer_last_name}} </td>
                                       </tr>
                                 
                                       <tr>
                                          <td> <h3> <strong>Total Amount: </strong> </h3>  </td> <td> <h3> <i class="fa fa-inr"></i> {{ @$order->product_total_amount }} </h3></td>
                                       </tr>
                                         <tr>
                                          <td><strong>Order Type: </strong> </td> <td>{{ @$order->order_type}}</td>
                                       </tr>
                                       <tr>
                                          <td><strong>Payment Mode: </strong> </td> <td>{{ @$order->payment_type->payment_type}}</td>
                                       </tr>
                                       <tr>
                                          <td><strong>Processed By: </strong> </td> <td> -- </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>{{-- card body end --}}
                     </div><!-- COL END -->
                  </div>
                  @if($order->service_booking_order != 1)
                  
                  
                   <div class="col-md-2">
                   </div>
                   
                   
                   @if($order->status_id == '7')
                   <div class="col-md-4">
                        <div class="form-group">
                            <select disabled name="delivery_boy_id"  class="attr_value form-control" >
                              <!--<option value="">Select Delivery Boy</option>-->
                              @foreach ($delivery_boys as $data)
                                @if($order->delivery_boy_id == $data->delivery_boy_id)
                                 <option {{request()->input('delivery_boy_id',$order->delivery_boy_id) == $data->delivery_boy_id ? 'selected':''}} value="{{$data->delivery_boy_id}}">{{ $data->delivery_boy_name}}</option>
                                @endif
                              @endforeach
                            </select>
                        </div>
                   </div>
                   @endif
                   <div class=" @if($order->status_id  == '7') col-md-4 @else col-md-8  @endif">
                        <div class="form-group">
                            <select disabled name="status_id" class="attr_value form-control" >
                                 <option value="">Status</option>
                              @foreach ($status as $key)
                              <option {{request()->input('status_id',$order->status_id) == $key->status_id ? 'selected':''}} value=" {{ $key->status_id}} "> {{ $key->status}}</option>
                              @endforeach                            
                              </select>
                        </div>
                   </div>
                   <div class="col-md-2">
                   </div>
               </div>

                  <div  class="row">
                     <div class="col-md-12">
                        <div class="card">
                           <div class="card-body">
                              <div style="background-color:#f1f1f9;" class="table-responsive">
                               <table class="table table-striped table-bordered text-nowrap w-100">
                                 <thead>
                                    <tr>
                                       <td>Item<br>Name</td>
                                       <td>Qty</td>
                                       <td>MRP</td>
                                       <td>Sale Price</td>
                                       <td>Discount<br>Amount</td>
                                       <td class="text-center">Tax %</td>
                                       <td class="text-center">Tax Details</td>
                                       <td>Tax<br>Amount</td>
                                       <td>Subtotal</td>
                                       <td>Total</td>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @php
                                       $dis_amt = 0;
                                       $subtotal = 0;
                                       $tax_amount  = 0;
                                       $gand_total = 0;
                                       $tval = 0;
                                       $t_val = 0;
                                    @endphp
                                    @foreach ($order_items as $order_item)
                                       <tr>
                                          <td>
                                             <img src="{{asset('/assets/uploads/products/base_product/base_image/'.@$order_item->product_varient->product_varient_base_image)}}"  width="50" >
                                             <br>
                                             
                                             {{@$order_item->product->product_name}}   
                                             @if (isset($order_item->product_varient_id) && $order_item->product_varient_id != 0 )
                                             @if (@$order_item->product->product_name != @$order_item->product_varient->variant_name )
                                                -
                                              {{ @$order_item->product_varient->variant_name }}
                                              @endif
                                           
                                           
                                           @endif
                                           </td>
                                          <td>{{@$order_item->quantity}} </td>
                                           <td> {{ @$order_item->product_varient->product_varient_price }}</td>
                                           <td> {{ @$order_item->product_varient->product_varient_offer_price }}</td>
                                 
                                           <td>
                                              @php
                                                 $discountAmt = $order_item->quantity * (@$order_item->product_varient->product_varient_price - @$order_item->product_varient->product_varient_offer_price);
                                              @endphp
                                              {{@$discountAmt}} 
                                             </td>
                                 
                                           <td>
                                              @php
                                                $tax_info = \DB::table('mst_store_products')
                                                ->join('mst__taxes','mst__taxes.tax_id','=','mst_store_products.tax_id')
                                                ->where('mst_store_products.product_id', $order_item->product_id)
                                                ->select('mst__taxes.tax_id','mst__taxes.tax_name','mst__taxes.tax_value')
                                                ->first();  
                                                $tval  = $order_item->unit_price * @$order_item->quantity;
                                                $tTax = $order_item->quantity * (@$order_item->product_varient->product_varient_offer_price * @$tax_info->tax_value / (100 + @$tax_info->tax_value));
                                                $orgCost =  $order_item->quantity * (@$order_item->product_varient->product_varient_offer_price * 100 / (100 + @$tax_info->tax_value));
                                                $Tot = $tTax + $orgCost;
                                             @endphp

                                              {{@$tax_info->tax_value}} 
                                             
                                             </td>
                                        
                                          @php
                                            
                                             @$t_val = ($tax_info->tax_value * $tval) * 0.01 ;
                                             $splitdata = \DB::table('trn__tax_split_ups')->where('tax_id',$tax_info->tax_id)->get();
                                               // dd($splitdata);
                                          @endphp
                                          {{-- <td>
                                             {{ @$tax_info->tax_name }}
                                          </td>
                                          <td>
                                             {{@$tax_info->tax_value }}
                                          </td> --}}
                                          <td> 
                                             <table style="line-height: 1px; font-size: 12px;">
                                                @foreach ($splitdata as $item)
                                                @if(@$tax_info->tax_value == 0 || !isset($tax_info->tax_value))
                                                @php
                                              $tax_info->tax_value = 1;
                                                  
                                                @endphp   
                                                @endif         
                                                <tr>
                                                   <td>
                                                @php
                                                    $stax = ($item->split_tax_value * $tTax) / $tax_info->tax_value; 
                                                @endphp
                                             {{ $item->split_tax_name }} - {{ $item->split_tax_value }}%
                                             
                                           -  {{ number_format((float)$stax, 2, '.', '') }}  

                                                   </td>
                                                </tr>
                                                @endforeach
                                             </table>
                                          </td>
                                         
                                          <td> 
                                             @if (isset($tTax))
                                            {{ number_format((float)$tTax, 2, '.', '') }}  
                                             @endif
                                          </td>

                                          <td>
                                            {{ number_format((float)$orgCost, 2, '.', '') }}  
                                            
                                           </td>
                                           <td>
                                             {{ number_format((float)$Tot, 2, '.', '') }}  

                                           </td>
                                          
                                       </tr>
                                       @php
                                          $dis_amt =  $dis_amt + $discountAmt;
                                          $single_subtotal = @$order_item->unit_price * @$order_item->quantity;
                                          $subtotal = $subtotal + $orgCost; 
                                          $tax_amount = $tax_amount + $tTax ; 
                                       @endphp
                                    @endforeach
                                 </tbody>
                               </table>
                              <div>
                           <div>   
                              <h5>Payment Split Information</h5>
                              <table class="table table-bordered text-nowrap w-100">
                                 <tr>
                                    <td>
                                       Split Amount:
                                    </td>
                                    <td>
                                       {{ @$payments->splitAmount }}
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       Split Charge:
                                    </td>
                                    <td>
                                       {{ @$payments->serviceCharge }}
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       Service Tax:
                                    </td>
                                    <td>
                                       {{ @$payments->serviceTax }}
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       Settlement Amount:
                                    </td>
                                    <td>
                                       {{ @$payments->settlementAmount }}
                                    </td>
                                 </tr>


                              </table>
                        </div>
                     </div>
                  </div>
                  <br>
                  
                   <!--<div class="row">-->
                   <!--  <div class="col-md-12">-->
                   <!--        <div class="form-group">-->
                   <!--           <textarea name="order_note" placeholder="Note" class="form-control">{{ $order->order_note}}</textarea>-->
                   <!--        </div>-->
                   <!--  </div>-->
                   <!--</div>-->
                </div>

                @else

                 <div class="col-md-12">
                    <div class="card">
                       <div class="card-body">
                            <table class="table row table-borderless">
                                <tbody class="col-lg-12 col-xl-6 p-0">
                                    @php
                                        $serviceVarDetail = \DB::table('mst_store_product_varients')->where('product_varient_id',$order->product_varient_id)->first();
                                        $serviceDetail = \DB::table('mst_store_products')->where('product_id',$serviceVarDetail->product_id)->first();
                                        $addCus = \DB::table('trn_customer_addresses')->where('customer_address_id',$order->delivery_address)->first();

                                    @endphp
                                   <tr>
                                      <td><strong>Service: </strong> </td> 
                                      <td> 
                                          @if($serviceDetail->product_name != $serviceVarDetail->variant_name)
                                            {{$serviceDetail->product_name}} {{$serviceDetail->variant_name}}
                                          @else
                                            {{$serviceDetail->product_name}}
                                          @endif
                                      </td>
                                   </tr>
                                   <tr>
                                      <td><strong>Customer Phone: </strong> </td> 
                                      <td>{{ @$order->customer->customer_mobile_number}} <br> 
                                      {{ @$addCus->phone }} </td>
                                   </tr>
                                   @if($addCus)
                                   <tr>
                                       @php
                                       @endphp
                                      <td><strong>Address: </strong> </td> 
                                      <td>
                                          {{ @$addCus->name }}, {{ @$addCus->phone }} <br>
                                          {{ @$addCus->place }}, {{ @$addCus->address }} <br>
                                          
                                      </td>
                                   </tr>
                                   @endif
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @endif


                 

               
                  <br>
                  <center>
                     <!--<button type="submit"   class="btn btn-block btn-blue"  >Submit</button>-->
                     {{-- <a type="button" class="btn btn-cyan" onclick="history.back()">Cancel</a> --}}
                  </center>
               </br>
               </form>
              
          
         </div>
      </div>
      </div>
      </div>
   @endsection
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript">
$('#print').click(function(){
$('#print'). hide();
});


function GoBackWithRefresh(event) {
    if ('referrer' in document) {
        window.location = document.referrer;
        /* OR */
        //location.replace(document.referrer);
    } else {
        window.history.back();
    }
}
</script>