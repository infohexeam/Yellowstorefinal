@extends('admin.layouts.app')
@section('content')
<style>
     .line{
        width: 112px;
        height: 47px;
        border-bottom: 1px solid black;
        position: absolute;
        }
</style>
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
                <form action="{{route('admin.list_customer')}}" method="GET"
                         enctype="multipart/form-data">
                   @csrf
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Customer Name</label>
                    <div id="customer_first_namel"></div>
                       <input type="text" class="form-control" name="customer_first_name" id="customer_first_name"  value="{{ request()->input('customer_first_name') }}" placeholder="Customer Name">

                  </div>
               </div>
                <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Customer Email</label>
                    <div id="customer_emaill"></div>
                       <input type="email" class="form-control" name="customer_email" id="customer_email"  value="{{ request()->input('customer_email') }}" placeholder="Customer Email">

                  </div>
               </div>

                <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Customer Mobile</label>
                    <div id="customer_mobile_numberl"></div>
                       <input type="text" class="form-control" name="customer_mobile_number"  id="customer_mobile_number" maxlength="10"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  value="{{ request()->input('customer_mobile_number') }}" placeholder="Customer Mobile">

                  </div>
               </div>


             </div>
             <div class="row">
                    <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">From Date</label>
                    <div id="date_froml"></div>
                     <input type="date" class="form-control" name="date_from" id="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date">

                  </div>
               </div>
                 <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">To Date</label>
                    <div id="date_tol"></div>
                     <input type="date" class="form-control"  name="date_to"  id="date_to" value="{{ request()->input('date_to') }}" placeholder="To Date">

                  </div>
               </div>
                <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div id="customer_profile_statusl"></div>
                     <select name="customer_profile_status" id="customer_profile_status"  class="form-control" >
                 <option value="" >Select Profile Status</option>
                 <option {{request()->input('customer_profile_status') == '1' ? 'selected':''}} value="1" >Active</option>
                 <option {{request()->input('customer_profile_status') == '0' ? 'selected':''}} value="0" >InActive</option>
                 </select>
                  </div>
               </div>
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                          <a href="{{route('admin.list_customer')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                </div>
                   </form>
                </div>


               <div class="card-body">

                <div class="table-responsive">
                  <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                      <tr>
                        <th class="wd-15p">SL.No</th>
                        <th class="wd-15p">{{ __('Name') }}</th>
                        <th class="wd-15p">{{ __('Email') }}</th>
                        <th class="wd-15p">{{ __('Mobile') }}</th>
                        <th class="wd-15p">{{__('Profile Status')}}</th>
                       <th class="wd-15p">{{__('OTP Status')}}</th>

                        <th class="wd-15p">{{__('Action')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $i = 0;
                      @endphp
                      @foreach ($customers as $customer)
                      <tr>
                        <td>{{ ++$i }}</td>
                        <td>
                            {{$customer->customer_first_name." ".$customer->customer_last_name}}
                            <input type="hidden" id="cusId{{ $customer->customer_id }}" value="{{$customer->customer_first_name." ".$customer->customer_last_name}}" />   
                        </td>
                       {{--  <td>{{$customer->countries['country_name']}}</td>  --}}
                        <td>{{$customer->customer_email}}</td>
                        <td>{{$customer->customer_mobile_number}}</td>

                        <td>
                       <form action="{{route('admin.status_customer',$customer->customer_id)}}" method="POST">

                            @csrf
                            @method('POST')
                            <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm @if($customer->customer_profile_status == 0) btn-danger @else btn-success @endif"> @if($customer->customer_profile_status == 0)
                            InActive
                            @else
                            Active
                            @endif</button>
                          </form>
                        </td>
                        <td>
                           <form action="{{route('admin.otp_status_customer',$customer->customer_id)}}" method="POST">

                            @csrf
                            @method('POST')
                            <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm @if($customer->customer_otp_verify_status == 0) btn-danger @else btn-success @endif"> @if($customer->customer_otp_verify_status == 0)
                            Not Verified
                            @else
                            Verified
                            @endif</button>
                          </form>
                        </td>
                         <td>
                     {{-- <form action="{{route('admin.destroy_customer',$customer->customer_id)}}" method="POST"> --}}

                    @csrf
                      @method('POST')

                       {{-- <!--<a class="btn btn-sm btn-cyan" href="{{url('admin/customer/edit/'.Crypt::encryptString($customer->customer_id))}}">Edit</a>--> --}}
                      <a class="btn btn-sm btn-cyan"
                       href="{{url('admin/customer/view/'.Crypt::encryptString($customer->customer_id))}}">View</a>
                        {{-- <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button> --}}
                         {{-- </form> --}}
                                          
                <button type="button" class="btn btn-sm btn-gray" data-toggle="modal" onclick="updateCustomerData({{$customer->customer_id}},{{ (new \App\Helpers\Helper)->findRewardPoints($customer->customer_id) }})" data-target="#reddemRewardModal"  >
                    Redeem Points
                </button>

                        </td>

                      </tr>
                      @endforeach
                    </tbody>
                  </table>


                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

            <div class="modal fade" id="reddemRewardModal" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Redeem Reward Points of <span id="customerName"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">
                         <div class="row" style="border-bottom: 1px solid black;" >
                        <span class="modal-title" >
                            <span  id="rewPts" style="font-size: x-large;font-weight: revert-layer;" ></span> 
                            <span > Points available.</span> 
                        </span> 
                        </div>

                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-12 p-0">
                                  
                                   <div class="col-md-12">
                                      <div class="form-group">
                                          <label class="form-label">Reward point to be redeemed </label>
                                          <input type="number" step="0.01"  class="form-control" name="points" oninput="reduceRewPts(this.value)" id="reward_point"  placeholder="Reward point to be redeemed" />
                                      </div>
                                    </div>
                                    
                                     <div class="col-md-12">
                                      <div class="form-group">
                                          <label class="form-label">Discription </label>
                                          <input type="text"  class="form-control" name="discription" id="discription"  placeholder="Discription" />
                                      </div>
                                    </div>
    <input type="hidden"  class="form-control" name="customer_id" id="customerId"  />

                                    <div class="col-md-12">
                                        <button type="submit" id="saveBtn" class="btn btn-sm btn-gray" >Redeem Points</button>
                                    </div>

                              </tbody>
                           </table>
                        </div>

                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>
            </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script>
$('#reward_point').val(0)
function reduceRewPts(valRew){
    //console.log(valRew);
    let rewPts = parseFloat($('#rewPts').text());
    // console.log(valRew+" : "+rewPts);
    if(valRew > rewPts){
        $("#saveBtn").attr("disabled", true);
    }else{
        $("#saveBtn").attr("disabled", false);
    }

}
function updateCustomerData(id,rewPts){
  console.log(id);
    let cusName = $('#cusId'+id).val();
    $('#customerName').text(cusName);
    $('#customerId').val(id);
    $('#rewPts').text(rewPts);

}

$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Customers',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5]
                 }
            },
            {
                extend: 'excel',
                title: 'Customers',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5]
                 }
            }
         ]
    } );

} );

</script>


  @endsection
