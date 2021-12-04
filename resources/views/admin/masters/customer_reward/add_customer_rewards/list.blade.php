@extends('admin.layouts.app')
@section('content')
@php
$date = Carbon\Carbon::now();
@endphp
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12 col-lg-12">
         <div class="card">
            <div class="row">
               <div class="col-12" >

                  @if ($message = Session::get('status'))
                  <div class="alert alert-success">
                     <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                  </div>
                  @endif
                  <div class="col-lg-12">
                     @if ($errors->any())
                     <div class="alert alert-danger">
                        <h6>Whoops!</h6> There were some problems with your input.<br><br>
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

                     <div class="card-body">
                      <a href="  {{route('admin.add_reward_to_customer')}} " class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Add Reward Points to Non-Existing Customer
                        </a>
                        <br>
                        <div class="table-responsive">
                           <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">Customer <br> Mobile Number</th>
                                    <th class="wd-15p">{{ __('Reward Points') }}</th>
                                    <th class="wd-15p">{{ __('Reward Description') }}</th>
                                    <th class="wd-15p">{{ __('Date') }}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                               

                                 @foreach ($dummy_rewards as $temp_reward)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $temp_reward->customer_mobile_number}}</td>
                                    <td>{{ $temp_reward->reward_points}}</td>
                                    <td>{{ $temp_reward->reward_discription}}</td>
                                    <td>{{ \Carbon\Carbon::parse($temp_reward->added_date)->format('d-m-Y')}} </td>
                                 
                                    <td>
                                      <form action="{{route('admin.remove_temp__points_to_customer',$temp_reward->reward_to_customer_temp_id)}}" method="POST">
                                          @csrf
                                          @method('POST')

                                       <a class="btn btn-sm btn-cyan" href="{{url('admin/edit/temp/reward-to-customer/'.Crypt::encryptString($temp_reward->reward_to_customer_temp_id ))}}">Edit</a>
                                          <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                    </td>
                                 </tr>
                                 @endforeach
                                 
                                 
                                   @foreach ($rewards as $reward)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $reward->customer_mobile_number}}</td>
                                    <td>{{ $reward->reward_points}}</td>
                                    <td>{{ $reward->reward_discription}}</td>
                                    <td>{{ \Carbon\Carbon::parse($reward->added_date)->format('d-m-Y')}} </td>
                                 
                                    <td>
                                     <form action="{{route('admin.remove_points_to_customer',$reward->reward_to_customer_id)}}" method="POST">
                                          @csrf
                                          @method('POST')

                                       <a class="btn btn-sm btn-cyan" href="{{url('admin/edit/reward-to-customer/'.Crypt::encryptString($reward->reward_to_customer_id ))}}">Edit</a>
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
 





            @endsection

