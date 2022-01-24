


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
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">Invoice Number: {{@$invoice_data->invoice_id}} <br> Invoice Date: {{$changeDate = date("d-m-Y", strtotime( @$order->created_at))  }}</td>
          </tr>

          <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"> 
              <div style="margin-top:5px;">
                {{ @$store_data->store_primary_address }} <br>
              <p style="margin-top:2px;">  Phone: {{ @$store_data->store_mobile }}  </p>  <br>
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
      <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" align="right">{{@$order->customer['customer_first_name']}} {{@$order->customer['customer_last_name']}}</td>
      </tr>
      <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" align="right"> 
            <div>
                <br>
              {{@$order->customer['customer_address']}} <br>
              Pincode: {{$order->customer['customer_pincode']}}<br>
              Phone: {{@$order->customer['customer_mobile_number']}}<br>
             
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
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
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
              Rate
            </td>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
              Subtotal
            </td>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
              Discount<br>Amount		
            </td>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
              Tax Details			
            </td>
            {{-- <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
              Tax Name
            </td>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
              Tax Percentage
            </td> --}}
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333; border-right:1px solid #333;" align="center">
              Tax <br> Amount
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
          @endphp
            @foreach ($order_items as $order_item)
              <tr>
                <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" height="32" align="center"> 
                  {{@$order_item->product->product_name}}
                  
                  @if (isset($order_item->product_varient_id) && $order_item->product_varient_id != 0 )
                  
                  
                  @if (@$order_item->product->product_name != @$order_item->product_varient->variant_name )
                    -
                  {{ @$order_item->product_varient->variant_name }}
                  @endif

                  
                  @endif
                </td>
                <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                  {{@$order_item->quantity}} 
                </td>
                <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                  {{ @$order_item->product_varient->product_varient_price }}
                </td>
                <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                  {{@$order_item->unit_price}} 
                </td>
                <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333; border-right:1px solid #333;" align="center">
                  @php
                  $tval  = $order_item->unit_price * @$order_item->quantity;
                  @endphp
                  {{@$order_item->unit_price * @$order_item->quantity}} 
                </td>
                <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                 @if (isset($order_item->discount_amount))
                  {{@$order_item->discount_amount}}
                     @else
                     0
                 @endif
                </td>
                  @php
                    $tax_info = \DB::table('mst_store_products')
                    ->join('mst__taxes','mst__taxes.tax_id','=','mst_store_products.tax_id')
                    ->where('mst_store_products.product_id', @$order_item->product_id)
                    ->select('mst__taxes.tax_id','mst__taxes.tax_name','mst__taxes.tax_value')
                    ->first();
                    $t_val = (@$tax_info->tax_value * $tval) * 0.01 ;
                    $splitdata = \DB::table('trn__tax_split_ups')->where('tax_id',@$tax_info->tax_id)->get();
                  @endphp
                {{-- <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                  {{ @$tax_info->tax_name }} 
                </td>
                <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                  {{@$tax_info->tax_value }} 
                </td> --}}
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
                        $stax = ($item->split_tax_value * $t_val) / $tax_info->tax_value; 
                    @endphp
                 {{ $item->split_tax_name }} - {{ $item->split_tax_value }}%
                 
               -  {{ number_format((float)$stax, 2, '.', '') }}  

                       </td>
                    </tr>
                    @endforeach
                 </table>
                </td>
                <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                  {{ @$t_val }} 
                </td>
                <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                  {{@$order_item->total_amount}} 
                </td>  
                
              </tr>
              @php
                $dis_amt =  $dis_amt + @$order_item->discount_amount;
                $single_subtotal = @$order_item->unit_price * @$order_item->quantity;
                $subtotal = $subtotal + $single_subtotal; 
                $tax_amount = $tax_amount + $t_val ; 
              @endphp
            @endforeach

        </table>
      </td>
  </tr>
  
  
</table>
<div style="font-family:Verdana, Geneva, sans-serif;border-top-style:solid;border-top-width:1px; position:absolute;
    bottom:0;
    right:0; " height="32" align="center">
  <table style="margin:10px;">
    <tr  >
      <td style="font-size: smaller;" >Sub Total &nbsp;</td>
      <td style="font-size: smaller;" > {{ @$subtotal }} </td>
    </tr>
    <tr>
      <td style="font-size: smaller;">Discount Amount &nbsp;</td>
      <td style="font-size: smaller;"> {{ @$dis_amt }}</td>
    </tr>
    <tr>
      <td style="font-size: smaller;">Tax Amount &nbsp;</td>
      <td style="font-size: smaller;">  {{ @$tax_amount }}</td>
    </tr>
    @if(($order->amount_reduced_by_coupon != null) && ($order->amount_reduced_by_coupon > 0))
    <tr>
      <td style="font-size: smaller;">Coupon Discount &nbsp;</td>
      <td style="font-size: smaller;">  {{ @$order->amount_reduced_by_coupon }}</td>
    </tr>
    @endif

    @if(($order->reward_points_used != null) || ($order->reward_points_used != 0))
    <!--<tr>-->
    <!--  <td style="font-size: smaller;">Reward point used &nbsp;</td>-->
    <!--  <td style="font-size: smaller;"> </td>-->
    <!--</tr>-->
    @if(($order->amount_reduced_by_rp != null) && ($order->amount_reduced_by_rp > 0))
     <tr>
      <td style="font-size: smaller;">Reward point amount &nbsp;</td>
      <td style="font-size: smaller;"> {{ @$order->amount_reduced_by_rp }} ({{ @$order->reward_points_used }} points)</td>
    </tr>
    @endif
    @endif
      
    <tr>
      <td style="font-size: smaller;font-weight: 500;">&nbsp;</td>
      <td style="font-size: smaller;font-weight: 500;">&nbsp;</td>
    </tr>
    <tr>
      <td style="font-size: smaller;font-weight: 500;">Grand Total &nbsp;</td>
      <td style="font-size: smaller;font-weight: 500;"> {{ @$order->product_total_amount }}</td>
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