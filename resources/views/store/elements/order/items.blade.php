

<div style="margin:10%;" >
    <u  style="margin-bottom: 0px;" ><h3> {{ $order_no }}</h3></u>
    <p style="margin-top: 0px;" >Date: {{ \Carbon\Carbon::parse(@$order->created_at)->format('d/m/Y')}}</p>
   <table width="100%" border="0" cellspacing="0" cellpadding="0">

    <tr>
      <td  colspan="2"> </td>
    </tr>
      <tr>
          <td colspan="2">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" height="32" align="center">
                Item Name
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Qty
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Rate
              </td>
             
            </tr>
          
              @foreach ($order_items as $order_item)
                <tr>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" height="32" align="center"> 
                    {{@$order_item->product->product_name}}
                    @if (isset($order_item->product_varient_id) && $order_item->product_varient_id != 0 )
                      -
                    {{ @$order_item->product_varient->variant_name }}
                    
                    @endif
                  </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                    {{@$order_item->quantity}} 
                  </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                    {{@$order_item->unit_price}} 
                  </td>
                 
                  
                
                  
                </tr>
              
              @endforeach
  
          </table>
        </td>
    </tr>
   </table>
</div>
 
  @php
    //  die;
  @endphp