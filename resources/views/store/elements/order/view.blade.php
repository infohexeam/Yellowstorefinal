
@extends('store.layouts.app')
@section('content')
@php
use App\Models\admin\Trn_StoreDeliveryTimeSlot;
   
@endphp
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
                                          <td><strong>Order Date: </strong> </td> <td>{{ \Carbon\Carbon::parse(@$order->created_at)->format('d M Y')}}</td>
                                       </tr>
                                       <tr>
                                          <td><strong>Customer Name: </strong> </td> <td>{{ @$order->customer['customer_first_name']}} {{ @$order->customer['customer_last_name']}} </td>
                                       </tr>
                                 
                                       <tr>
                                          <td> <h3> <strong>Total Amount: </strong> </h3>  </td> <td> <h3> <i class="fa fa-inr"></i> {{ @$order->product_total_amount }} </h3></td>
                                       </tr>
                                       
                                       <tr>
                                          <td><strong>Delivery Charge: </strong> </td> <td> @if(isset($order->delivery_charge)){{ @$order->delivery_charge}} @else 0.00 @endif</td>
                                       </tr>
                                       
                                       <tr>
                                          <td><strong>Packing  Charge: </strong> </td> <td> @if(isset($order->packing_charge)){{ @$order->packing_charge}} @else 0.00 @endif</td>
                                       </tr>
                                        <tr>
                                       <td>
                                       <strong>Redeemed Admin Amount By wallet: </strong> </td> <td> @if(isset($order->amount_reduced_by_rp))
                                                   {{ @$order->amount_reduced_by_rp}} ({{ @$order->reward_points_used}} points )
                                                   @else
                                                   0.00
                                                   @endif</td>
                                       </tr>
                                       <tr>
                                       <td>
                                       <strong>Redeemed Store Amount By Wallet: </strong> </td> <td> @if(isset($order->amount_reduced_by_rp_store))
                                                   {{ @$order->amount_reduced_by_rp_store}} ({{ @$order->reward_points_used_store}} points )
                                                   @else
                                                   0.00
                                                   @endif</td>
                                       </tr>
                                       <tr>
                                       <td>
                                       <strong>Redeemed  Amount By Coupon: </strong> </td> <td> @if(isset($order->amount_reduced_by_coupon)) {{ @$order->amount_reduced_by_coupon }} @else 0.00 @endif</td>
                                       </tr>
                                       
                                       <tr>
                                          <td><strong>Payment Mode: </strong> </td> <td>{{ @$order->payment_type['payment_type']}}</td>
                                       </tr>
                                       <tr>
                                          <td><strong>Delivery Option: </strong> </td> 
                                          <td>
                                          @if($order->delivery_option==NULL)
                                             @if (isset($order->time_slot) && ($order->time_slot != 0)) 
                                             @php
                                                 $deliveryTimeSlot = Trn_StoreDeliveryTimeSlot::withTrashed()->find($order->time_slot);
                                             @endphp
                                                Time Slot Delivery ({{ @$deliveryTimeSlot->time_start . "-" . @$deliveryTimeSlot->time_end }})
                                             @else
                                                Immediate Delivery
                                             @endif
                                          @else
                                             @if($order->delivery_option==1)
                                               Immediate Delivery @if($order->immediate_store_text)({{@$order->immediate_store_text}}) @endif
                                             @endif
                                               @if($order->delivery_option==0)
                                               @if (isset($order->time_slot) && ($order->time_slot != 0)) 

                                                @php
                                                 $deliveryTimeSlot = Trn_StoreDeliveryTimeSlot::withTrashed()->find($order->time_slot);
                                             @endphp
                                                Time Slot Delivery ({{ @$deliveryTimeSlot->time_start . "-" . @$deliveryTimeSlot->time_end }})
                                             @endif
                                             @endif
                                             @if($order->delivery_option==2)
                                                @php
                                                 $deliveryTimeSlot = Trn_StoreDeliveryTimeSlot::withTrashed()->find($order->time_slot);
                                             @endphp
                                                Time Slot Delivery ({{ @$deliveryTimeSlot->time_start . "-" . @$deliveryTimeSlot->time_end }})
                                             @endif
                                             @if($order->delivery_option==3)
                                               Future Delivery<br>Expected Delivery Date:{{date('d-M-Y',strtotime(@$order->future_delivery_date))}}
                                               @php
                                             $deliveryTimeSlot = Trn_StoreDeliveryTimeSlot::withTrashed()->find($order->time_slot);
                                             @endphp
                                             <br>
                                               (Slot: {{ @$deliveryTimeSlot->time_start . "-" . @$deliveryTimeSlot->time_end }})
                                             @endif
                                             

                                          @endif
                                          </td>
                                          </tr>
                                          <tr>
                                          <td><strong>Delivery Type:</strong></td>
                                          <td>
                                          @if(@$order->is_collect_from_store==NULL||@$order->is_collect_from_store==0)
                                           Pay After Delivery
                                          @else
                                           Collect From Store
                                          @endif
                                          </td>
                                       </tr>

                                        @if(@$order->order_type == 'POS')
                                       <tr>
                                          <td><strong>Processed By: </strong> </td> <td> {{ @$order->storeadmin['admin_name'] }} </td>
                                       </tr>
                                       @endif

                                         <tr>
                                          <td><strong>Order Type: </strong> </td> <td>{{ @$order->order_type}}</td>
                                       </tr>

 @if(@$order->order_type != 'POS')
                                          <tr>
                                          <td><strong>Order Status: </strong> </td> <td>{{ @$order->status->status}} @if(@$order->status->status_id == 9) ( {{ \Carbon\Carbon::parse(@$order->delivery_date)->format('d-m-Y')}} {{ @$order->delivery_time }}  ) @endif </td>
                                       </tr>
