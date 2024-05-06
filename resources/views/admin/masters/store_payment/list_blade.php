@extends('admin.layouts.app')
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
                     <label class="form-label">Name</label>
                       <input type="text" class="form-control" 
                       name="customer_first_name"  value="{{ request()->input('customer_first_name') }}" placeholder="Customer Name">

                  </div>
               </div>
                <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Email</label>
                       <input type="email" class="form-control" 
                       name="customer_email"  value="{{ request()->input('customer_email') }}" placeholder="Customer Email">

                  </div>
               </div>
            
                <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Mobile</label>
                       <input type="text" class="form-control" 
                       name="customer_mobile_number" maxlength="10"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  value="{{ request()->input('customer_mobile_number') }}" placeholder="Customer Mobile">

                  </div>
               </div>
               
             </div>
             <div class="row">
                    <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">From Date</label>
                     <input type="date" class="form-control" name="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date">

                  </div>
               </div> 
                 <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">To Date</label>
                     <input type="date" class="form-control" name="date_to" value="{{ request()->input('date_to') }}" placeholder="To Date">

                  </div>
               </div> 
                <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Status</label>
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
                           <button type="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('admin.list_customer')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                </div>
                   </form>
                </div>
              
             
               <div class="card-body">
                        
                <div class="table-responsive">
                  <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                      <tr>
                        <th class="wd-15p">S.No</th>
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
                        <td>{{$customer->customer_first_name." ".$customer->customer_last_name}}</td>
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
                     <form action="{{route('admin.destroy_customer',$customer->customer_id)}}" method="POST">
                       
                    @csrf
                      @method('POST')

                       <a class="btn btn-sm btn-cyan" href="{{url('admin/customer/edit/'.Crypt::encryptString($customer->customer_id))}}">Edit</a> 
                      <a class="btn btn-sm btn-cyan"
                       href="{{url('admin/customer/view/'.Crypt::encryptString($customer->customer_id))}}">View</a> 
                        <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                         </form> 

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

 
  @endsection
 