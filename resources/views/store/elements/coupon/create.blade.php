@extends('store.layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
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
               <form action="{{route('store.store_coupon')}}" method="POST"  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Coupon Code *</label>
                                <input type="text" required class="form-control" name="coupon_code" value="{{old('coupon_code')}}" placeholder="Coupon Code">
                            </div>
                        </div>
                        
                       <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"> Minimum Purchase Amount *</label>
                            <input type="number" min="0" step="1" oninput="validity.valid||(value=''); truncateInput(this, 8, 'min_purchase_amt_message');" required class="form-control" name="min_purchase_amt" id="min_purchase_amt" value="{{old('min_purchase_amt')}}" placeholder="Minimum Purchase Amount" maxlength="8">
                            <span id="min_purchase_amt_message" class="text-danger" style="display: none;">Maximum allowed digit is 8</span>
                        </div>
                    </div>

                        
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Coupon Type *</label>
                                <select required=""  name="coupon_type"  class="form-control"  >
                                    <option  value="">Coupon Type</option>
                                    <option  {{old('coupon_type') == '1' ? 'selected':''}} value="1">Single use</option>
                                    <option  {{old('coupon_type') == '2' ? 'selected':''}} value="2">Multi use</option>
                                 </select>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Discount Type *</label>
                                <select required="" onchange="chgDis(this.value)" name="discount_type" id="discount_type"  class="form-control"  >
                                    <option  value="">Discount Type</option>
                                    <option  {{old('discount_type') == '1' ? 'selected':''}} value="1">Fixed</option>
                                    <option  {{old('discount_type') == '2' ? 'selected':''}} value="2">Percentage</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Discount <span id="loseend" > </span> *</label>
                                <input type="number" min="0" step="1" oninput="validity.valid||(value=''); truncateInput(this, 8, 'discount_message');" required class="form-control" name="discount" id="discountAmt" value="{{old('discount')}}" placeholder="Discount" maxlength="8">
                                <span id="discount_message" class="text-danger" style="display: none;">Maximum allowed digit is 8</span>
                            </div>
                        </div>

                     
                    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Valid From *</label>
                                <input type="date" id="valid_from" required class="form-control" name="valid_from" value="{{old('valid_from')}}" placeholder="" min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Valid To *</label>
                                <input type="date" required class="form-control" id="valid_to" name="valid_to" value="{{old('valid_to')}}" placeholder="">
                            </div>
                        </div>

                           <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Status *</label>
                                <select required=""  name="coupon_status"  class="form-control"  >
                                    <option  value="">Status</option>
                                    <option  {{old('coupon_status') == '0' ? 'selected':''}} value="0">Active</option>
                                    <option  {{old('coupon_status') == '1' ? 'selected':''}} value="1">InActive</option>
                                </select>
                            </div>
                        </div>

               

                  
                                
                  </div>
                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">Reset</button>
                           <a class="btn btn-danger" href="{{ route('store.list_coupon') }}">Cancel</a>
                           </center>
                        </div>
               </form>

         </div>
      </div>
   </div>
</div>
<script>
    $("#valid_from").change(function(){
    
    var valid_date = $("#valid_from").val();
    
    $('#valid_to').attr('min', valid_date);
}); 

</script>
<script>
    function chgDis(val){
        if(val == 1){
            $('#loseend').text('Amount');
        }
        else if(val == 2)
        {
            $('#loseend').text('(%)');
        }
        else
        {
            $('#loseend').text('');
        }
    }
    
    $(document).ready(function() {
        var val = $('#discount_type').val();
        if(val == 1){
            $('#loseend').text('Amount');
        }
        else if(val == 2)
        {
            $('#loseend').text('(%)');
        }
        else
        {
            $('#loseend').text('');
        }
    });
    
    
function disChange(dis)
{
   var discountType =  $('#discount_type').val();
   if(validity.valid)
   {
   if(discountType == 2)
   {
       if(dis > 100)
       {
 $('#discountAmt').val(0);
       }
   }
   
    if(dis < 0)
       {
            $('#discountAmt').val(0);
       }
       //validity.valid||(value='');
   }
   else

   {
     $('#discountAmt').val(0);

   }
}

function truncateInput(element, maxLength, messageElementId) {
    var inputLength = element.value.length;
    var messageElement = document.getElementById(messageElementId);
    if (inputLength > maxLength) {
        element.value = element.value.slice(0, maxLength);
        messageElement.style.display = 'block';
    } else {
        messageElement.style.display = 'none';
    }
}



</script>
 @endsection