@else
 <tr>
                                          <td><strong>Order Status: </strong> </td> <td>{{ @$order->status->status}} @if(@$order->status->status_id == 9) @endif </td>
                                       </tr>
@endif
                              @if($order->is_collect_from_store!=1)
                                       @php
                                       $oredrAddr = \DB::table('trn_customer_addresses')->where('customer_address_id',$order->delivery_address)->first();
                                     @endphp
                                     <tr>
                                       <td>   <strong>Delivery Address :</strong> </td> 
                                       <td> 
                                       
                                        @if(@$order->order_type != 'POS')

                                        {{ @$oredrAddr->name}} <br/> {{ @$oredrAddr->address}}

                                          @if (isset($order->place))
                                          {{ @$order->place }} ,
                                          @endif
      
                                          @if (isset($order->customerAddress->districtFunction->district_name))
                                          {{  @$order->customerAddress->districtFunction->district_name }} ,
                                          @endif
      
                                          @if (isset($order->customerAddress->stateFunction->state_name))
                                          {{  @$order->customerAddress->stateFunction->state_name }} ,
                                          @endif
      
                                           @if (isset($order->customerAddress->stateFunction->country->country_name))
                                           {{  @$order->customerAddress->stateFunction->country->country_name }}
                                           @endif
                                           <br>
                                           {{ @$oredrAddr->pincode}} <br> {{ @$oredrAddr->phone}} 
                                           
                                           @else
                                           ---
                                            @endif


                                       </td>
                                     </tr>
                                    @endif
                                     
                                    
                                      
                                    </tbody>
                                 </table>
                              </div>
                           </div>{{-- card body end --}}
                     </div><!-- COL END -->
                  </div>

                      @if (isset($order->delivery_boy['delivery_boy_name']))
                  
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
                                    <td><strong>Delivery Boy Name :</strong> {{@$order->delivery_boy['delivery_boy_name']}}</td>
                                 </tr>

                                 <tr>
                                    <td><strong>Delivery Phone :</strong> {{@$order->delivery_boy['delivery_boy_mobile']}}</td>
                                 </tr>
                           @if($order->status_id!=5)
                                <tr>
                                    <td><strong>Delivery Date :</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y')}}</td>
                                 </tr>
                           @endif
                              </tbody>
                           </table>
                           </div>
                        </div>{{-- card body end --}}
                     </div><!-- COL END -->
                  </div>
               @endif

