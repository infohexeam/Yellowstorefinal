


   <table  width="100%" border="0" cellspacing="0" cellpadding="0">
    {{-- <tr>
      <td colspan="2"><img src="logo.png" width="150"  /></td>
    </tr> --}}
    {{-- <tr>
      <td colspan="2"> </td>
    </tr> --}}
    <tr>
      <td width="49%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><table width="100%" border="0" style="margin-bottom: 10px;" cellspacing="0" cellpadding="0">
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:15px;">Invoice From</td>
            </tr>
            @php
              $invoice_data = \DB::table('trn_order_invoices')->where('order_id',$order->order_id)->first();
           @endphp
             <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"> {{ @$store_data->store_name }} </td>
            </tr>
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">Order Number: {{@$order->order_number}} </td>
            </tr>
  
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">Invoice Number: {{@$invoice_data->invoice_id}} <br> Invoice Date: {{$changeDate = date("d-m-Y", strtotime( @$order->created_at))  }}</td>
            </tr>
  
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"> 
                <div style="margin-top:5px;">
                  {{ @$store_data->store_primary_address }} <br>
                    @if (isset($store_data->place))
                  {{ @$store_data->place }} ,
                  @endif
  
                   @if (isset($store_data->town->town_name))
                   {{ @$store_data->town->town_name }} ,
                   @endif
  
                  @if (isset($store_data->district->district_name))
                  {{  @$store_data->district->district_name }} ,
                  @endif
  
                  @if (isset($store_data->state->state_name))
                  {{  @$store_data->state->state_name }} ,
                  @endif
  
                   @if (isset($store_data->country->country_name))
                   {{  @$store_data->country->country_name }}
                   @endif
                   <br>
                    Phone: {{ @$store_data->store_mobile }}   <br>
            GST : {{ @$store_data->gst }}
                   
                   <br>
                  <!--Phone: {{ @$store_data->store_mobile }} <br>-->
  
                

               </div>
              </td>
            </tr>
             <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"></td>
            </tr>
         
            </tr>
            </table>
          </td>
        </tr>
      </table></td>
      <td width="51%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
       
        <tr>
          <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:15px;" align="right">Invoice To</td>
        </tr>
        <!--<tr>-->
        <!--  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" align="right">{{@$order->customer['customer_first_name']}} {{@$order->customer['customer_last_name']}}</td>-->
        <!--</tr>-->
        <tr>
          <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" align="right"> 
              <div>
                  <br>
                  @if($order->is_collect_from_store!=1)
                  @if (isset($order->delivery_address))
                    @php 
                     $cAddr =  \DB::table('trn_customer_addresses')->where('customer_address_id',$order->delivery_address)->first();
                    @endphp
                    {{@$cAddr->name }}  <br> 
                    
                      {{@$order->customerAddress['address']}} <br>
  
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
  
                       Pincode: {{@$order->customerAddress['pincode']}}<br>
                       Phone: {{@$order->customerAddress['phone']}}<br>
                                       
                  @else
                @if(@$order->customer['customer_address']!=NULL)
                {{@$order->customer['customer_address']}} <br>
                Pincode: {{$order->customer['customer_pincode']}}<br>
                Phone: {{@$order->customer['customer_mobile_number']}}<br>
                @endif
                  @endif
                @else
                  {{@$order->customer['customer_first_name']}} {{@$order->customer['customer_last_name']??''}} <br>
                Pincode: {{$order->customer['customer_pincode']}}<br>
                Phone: {{@$order->customer['customer_mobile_number']}}<br>

                @endif
               
               
              </div>
          </td>
        </tr> 
       
      </table></td>
    </tr>
    <tr>
      <td  colspan="2"> </td>
    </tr>
      <tr>
          <td colspan="2">
          @if($order->service_booking_order!=1)
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" height="32" align="center">
                SL.<br>No
              </td>

              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" height="32" align="center">
                Item<br>Name
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Qty
              </td>
               <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                MRP
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Sale Price
              </td>
              {{-- <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Subtotal
              </td> --}}
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Discount<br>Amount		
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Tax %			
              </td>
              {{-- <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Tax Name
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Tax Percentage
              </td> --}}
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333; border-right:1px solid #333;" align="center">
                Tax <br> Details
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333; border-right:1px solid #333;" align="center">
                Tax <br> Amount
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Sub Total
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Total
              </td>
            </tr>
            @php
                $dis_amt = 0;
                $subtotal = 0;
                $tax_amount  = 0;
                $gand_total = 0;
                $tval = 0;
                $t_val = 0;
                $c = 0;
            @endphp
              @foreach ($order_items as $order_item)
                <tr>
                  <td  style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" height="32" align="center">{{ ++$c }}</td>

                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" height="32" align="center"> 
                  
                  @if(@$order_item->product_name==NULL)
                    {{ @$order_item->product_varient->variant_name }}
                  @else
                    {{ @$order_item->product_name }}
                  @endif
                  
                    {{-- {{@$order_item->product->product_name}}
                    
                    @if (isset($order_item->product_varient_id) && $order_item->product_varient_id != 0 )
                    
                    
                    @if (@$order_item->product->product_name != @$order_item->product_varient->variant_name )
                      -
                    {{ @$order_item->product_varient->variant_name }}
                    @endif
  
                    
                    @endif --}}
                  </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                    {{@$order_item->quantity}} 
                  </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                    {{ @$order_item->unit_price }}
                  </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                    {{ @$order_item->mrp }}
                  </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333; border-right:1px solid #333;" align="center">
                    @php
                    $discountAmt = $order_item->quantity * (@$order_item->mrp - @$order_item->unit_price);
                  @endphp
                    {{ $discountAmt }} 
                  </td>
  
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
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
  
                    {{@$tax_info->tax_value}} 
                   
                   </td>
  
                   @php
                                              
                    @$t_val = ($tax_info->tax_value * $tval) * 0.01 ;
                      $splitdata = \DB::table('trn__tax_split_ups')->where('tax_id',@$tax_info->tax_id)->get();
                        // dd($splitdata);
                  @endphp 
  
                  
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                    <table style="line-height: 1rem; font-size: 10px;width:180px;">
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
                   {{ $item->split_tax_name }} : {{ number_format((float)$stax, 2, '.', '') }}  
  
                         </td>
                      </tr>
                      @endforeach
                   </table>
                  </td>
                  
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                    @if (isset($tTax))
                   {{ number_format((float)$tTax, 2, '.', '') }}  
                    @endif
                 </td>
  
                 <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                  {{ number_format((float)$orgCost, 2, '.', '') }}  
                   
                  </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
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
  
          </table>
          @endif
        </td>
    </tr>
    
    
  </table>
  <div style="font-family:Verdana, Geneva, sans-serif;border-top-style:solid;border-top-width:1px; position:absolute;
      bottom:0;
      right:0; " height="32" align="center">
    <table style="margin:10px;">
      <tr  >
        <td style="font-size: smaller;" >Sub Total &nbsp;</td>
        <td style="font-size: smaller;" > {{ number_format((float)$subtotal, 2, '.', '') }} </td>
      </tr>
      <tr>
        <td style="font-size: smaller;">Total Tax &nbsp;</td>
        <td style="font-size: smaller;">  {{ number_format((float)$tax_amount, 2, '.', '') }}</td>
      </tr>
  
                @php
                $pCharge = 0;
                $dCharge = 0;
                  $dCharge =   @$order->delivery_charge;
                  $pCharge =   @$order->packing_charge;
                @endphp

                @if(@$order->order_type == 'APP')
  
        <tr>
          <td style="font-size: smaller;">Delivery Charge &nbsp;</td>
          <td style="font-size: smaller;">  {{ number_format((float)$dCharge, 2, '.', '') }}</td>
        </tr>
        
        <tr>
          <td style="font-size: smaller;">Packing Charge &nbsp;</td>
          <td style="font-size: smaller;">  {{ number_format((float)$pCharge, 2, '.', '') }}</td>
        </tr>

        @else

        <tr>
          <td style="font-size: smaller;">Delivery Charge &nbsp;</td>
          <td style="font-size: smaller;">  0.00 </td>
        </tr>
        
        <tr>
          <td style="font-size: smaller;">Packing Charge &nbsp;</td>
          <td style="font-size: smaller;"> 0.00 </td>
        </tr>
  
  
        @endif
         <tr>
        <td style="font-size: smaller;">Applied Discount &nbsp;</td>
        <td style="font-size: smaller;">  {{ number_format((float)$dis_amt, 2, '.', '') }}</td>
      </tr>
          @if(@$order->order_type == 'APP')
     

       <tr>
        <td style="font-size: smaller;">Redeemed amount By Admin &nbsp;</td>
        <td style="font-size: smaller;">
             @if(isset($order->amount_reduced_by_rp))
       {{ @$order->amount_reduced_by_rp}} ({{ @$order->reward_points_used}} points )
       @else
       0.00
       @endif
                                                   
        </td>
      </tr>

      <tr>
        <td style="font-size: smaller;">Redeemed amount By Store</td>
        <td style="font-size: smaller;"> 
                @if(isset($order->amount_reduced_by_rp_store))
                {{ @$order->amount_reduced_by_rp_store}} ({{ @$order->reward_points_used_store}} points )
                @else
                0.00
                @endif
                
        </td>
     </tr>
      
       <tr>
        <td style="font-size: smaller;">Coupon Amount &nbsp;</td>
        <td style="font-size: smaller;">  {{ @$order->amount_reduced_by_coupon }}</td>
      </tr>
      @else
      <tr>
        <td style="font-size: smaller;">Redeemed amount By Admin &nbsp;</td>
        <td style="font-size: smaller;">
            0.00
                                                   
        </td>
      </tr>

      <tr>
        <td style="font-size: smaller;">Redeemed amount By Store</td>
        <td style="font-size: smaller;"> 
          0.00
                
        </td>
     </tr>
      
       <tr>
        <td style="font-size: smaller;">Coupon Amount &nbsp;</td>
        <td style="font-size: smaller;">   0.00</td>
      </tr>

      @endif
  
      <tr>
        <td style="font-size: smaller;font-weight: 500;">Grand Total &nbsp;</td>
        <td style="font-size: smaller;font-weight: 500;"> {{ @$order->product_total_amount }}</td>
      </tr>
  
     
  
  
     
      

      <tr>
        <td style="font-size: smaller;font-weight: 500;">&nbsp;</td>
        <td style="font-size: smaller;font-weight: 500;">&nbsp;</td>
      </tr>
   
     
    </table>
  </div>
  
  
  {{-- 
  <span style="text-align: center;margin:10px;" > <u> Tax Split Ups </u> </span>
  
  <table style="margin: 10px;
  font-size: 11px;border:thin;" width="60%" border="1" cellspacing="0" cellpadding="0" >
    <thead   cellspacing="1" cellpadding="1">
      
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
             <th  ><b>{{ $tax_s->tax_name }} {{ $tax_s->tax_value }}% </b></th>
             @foreach ($splitdata as $item)
             @php
                $spliteddata = \DB::table('mst_store_products')
                ->join('trn_order_items','trn_order_items.product_id','=','mst_store_products.product_id')
                ->where('mst_store_products.tax_id', $tax_s->tax_id)
                ->where('trn_order_items.order_id', $order_id)
                ->sum('trn_order_items.tax_amount');
             @endphp
             <td   cellspacing="1" cellpadding="1" >
  
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
    
  </table> --}}
  @php
     // die;
  @endphp