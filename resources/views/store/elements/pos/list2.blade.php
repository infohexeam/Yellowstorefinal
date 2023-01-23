@extends('store.layouts.app')
@section('content')

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
      <div class="card">
        <div class="row">
          <div class="col-12">


            @if ($message = Session::get('status'))
            <div class="alert alert-success">
              <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
            </div>
            @endif
            @if ($message = Session::get('error'))
            <div class="alert alert-danger">
              <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
            </div>
            @endif
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

            <div class="card-header">
                <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>
            <div class="card-body border">
          
                <form id="reset" method="GET" enctype="multipart/form-data">
                   @csrf
                        <div class="row">

                            <div class="col-md-12">
                              <span id="msgPro" style="color:red"></span>
                                <div class="form-group">
                                    <label class="form-label">Customer</label>
                                        <select name="customer_id2" id="customer_id2" class="form-control" readonly="">
                                             <option value="" >Store Customer</option>
                                             <!--@foreach ($customer as $data)-->
                                             <!--     <option value="{{ $data->customer_id }}" >{{ $data->customer_first_name }} {{ $data->customer_last_name }} - {{ $data->customer_mobile_number }} </option>-->
                                             <!--@endforeach-->
                                        </select>
                                 </div>
                            </div>
                            <input type="hidden" name="customer_id" name="customer_id" value="3"/>

                             <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Select Product</label>
                                        <select name="product_id" onchange="productSelected(this.value)" id="product_id" class="form-control select2-show-search" data-placeholder="Select Product" >
                                             <option value="" >Select Product</option>
                                              @foreach ($products as $data)
                                                  <option value="{{ $data->product_id }}|{{ $data->product_varient_id }}" >
                                                 
                                                                                                      {{$data->variant_name}}

                                              {{--  {{ $data->product_name }} 
                                               @if( $data->product_name != $data->variant_name)
                                                       @if (isset($data->variant_name))
                                                       -
                                                     <!--@endif-->
                                                     
                                                     {{$data->variant_name}}
                                                    @endif  --}}

                                                  </option>
                                             @endforeach
                                        </select>
                                 </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">MRP</label>
                                    @php
                                   $uid = uniqid();
                                   @endphp

                                    <input type="text" id="order_uid" name="order_uid" value="{{$uid}}">
                                   <input type="text" readonly class="form-control" id="mrp" name="mrp" value="{{request()->input('mrp')}}" placeholder="MRP">
                                 </div>
                            </div>
 
                              <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Quantity</label>
                                   <input type="number" oninput="changeQuantity()"  class="form-control" id="quantity" name="quantity" value="{{request()->input('quantity')}}" placeholder="Quantity">
                                    <span id="msgQuantity" style="color:red"></span>
                                 </div>
                            </div>

                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Sale Price</label>
                                   <input type="text" readonly class="form-control" id="rate" name="rate" value="{{request()->input('rate')}}" placeholder="Sale Price">
                                 </div>
                            </div>

                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="form-label">Discount %</label>-->
                            <!--       <input type="number" onchange="changeDiscount(this.value)" min="0" oninput="validateDis(this.value)" value=0 class="form-control" id="discount" name="discount" value="{{request()->input('discount')}}" placeholder="Discount %">-->
                            <!--     </div>-->
                            <!--</div>-->

                             <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Discount</label>
                                   <input type="text" readonly class="form-control" id="total_discount" name="total_discount" value="{{request()->input('total_discount')}}" placeholder="Discount">
                                 </div>
                            </div>

                             <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Tax Percentage</label>
               
                                   <input type="text" readonly onchange="changeTax()" class="form-control" id="tax_value" name="tax_value" value="{{request()->input('tax_value')}}" placeholder="Tax Percentage">
  
                                        {{-- <select  name="tax_value" id="tax_value" class="form-control" >
                                             <option value="0">Tax</option>
                                             @foreach ($tax as $data)
                                                  <option value="{{ $data->tax_value }}" >{{ $data->tax_name }} - {{ $data->tax_value }}%</option>
                                             @endforeach
                                        </select> --}}
                                 </div>
                            </div>

                             <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Amount</label>
                                   <input readonly type="text" class="form-control" id="total_amount" name="total_amount" value="{{request()->input('total_amount')}}" placeholder="Total Amount">
                                 </div>
                            </div>

                            <br>

                            <div class="col-md-12">
                                <div class="form-group">
                                <center>
                                   <a style="color:#ffffff;" id="addBtn" onclick="submitProduct()" class="btn btn-block btn-blue"> Submit </a>
                                   {{-- <a style="color:#ffffff;" class="btn btn-block btn-blue"> Add More Product </a> --}}
                                </center>
                                </div>
                            </div>
                        </div>
                </form>
            </div>

               <div class="card-body">
                         <h3 class="text-bold" id="cname"></h3>
                <div class="table-responsive">
                  <form action="{{route('store.save_pos_lock')}}" method="POST" enctype="multipart/form-data">
                  @csrf
                   <input type="text" id="or_uid" name="or_uid" value="{{$uid}}">
                  <table id="myTable" style="background: #f1f1f9;" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                      <tr>
                        {{-- <th class="wd-15p">Customer</th> --}}
                        <th class="wd-15p">Product</th>
                        <th class="wd-15p">Qty & Rate</th>
                        <th class="wd-15p">Discount</th>
                        <th class="wd-15p">Tax %</th>
                        <th class="wd-15p">Sub Total</th>

                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $i = 0;
                      @endphp
     
                      
                    </tbody>
                  </table>
          <button id="order_btn" type="submit"  style="color:#ffffff;" class="btn btn-block btn-cyan"> Confirm Order </button>
          <a id="cancel_btn" href=" "   style="color:#ffffff;" class="mt-2 btn btn-block btn-warning"> Cancel </a>
       </form>

              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  </div>


     {{-- <div class="spinner1">
          <div class="double-bounce1"></div>
          <div class="double-bounce2"></div>
     </div> --}}
										

