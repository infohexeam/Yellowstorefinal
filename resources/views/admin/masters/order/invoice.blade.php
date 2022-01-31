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
              
            </div>
            <div class="col-md-4">
            {{--  <a href="{{route('store.generate_invoice_pdf')}}" class="btn btn-info ">Generate Invoice</a> --}}
          </div>
            <div id="div_print"><br><br><br><br>
            <div class="col-lg-12">
              
               
                 <input type="hidden" class="form-control" name="order_id" value="{{$order->order_id}}">
                 
                 <div class="col-md-12">
                 <div class="row">
                                <div class="col-md-6 text-left">
                                    <p class="h3">Invoice From:</p>
                                      @php
                                        $invoice_data = \DB::table('trn_order_invoices')->where('order_id',$order->order_id)->first();
                                       $store_data = \DB::table('mst_stores')->where('store_id',$order->store_id)->first();
                                       @endphp
                                    <p class="h5">{{ @$store_data->store_name }}</p>
                                    <address>
                                    <h6>Invoice Number : {{@$invoice_data->invoice_id}}</h6>
                                    <h6>Invoice Date : {{$changeDate = date("d-m-Y", strtotime( @$invoice_data->invoice_date))  }}</h6>
                                    <div>
                                       {{ @$store_data->store_primary_address }} <br>
                                       Phone: {{ @$store_data->store_mobile }} <br>

                                    </div>
                                    
                                    </address>
                                 </div>

                                 <div class="col-md-6 text-right">
                                    <p class="h3">Invoice To:</p>

                                      @php
                                       $oredrAddr = \DB::table('trn_customer_addresses')->where('customer_address_id',$order->delivery_address)->first();
                                       @endphp

 
                                    <address>
                                    <h5> {{@$order->customer['name']}} </h5>
                                   <div> <br>
                                    {{ @$oredrAddr->name}}{{ @$oredrAddr->address}}
                                    {{ @$oredrAddr->pincode}} <br> {{ @$oredrAddr->phone}} 
                                   </div>
                                   
                                    </address>
                                 </div>
                              </div>
                  </div>
              
            <br>
                  <div class="col-md-12">
                  <div class="table-responsive push">
                        <table class="table table-bordered table-hover mb-0 text-nowrap">
                          <thead>
                                    <tr>
                                       <td>Item<br>Name</td>
                                       <td>Qty</td>
                                       <td>Rate</td>
                                       <td>Subtotal</td>
                                       <td>Discount<br>Amount</td>
                                       <td class="text-center">Tax Details</td>
                                       {{-- <td>Tax<br>Name</td> --}}
                                       {{-- <td>Tax<br>Percentage</td> --}}
                                       <td>Tax<br>Amount</td>
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
                                             @if (strlen($order_item->product->product_name. " ".$order_item->product_varient->variant_name) < 22)
                                                {{@$order_item->product->product_name}}   
                                                   @if (isset($order_item->product_varient_id) && $order_item->product_varient_id != 0 )
                                                      @if($order_item->product->product_name != $order_item->product_varient->variant_name)
                                                            - {{ @$order_item->product_varient->variant_name }}
                                                      @endif
                                                   @endif
                                             @else
                                             {{@$order_item->product->product_name}} - <br> 
                                             {{ @$order_item->product_varient->variant_name }}

                                             @endif
                                          </td>
                                          <td>{{@$order_item->quantity}} </td>
                                          <td>{{@$order_item->unit_price}} </td>
                                          <td>
                                             @php
                                                 $tval  = $order_item->unit_price * @$order_item->quantity;
                                             @endphp
                                             {{@$order_item->unit_price * @$order_item->quantity}} 
                                          </td>
                                          <td>{{@$order_item->discount_amount}} </td>
                                          @php
                                             $tax_info = \DB::table('mst_store_products')
                                             ->join('mst__taxes','mst__taxes.tax_id','=','mst_store_products.tax_id')
                                             ->where('mst_store_products.product_id', $order_item->product_id)
                                             ->select('mst__taxes.tax_id','mst__taxes.tax_name','mst__taxes.tax_value')
                                             ->first();
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
                                                    $stax = ($item->split_tax_value * $t_val) / $tax_info->tax_value; 
                                                @endphp
                                             {{ $item->split_tax_name }} - {{ $item->split_tax_value }}%
                                             
                                           -  {{ number_format((float)$stax, 2, '.', '') }}  

                                                   </td>
                                                </tr>
                                                @endforeach
                                             </table>
                                          </td>
                                         
                                          <td> 
                                             @if (isset($tax_info->tax_value))
                                             {{ @$t_val }}
                                             @endif
                                          </td>
                                          <td>{{@$order_item->total_amount}} </td>
                                          
                                       </tr>
                                       @php
                                          $dis_amt =  $dis_amt + @$order_item->discount_amount;
                                          $single_subtotal = @$order_item->unit_price * @$order_item->quantity;
                                          $subtotal = $subtotal + $single_subtotal; 
                                          $tax_amount = $tax_amount + $t_val ; 
                                       @endphp
                                    @endforeach
                                    
                                    <tr>
                                       <td colspan="7" class=" text-right">Sub Total</td>
                                       <td class=" h4"> {{ @$subtotal }} </td>
                                    </tr>
                                    <tr>
                                       <td colspan="7" class=" text-right">Discount Amount</td>
                                       <td class=" h4"> {{ @$dis_amt }}</td>
                                    </tr>
                                    <tr>
                                       <td colspan="7" class=" text-right">Tax Amount</td>
                                       <td class=" h4"> {{ @$tax_amount }}</td>
                                    </tr>
                                  
                                    

                                    @if(@$order->order_type == 'APP')
                                    
                                    @if(($order->reward_points_used != null) || ($order->reward_points_used != 0))
                                    
                                        <!--<tr>-->
                                        <!--   <td colspan="6" class=" text-right">Reward point used</td>-->
                                        <!--   <td class=" h4">  </td>-->
                                        <!--</tr>-->
                                    
                                        <tr>
                                           <td colspan="7" class=" text-right">Reward point amount</td>
                                           <td class=" h4"> {{ @$order->amount_reduced_by_rp}} ({{ @$order->reward_points_used}} points) </td>
                                        </tr>
                                        
                                    
                                    @endif
                                    
                                    <tr>
                                       <td colspan="7" class=" text-right">Packing Charge</td>
                                       <td class=" h4"> 0 </td>
                                    </tr>


                                    <tr>
                                       <td colspan="7" class=" text-right">Delivery Charge</td>
                                       <td class="  h4">{{ @$order->delivery_charge}}</td>
                                    </tr>
                                    @endif
                                    @php
                                        @$gand_total = @$subtotal + @$tax_amount - @$dis_amt;
                                    @endphp

                                    <tr>
                                       <td colspan="7" class="font-weight-bold text-uppercase text-right">Grand Total</td>
                                       <td class="font-weight-bold  h4"><i class="fa fa-inr"></i> {{ @$order->product_total_amount }}</td>
                                       {{-- <td class="font-weight-bold  h4"><i class="fa fa-inr"></i> {{ @$gand_total }}</td> --}}
                                    </tr>


                                 </tbody>
                        </table>
                     </div> 
                  </div>
                              {{-- <br>
                              <h5 class="mt-6 ml-4">Tax Split Ups</h5>
                              <div class="col-md-8">
                                 <div class="table-responsive push">
                                 <table class="table table-bordered table-hover mb-0 text-nowrap">
                                   <thead>
                                      
                                      @php
                                          $tax_d = \DB::table('mst_store_products')
                                             ->join('trn_order_items','trn_order_items.product_id','=','mst_store_products.product_id')
                                               ->join('mst__taxes','mst__taxes.tax_id','=','mst_store_products.tax_id')
                                               ->where('trn_order_items.order_id', $order_id)
                                               ->select('mst__taxes.tax_id','mst__taxes.tax_name','mst__taxes.tax_value')
                                               ->get()->unique('tax_id');
                                            //   dd($tax_d);
                                      @endphp
                                      @foreach ($tax_d as $tax_s)
                                      @php
                                         $splitdata = \DB::table('trn__tax_split_ups')->where('tax_id',$tax_s->tax_id)->get();
                                         $stax = 0;
                                        @endphp
                                         <tr>
                                            <th><b>{{ $tax_s->tax_name }} {{ $tax_s->tax_value }}% </b></th>
                                            @foreach ($splitdata as $item)
                                            @php
                                               $spliteddata = \DB::table('mst_store_products')
                                               ->join('trn_order_items','trn_order_items.product_id','=','mst_store_products.product_id')
                                               ->where('mst_store_products.tax_id', $tax_s->tax_id)
                                               ->where('trn_order_items.order_id', $order_id)
                                               ->sum('trn_order_items.tax_amount');
                                            @endphp
                                            <td>

                                                {{ $item->split_tax_name }} {{ $item->split_tax_value }}%
                                                <br>
                                                @php
                                                    $stax = ($item->split_tax_value * $spliteddata) / $tax_s->tax_value; 
                                                @endphp
                                                {{ number_format((float)$stax, 2, '.', '') }}
                                               
                                             </td>
                                            @endforeach
                                         </tr>
                                      @endforeach
                                   </thead>
                                   <tbody>
                                   </tbody>
                                 </table>
                              </div>
                           </div> --}}
                           
                        </div>
                     </div>

                        <div class="card-footer text-right">
                           <a  href="{{ url('admin/order/list') }}" class="btn btn-cyan text-white " >Cancel</a>
                                

                              <button type="button" class="btn btn-info mb-1"  onClick="printdiv('div_print');"><i class="si si-printer"></i> Print Invoice</button>
                         
                     </div><!-- COL-END -->
                 
             
         </div>
      </div>
   </div>

   <script type="text/javascript">
        function printdiv(printpage) {
            var headstr = "<html><head><title></title></head><body>";
            var footstr = "</body>";
            var newstr = document.all.item(printpage).innerHTML;
            var oldstr = document.body.innerHTML;
            document.body.innerHTML = headstr + newstr + footstr;
            window.print();
            location.reload();
            return false;
        }
    </script>

   @endsection
  
   