{{-- 
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
                                    <td><strong>Sub Admin Name :</strong>
                                       @if(isset($order->store->subadmin->name))
                                       {{ @$order->store->subadmin->name}}
                                       @else
                                          ---
                                       @endif
                                    </td>
                                 </tr>
                                   <tr>
                                    <td><strong>Sub Admin Phone :</strong> 
                                       @if(isset($order->store->subadmin->phone))
                                       {{ @$order->store->subadmin->phone}}
                                       @else
                                          ---
                                       @endif
                                 </tr>

                              </tbody>
                           </table>
                           </div>
                        </div>
                     </div>
                  </div> --}}
                  @if($order->service_booking_order != 1)

                  @endif

                  @if($order->service_booking_order != 1)
                  
                  
                
               </div>

                  <div  class="row">
                     <div class="col-md-12">
                        <div class="card">
                           <div class="card-body">
                              <div style="background-color:#f1f1f9;" class="table-responsive">
                               <table class="table table-striped table-bordered text-nowrap w-100">
                                 <thead>
                                    <tr>
                                       <td>Sl No.</td>
                                       <td>Item</td>
                                       <td>Qty</td>
                                       <td>MRP</td>
                                       <td>Sale<br>Price</td>
                                       <td>Discount<br>Amount</td>
                                       <td>Tax %</td>
                                       <td>Tax Details</td>
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
                                       $i=1;
                                    @endphp
                                    @foreach ($order_items as $order_item)
                                       <tr>
                                       <td>{{$i++}}
                                          <td>
                                             
                                             @if(@$order_item->product_name==NULL)
                                             <img src="{{asset('/assets/uploads/products/base_product/base_image/'.@$order_item->product_varient->product_varient_base_image)}}"  width="50" >
                                             <br>
                                               {{ @$order_item->product_varient->variant_name }}
                                             @else
                                             <img src="{{asset('/assets/uploads/products/base_product/base_image/'.@$order_item->product_image)}}"  width="50" >
                                             <br>
                                               {{ @$order_item->product_name }}
                                             @endif
                                            
                                             @if(@$order->order_type != 'POS')
                                             @if($order_item->is_timeslot_product)
                                             @if($order_item->is_timeslot_product==1)
                                             Product was available between
                                             {{date('g:i A',strtotime($order_item->time_start))}}-{{date('g:i A',strtotime($order_item->time_end))}}
                                             @endif
                                             @endif
                                             @endif
                                             
                                             {{-- {{@$order_item->product->product_name}}   
                                             @if (isset($order_item->product_varient_id) && $order_item->product_varient_id != 0 )
                                             @if (@$order_item->product->product_name != @$order_item->product_varient->variant_name )
                                                @if(strlen($order_item->product->product_name.$order_item->product_varient->variant_name) < 15)
                                                -
                                                {{ @$order_item->product_varient->variant_name }}
                                                @else

                                                <br>

                                                {{ @$order_item->product_varient->variant_name }}

                                                @endif

                                              @endif
                                           
                                           
                                           @endif --}}
                                           </td>
                                          <td>{{@$order_item->quantity}} </td>
                                          <td>{{ @$order_item->mrp }}</td>
                                          <td>{{ @$order_item->unit_price}} </td>
                                 
                                           <td>
                                              @php
                                                 //$discountAmt = $order_item->quantity * (@$order_item->product_varient->product_varient_price - @$order_item->product_varient->product_varient_offer_price);
                                                $discountAmt=@$order_item->quantity*@$order_item->discount_amount ;
                                              @endphp
                                            {{ number_format((float)$discountAmt, 2, '.', '') }}  

                                             </td>
                                 
                                             @php
                                            $tax_info = \DB::table('mst_store_products')
                                             ->join('mst__taxes','mst__taxes.tax_id','=','mst_store_products.tax_id')
                                             ->where('mst_store_products.product_id', $order_item->product_id)
                                             ->select('mst__taxes.tax_id','mst__taxes.tax_name','mst__taxes.tax_value')
                                             ->first();  
                                              $tax_info->tax_value=$order_item->tax_value;
                                             $tax_info->tax_id=$order_item->tax_id; 
                                             $tval  = $order_item->unit_price * @$order_item->quantity;
                                             $tTax = $order_item->quantity * (@$order_item->unit_price * @$tax_info->tax_value / (100 + @$tax_info->tax_value));
                                             $orgCost =  $order_item->quantity * (@$order_item->unit_price * 100 / (100 + @$tax_info->tax_value));
                                             $Tot = $tTax + $orgCost;
                                          @endphp
                                        
                                          @php
                                            
                                             @$t_val = ($tax_info->tax_value * $tval) * 0.01 ;
                                             $splitdata = \DB::table('trn__tax_split_ups')->where('tax_id',@$tax_info->tax_id)->get();
                                               // dd($splitdata);
                                          @endphp
                                    
                                           <td>@if(isset($tax_info->tax_value)){{@$tax_info->tax_value}} @else No tax @endif</td>
                                         <td>
                                          <table style="line-height: 1px; font-size: 12px;">
                                                @foreach ($splitdata as $item)
                                                @if(@$tax_info->tax_value == 0 || !isset($tax_info->tax_value))
                                                @php
                                              $tax_info->tax_value = 1;
                                                  
                                                @endphp   
                                                @endif         
                                                <tr>
                                                   <td  style="line-height: normal;border: none;padding: 0;font-size: medium;">
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
                               @if(count($order_items) == 0)
                                 <p style="text-align: center;" >No data found...</p>
                               @endif
                              <div>
                           <div>   
                             
                        </div>
                     </div>
                  </div>
                  <br>
                  
                
                </div>

               @if(($order->order_type == 'APP') && ($order->payment_type_id == 2))
               @php
               $c = 0;
               @endphp
               @if($payments!=NULL)
               @forelse($payments as $payment)
               @if($c == 0)
               @php
               $c = 1;
               @endphp
                <div class="card">
                  <div class="card-body">
                    
                       <h5>Payment Information</h5>
                    <table class="table table-bordered text-nowrap w-100">
                       <tr>
                          <td>
                            Payment Mode:
                          </td>
                          <td>
                             {{ $payment->paymentMode??'Online' }}
                          </td>
                       </tr>
    
                       <tr>
                          <td>
                             Reference Id:
                          </td>
                          <td>
                             {{ @$payment->referenceId }}
                          </td>
                       </tr>
    
                       <tr>
                          <td>
                             Status:
                          </td>
                          <td>
                             {{ @$payment->txStatus }}
                          </td>
                       </tr>
    
                       <tr>
                          <td>
                             Txn Time:
                          </td>
                          <td>{{ \Carbon\Carbon::parse(@$payment->created_at)->format('Y-m-d H:i:s')}}</td>
    
                       </tr>
    
    
                    </table>
                    @if(@$payment->paymentRole == 1)
                <h5>Payment Split Information @if(@$payment->paymentRole == 1) for Store @else for Admin @endif</h5>
                <table class="table table-bordered text-nowrap w-100">
                   <tr>
                      <td>
                         Split Amount:
                      </td>
                      <td>
                         {{ @$payment->splitAmount }}
                      </td>
                   </tr>

                   <tr>
                      <td>
                         Split Charge:
                      </td>
                      <td>
                         {{ @$payment->serviceCharge }}
                      </td>
                   </tr>

                   <tr>
                      <td>
                         Service Tax:
                      </td>
                      <td>
                         {{ @$payment->serviceTax }}
                      </td>
                   </tr>

                   <tr>
                      <td>
                         Settlement Amount:
                      </td>
                      <td>
                         {{ @$payment->settlementAmount }}
                      </td>
                   </tr>


                </table>
                @endif
               
                  </div>
                </div>
                @endif

               @empty
               @endforelse
               @endif

                @endif



                @else

                 <div class="col-md-12">
                    <div class="card">
                       <div class="card-body">
                       Service Details
                            <table class="table row table-borderless">
                                <tbody class="col-lg-12 col-xl-6 p-0">
                                    @php
                                        $serviceVarDetail = \DB::table('mst_store_product_varients')->where('product_varient_id',@$order->product_varient_id)->first();
                                        $serviceDetail = \DB::table('mst_store_products')->where('product_id',@$serviceVarDetail->product_id)->first();
                                        $addCus = \DB::table('trn_customer_addresses')->where('customer_address_id',@$order->delivery_address)->first();

                                    @endphp
                                   <tr>
                                      <td><strong>Service Name: </strong> </td> 
                                      <td> 
                                       {{@$serviceVarDetail->variant_name}}
                                         
                                          {{-- @if($serviceDetail->product_name != $serviceVarDetail->variant_name)
                                            {{$serviceDetail->product_name}} {{$serviceDetail->variant_name}}
                                          @else
                                            {{$serviceDetail->product_name}}
                                          @endif --}}
                                      </td>
                                   </tr>
                                    <tr>
                                      <td><strong>Service MRP: </strong> </td> 
                                      <td> 
                                       <i class="fa fa-inr"></i>{{@$order->service_mrp}}
                                         
                                          {{-- @if($serviceDetail->product_name != $serviceVarDetail->variant_name)
                                            {{$serviceDetail->product_name}} {{$serviceDetail->variant_name}}
                                          @else
                                            {{$serviceDetail->product_name}}
                                          @endif --}}
                                      </td>
                                   </tr>
                                   <tr>
                                      <td><strong>Service Discount: </strong> </td> 
                                      <td> 
                                       <i class="fa fa-inr"></i>{{@$order->service_discount_amount}}
                                         
                                          {{-- @if($serviceDetail->product_name != $serviceVarDetail->variant_name)
                                            {{$serviceDetail->product_name}} {{$serviceDetail->variant_name}}
                                          @else
                                            {{$serviceDetail->product_name}}
                                          @endif --}}
                                      </td>
                                   </tr>
                                    <tr>
                                      <td><strong>Service Tax Percentage: </strong> </td> 
                                      <td> 
                                       {{@$order->service_tax_value}}
                                         
                                          {{-- @if($serviceDetail->product_name != $serviceVarDetail->variant_name)
                                            {{$serviceDetail->product_name}} {{$serviceDetail->variant_name}}
                                          @else
                                            {{$serviceDetail->product_name}}
                                          @endif --}}
                                      </td>
                                   </tr>
                                   <tr>
                                      <td><strong>Service Tax Amount: </strong> </td> 
                                      <td> 
                                       <i class="fa fa-inr"></i>{{@$order->service_tax_amount}}
                                         
                                          {{-- @if($serviceDetail->product_name != $serviceVarDetail->variant_name)
                                            {{$serviceDetail->product_name}} {{$serviceDetail->variant_name}}
                                          @else
                                            {{$serviceDetail->product_name}}
                                          @endif --}}
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