<script>


function validateDis(disValue)
{
     if(disValue < 0) {
          $('#discount').val(0);
     }
     else if(disValue > 100)
     {
          $('#discount').val(0);
     }
      
}


    $("#order_btn").hide();
    $("#cancel_btn").hide();
    
    $("#discount").val(0);
    $("#total_discount").val(0);
    $("#quantity").val(1);
    $("#rate").val(0);
    $('#cname').text('');

          var customer_id = 0;
          var customer_name;
          var product_id = 0;
          var quantity = 1;
          var rate = 0;
          var discount = 0;
          var total_discount = 0;
          var tax_value = 0;
          var total_amount = 0;

          var product_name;
          var product_sale_price = 0;

var countPro = 0;


     function productSelected()
     {  
           product_id = $('#product_id').val();
           quantity = $('#quantity').val();
           var product_res = product_id.split("|");


          var _token = $('input[name="_token"]').val();
          
          $.ajax({
               url:"{{ route('store.find_product') }}",
               method:"POST",
               data:{product_id:product_res[0],product_varient_id:product_res[1], _token:_token},
               success:function(result)
               {
                          $('#quantity').val(quantity);
                          //alert(quantity);
                    product_name = result['variant_name'];
                    let product_varient_price = result['product_varient_price'];
                    product_sale_price = result['product_varient_offer_price'];
                    s_tax = result['tax'];
                    $('#rate').val(quantity * product_sale_price);
                    $('#mrp').val(quantity * product_varient_price);

                    $('#tax_value').val(s_tax);
                    $('#quantity').val(quantity);
                    total_amount = quantity * product_sale_price
                         tax = (parseFloat(s_tax) / 100) * total_amount;
                        // total_amount_plus_tax = parseFloat(total_amount) + parseFloat(tax);
                         total_amount_plus_tax = parseFloat(total_amount);
                    $('#total_amount').val(total_amount_plus_tax.toFixed(2)); // remove it
                  //  changeDiscount();
                  // console.log(parseFloat(product_varient_price) + " - " +parseFloat(product_sale_price));
                    let Tdis = parseFloat(product_varient_price) - parseFloat(product_sale_price);
                     $('#total_discount').val(Tdis.toFixed(2));

               }
          })

        //  alert('cusId='+customer_id+'\n proid='+product_id+' \n qty='+quantity+'\n rate='+rate+' \n disc='+discount+'\n tDis='+total_discount+'\n taxId='+tax_id+'\n tAmt='+total_amount);
     }
     
    function findProductAvailability(product_id , quantity)
     {
       //  let product_id = $('#product_id').val();
        // let   quantity = $('#quantity').val();
        
        if(quantity <= 0)
        {
            $('#quantity').val(1);
        }
        
                     //   $('#addBtn').attr('disabled', 'disabled');
        let product_res = product_id.split("|");
        var _token = $('input[name="_token"]').val();
        
         $.ajax({
               url:"{{ route('store.checkProductAvailability') }}",
               method:"POST",
               data:{product_id:product_res[0],product_varient_id:product_res[1],quantity:quantity, _token:_token},
               success:function(result)
               {
                   // console.log(result);
                    if(result == 'available')
                    {
                        $('#msgQuantity').text('');
                        // $('#addBtn').attr('disabled', false);
                        $('#addBtn').show()

                    }
                    else
                    {
                        // $('#addBtn').attr('disabled', 'disabled');
                        $('#addBtn').hide()
                        $('#msgQuantity').text('Quantity must not exceed '+result);
                    }
               }
          })
     }

     function changeQuantity()
     {  
           product_id = $('#product_id').val();
           quantity = $('#quantity').val();
           //alert(quantity);
           
           findProductAvailability(product_id,quantity);

          var _token = $('input[name="_token"]').val();
          var product_res = product_id.split("|");

          $.ajax({
               url:"{{ route('store.find_product') }}",
               method:"POST",
               data:{product_id:product_res[0],product_varient_id:product_res[1], _token:_token},
               success:function(result)
               {
                    product_name = result['variant_name'];
                    product_sale_price = result['product_varient_offer_price'];
                    product_varient_price = result['product_varient_price'];
                    var s_tax = result['tax'];
                    rate = $('#rate').val();
                    
                    if(quantity<=1)
                    {
                         $('#quantity').val(1);
                          $('#rate').val(product_sale_price);
                          //alert(product_sale_price);
                           tax = (s_tax / 100) * 1 * product_sale_price;
                         //total_amount_after_tax = parseFloat(rate) + parseFloat(tax);
                         total_amount_after_tax = parseFloat(1 * product_sale_price);

                         let total_discount = parseFloat(product_varient_price) - parseFloat(product_sale_price);
                         $('#total_discount').val(total_discount * 1);
                         $('#total_amount').val(total_amount_after_tax.toFixed(2)); // remove it

                    }
                    else
                    {
                         $('#quantity').val(quantity);
                        // alert(quantity);
                          
                          $('#rate').val(quantity * product_sale_price);
                          //alert(quantity * product_sale_price);
                         tax = (s_tax / 100) * rate;
                         //total_amount_after_tax = parseFloat(rate) + parseFloat(tax);
                         total_amount_after_tax = parseFloat(quantity * product_sale_price);

                         let total_discount = parseFloat(product_varient_price) - parseFloat(product_sale_price);
                         $('#total_discount').val(total_discount * quantity);
                         $('#total_amount').val(total_amount_after_tax.toFixed(2)); // remove it

                    }
                    
                    
                   
                  //  changeDiscount();
               }
          })

        //  alert('cusId='+customer_id+'\n proid='+product_id+' \n qty='+quantity+'\n rate='+rate+' \n disc='+discount+'\n tDis='+total_discount+'\n taxId='+tax_id+'\n tAmt='+total_amount);
     }

     function changeDiscount(disValue)
     {
          if(disValue === "")
          {
              // $('#discount').val(0);
          }
          //discount = $('#discount').val();
          product_id = $('#product_id').val();
          quantity = $('#quantity').val();
          var product_res = product_id.split("|");

         var _token = $('input[name="_token"]').val();
          
          $.ajax({
               url:"{{ route('store.find_product') }}",
               method:"POST",
               data:{product_id:product_res[0],product_varient_id:product_res[1], _token:_token},
               success:function(result)
               {
                    product_sale_price = result['product_varient_offer_price'];
                    var s_tax = result['tax'];
                    var total_amount_before_tax = quantity * product_sale_price;
                    
                    var tax = (s_tax / 100) * total_amount_before_tax;
                    var total_amount_after_tax = parseFloat(total_amount_before_tax) + parseFloat(tax);

                    var discount_amount = (parseInt(discount) / 100) * total_amount_before_tax;
                    
                    var totalAmountAfterTaxAndAfterDiscount = total_amount_after_tax - discount_amount;

                     //   alert(totalAmountAfterTaxAndAfterDiscount);
                    $('#total_discount').val(discount_amount.toFixed(2));    
                    $('#total_amount').val(totalAmountAfterTaxAndAfterDiscount.toFixed(2)); // remove it
  }
          })

     }

     function changeTax()
     {
          rate = $('#rate').val();
          total_discount = $('#total_discount').val();
          var total_rate = rate - total_discount;
          tax_value = $('#tax_value').val();
          tax = (parseFloat(tax_value) / 100) * total_rate;
          total_amount = parseFloat(total_rate) + parseFloat(tax);
          $('#total_amount').val(total_amount.toFixed(2)); // remove it
     }
     
     
     function submitProduct()
     {   


           customer_id = $('#customer_id').val();
           product_id = $('#product_id').val();
           quantity = $('#quantity').val();
           //alert(quantity);
           order_uid= $('#order_uid').val();
           //findProductAvailability(product_id , quantity);
           //alert(quantity);
           rate = $('#rate').val();
           mrp=$('#mrp').val();
           //alert(quantity);
       //    discount = $('#discount').val();
           total_discount = $('#total_discount').val();
           tax_value = $('#tax_value').val();
           total_amount = $('#total_amount').val();
           $('#order_btn').show();

          var total_rate = rate;
          var tax = (parseFloat(tax_value) / 100) * total_rate;

          var product_res = product_id.split("|");

           if(customer_id != "" && product_res[0] != "" && product_res[1] != "" && total_discount != ""  && rate != "" && total_amount != "")
           {
               $('#customer_id').attr("disabled", true); 
               $('#total_amount').val(0);
               $('#total_discount').val(0);
               product_res[0]=0;
               var _token = $('input[name="_token"]').val();
               $.ajax({
                    url:"{{ route('store.lock_product') }}",
                    method:"POST",
                     data:{product_id:product_res[0],product_varient_id:product_res[1],order_uid:order_uid,quantity:quantity,_token:_token},
                    success:function(result)
                    {
                         countPro++;
                         //alert(result['status']);
                         //  alert(customer_name);
                        // $('#cname').text(customer_name);
                        if(result['status']==1)
                        {
                         html = '<tr id="tr'+countPro+'"><td> <input type="hidden" class=".classCustomerID" name="customer_id" value="'+customer_id+'"> <input type="hidden" class=".classProductID" name="product_id[]" value="'+product_res[0]+'"> <input type="hidden" class=".classProductInvID" name="product_varient_id[]" value="'+product_res[1]+'"> '+product_name+' </td><td><input type="hidden" class=".classQuantity" name="single_quantity[]" value="'+quantity+'">'+quantity+' <i class="fa fa-times"></i> <input type="hidden" class=".classSingleQuantityRate" name="single_quantity_rate[]" value="'+(rate/ quantity)+'">'+ (rate/ quantity) +'</td><td><input type="hidden" class=".classDiscountAmount" name="discount_amount[]" value="'+total_discount+'"><input type="hidden" class=".classDiscountPercentage" name="discount_percentage[]" value="'+0+'">'+total_discount+'</td><td><input type="hidden" class=".classTotalTax" name="total_tax[]" value="'+tax.toFixed(2)+'">'+tax_value+'</td><td class="price"><input type="hidden" class=".classTotalAmount" name="total_amount[]" value="'+parseFloat(total_amount).toFixed(2)+'">'+parseFloat(total_amount).toFixed(2)+'</td><td><a class="btn btn-sm btn-danger text-white" id="removeBtn" onclick="removetr('+countPro+')" class=".removeBtn">Remove</a></td></tr>';
                         $('#myTable tr:last').after(html);
                         $('.total_sum').remove();
                              
                              $("#order_btn").show();
                              $("#cancel_btn").show();
                              var total_sum = 0;
                              $(".price").each(function(){
                              total_sum += parseFloat($(this).text());
                              $('.classProductId').val(0);
                              $('.total_sum').remove(); 

                         });
                         // add total amount
                         html = '<tr class="total_sum"><td colspan="5" class=" text-right">Total</td><td class=""><input type="hidden" id="classFullAmountId" class="classFullAmount" name="full_amount" value="'+total_sum.toFixed(2)+'">'+total_sum.toFixed(2)+'</td></tr>';
                         $('#myTable tr:last').after(html);
                        }
                        else
                        {
                         $('#msgPro').text(result['message']);
                          $('#order_btn').hide();

                        }

                        


                    }
               })

                    
                    
     
                      $("#product_id").val('').trigger('change');;
                     $('#quantity').val(1);
                     $('#rate').val('');
                    $('#mrp').val('');
                   //  $('#discount').val('');
                     $('#total_discount').val('');
                     $('#tax_value').val(0);
                     $('#total_amount').val('');

           }
           else
           {
              // $('#total_amount').val(0);
            //   $('#total_discount').val(0);
 alert("Plese fill all fields..");
           }

         // alert('cusId='+customer_id+'\n proid='+product_id+' \n qty='+quantity+'\n rate='+rate+' \n disc='+discount+'\n tDis='+total_discount+'\n tax_value='+tax_value+'\n tAmt='+total_amount);


     }
     function saveOrder()
     {
            var  products 
               
               var _token = $('input[name="_token"]').val();
               $.ajax({
                    url:"{{ route('store.find_customer') }}",
                    method:"POST",
                    data:{customer_id:2, _token:_token},
                    success:function(result)
                    {
                        
                    }
               })
     }
     //function productSelected(productId)
     //{
            //var _token = $('input[name="_token"]').val();
               //$.ajax({
                  //  url:"{{ route('store.find_tax') }}",
                  //  method:"GET",
                  //  data:{product_id:productId, _token:_token},
                  //  success:function(result)
                  //  {
                   //              alert(result);
                   // }
              // })
    // }
     
    


function removetr(key)
{
     //console.log(key); 
    // alert("here");
    $('#tr'+key).remove();
     $('.total_sum').remove(); 

var ht = '';
let ts = 0;
    $(".price").each(function(){
     ts += parseFloat($(this).text());
    // console.log($(this).text());
    });
     ht = '<tr class="total_sum"><td colspan="5" class=" text-right">Total</td><td class=""><input id="classFullAmountId" type="hidden" class="classFullAmount" name="full_amount" value="'+ts.toFixed(2)+'">'+ts.toFixed(2)+'</td></tr>';
   $('#myTable tr:last').after(ht);
   let total_sumr =   parseFloat($("#classFullAmountId").val());
   console.log($("#classFullAmountId").text());
  console.log(total_sumr);
   if(total_sumr <= 0){
     $('#order_btn').hide();
     }else{
          $('#order_btn').show();
     }

}
</script>
  

  @endsection
