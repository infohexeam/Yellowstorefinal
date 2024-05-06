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
               <form action="{{route('store.save_configure_points')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                    {{-- <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Registration Points</label>
                           <input type="number" class="form-control"  name="registraion_points" value="{{old('registraion_points',@$configure_points->registraion_points)}}" placeholder="Registration Points">
                        </div>
                        </div>--}}

                         <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">First Order Points</label>
                           <input type="number"  min="0" oninput="validity.valid||(value='');truncateInput(this, 10, 'fop_message');" class="form-control" name="first_order_points" value="{{old('first_order_points',@$configure_points->first_order_points)}}" placeholder="First Order Points" maxlength="10">
                           <span id="fop_message" class="text-danger" style="display: none;">Maximum allowed digit is 10</span>
                        </div>
                        </div>


                     <div class="col-md-6">

                         <div class="form-group">
                           <label class="form-label">Referral Points</label>
                            <input type="number"  min="0" oninput="validity.valid||(value='');truncateInput(this, 10, 'rp_message');" required=""  name="referal_points" class="form-control"  value="{{old('referal_points',@$configure_points->referal_points)}}" placeholder="Referral Points " maxlength="10">
                             <span id="rp_message" class="text-danger" style="display: none;">Maximum allowed digit is 10</span>
                           </div>
                        </div>
                        
                        
                        <div class="col-md-6">

                         <div class="form-group">
                           <label class="form-label">Referred Joiner Points</label>
                            <input type="number"  min="0" oninput="validity.valid||(value='');truncateInput(this, 10, 'jp_message');" required=""  name="joiner_points" class="form-control"  value="{{old('joiner_points',@$configure_points->joiner_points)}}" placeholder="Joiner Points " maxlength="10">
                             <span id="jp_message" class="text-danger" style="display: none;">Maximum allowed digit is 10</span>
                           </div>
                        </div>

                  <div class="col-md-3">

                         <div class="form-group">
                           <label class="form-label">Rupees </label>
                            <input type="number" readonly  min="1"  required=""  name="rupee" class="form-control"  value="1" placeholder="Rupee">
                           </div>
                        </div>
                          <div class="col-md-1">
                           <div class="form-group ">
                                                          <label class="form-label">&nbsp;&nbsp;&nbsp; </label>

=
                            </div>
                            </div> 

                        <div class="col-md-2">

                         <div class="form-group">
                           <label class="form-label">Points</label>
                            <input type="number" readonly  min="1"  required=""  name="rupee_points" class="form-control"  value="1" placeholder="Points">
                           </div>
                        </div>


                          <div class="col-md-3">

                         <div class="form-group">
                           <label class="form-label">Order Amount</label>
                            <input type="number"  min="0" oninput="validity.valid||(value='');truncateInput(this, 10);" required=""  name="order_amount" class="form-control"  value="{{old('order_amount',@$configure_points->order_amount)}}" placeholder="Order Amount " maxlength="10">
                           </div>
                        </div>

                      <div class="col-md-1">
                           <div class="form-group ">
                                                          <label class="form-label">&nbsp;&nbsp;&nbsp; </label>
=
                            </div>
                            </div> 
                            
                            <div class="col-md-2">

                         <div class="form-group">
                           <label class="form-label">Points</label>
                            <input type="number"  min="0" oninput="validity.valid||(value='');truncateInput(this, 10, 'points_message');" required=""  name="order_points" class="form-control"  value="{{old('points',@$configure_points->order_points)}}" placeholder="Order points " maxlength="10">
                             <span id="points_message" class="text-danger" style="display: none;">Maximum allowed digit is 10</span>
                           </div>
                        </div>
                        
                        <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Percentage of wallet points can be redeemed per order</label>
                           <input type="number"  min="0" max="100" oninput="validity.valid||(value='');truncateInput(this, 10);" class="form-control" name="redeem_percentage" value="{{old('redeem_percentage',@$configure_points->redeem_percentage)}}" placeholder="Percentage of wallet points can be redeemed per order">
                        </div>
                        </div>

                        <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Maximum redeem amount per order</label>
                           <input type="number"  min="0" oninput="validity.valid||(value='');truncateInput(this, 10, 'max_red_messagee');" class="form-control" name="max_redeem_amount" value="{{old('max_redeem_amount',@$configure_points->max_redeem_amount)}}" placeholder="Maximum redeem amount per order" maxlength="10">
                            <span id="max_red_message" class="text-danger" style="display: none;">Maximum allowed digit is 10</span>
                        </div>
                        </div>
                         <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Minimum Order Amount</label>
                           <input type="number"  min="1" oninput="validity.valid||(value='');truncateInput(this, 10);" class="form-control" name="minimum_order_amount" value="{{old('minimum_order_amount',@$configure_points->minimum_order_amount)}}" placeholder="Maximum redeem amount per order" maxlength="10">
                        </div>
                        </div>

                    </div>

                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                           {{-- <button type="reset" class="btn btn-raised btn-success">
                           Reset</button> --}}
                           {{-- <a class="btn btn-danger" href="{{ route('admin.list_configure_points') }}">Cancel</a> --}}
                           </center>
                        </div>
               </form>

      </div>
      </div>
   </div>
</div>
<script>
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


