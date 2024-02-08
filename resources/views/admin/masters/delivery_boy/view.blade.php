@extends('admin.layouts.app')
@section('content')
@php
$date = Carbon\Carbon::now();
use App\Models\admin\Trn_store_order;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Mst_delivery_boy;
use App\User;

@endphp

<div class="row" id="user-profile">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-body">
            <div class="wideget-user">
              {{--  <h4>{{$pageTitle}}</h4> --}}
                     <div class="row">
                  <div class="col-lg-6 col-md-12">
                     <div class="wideget-user-desc d-sm-flex">
                        <div class="wideget-user-img">
                           <input type="hidden" class="form-control" name="delivery_boy_id" value="{{$delivery_boy->delivery_boy_id}}" >
                           @if (isset($delivery_boy->delivery_boy_image))
                           <img class="avatar-xl rounded-circle mCS_img_loaded" src=" {{URL::to('assets/uploads/delivery_boy/images/'.$delivery_boy->delivery_boy_image)}}" alt="img" style="width: 150px; height: 150px;">
                           @else
                           <img class="avatar-xl rounded-circle mCS_img_loaded" src=" {{URL::to('assets/uploads/admin.png')}}" alt="img" style="width: 150px; height: 150px;">
                              
                           @endif
                        </div>

                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="border-top">
            <div class="wideget-user-tab">
               <div class="tab-menu-heading">
                  <div class="tabs-menu1">
                     <ul class="nav">
                        <li class=""><a href="#tab-51" class="active show"
                           data-toggle="tab">Profile</a></li>
                            <li><a href="#tab-61" data-toggle="tab" class="">Stores</a></li>
                            <li><a href="#tab-71" data-toggle="tab" class="">Delivery boy orders</a></li>

                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" name="delivery_boy_id" value="{{$delivery_boy->delivery_boy_id}}">
      <div class="card">
         <div class="card-body">
            <div class="border-0">
               <div class="tab-content">
                  <div class="tab-pane active show" id="tab-51">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Delivery boy Information</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong> Name :</strong> {{ $delivery_boy->delivery_boy_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Mobile:</strong>{{ $delivery_boy->delivery_boy_mobile}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Email :</strong> {{ $delivery_boy->delivery_boy_email}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Vehicle Type:</strong> {{ $delivery_boy->vehicle_type['vehicle_type_name'] }}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>UserName:</strong> {{ $delivery_boy->delivery_boy_username}}</td>
                                 </tr>
                                    <tr>
                                    <td><strong> Vehicle Number :</strong> {{ $delivery_boy->vehicle_number}}</td>
                                 </tr>
                               <tr>
                                    <td><strong>Address :</strong> {{ $delivery_boy->delivery_boy_address}}</td>
                                 </tr>

                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">

                                 <tr>
                                    <td><strong>Country :</strong>{{ @$delivery_boy->country['country_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>State :</strong> {{ @$delivery_boy->state['state_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>District :</strong> {{ @$delivery_boy->district['district_name']}}</td>
                                 </tr>
                                  <tr>
                                  @php
                                  $twn=DB::table('mst_towns')->where('town_id',$delivery_boy->town_id)->first();
                                  @endphp
                                    <td><strong>Pincode :</strong>  {{@$twn->town_name}}</td>
                                 </tr>
                                
                         {{--        <tr>
                                    <td><strong>Availability :</strong>
                                    @php
                                       @$avilabilityArray = explode(",", @$delivery_boy->delivery_boy_availability_id);
                                       $com = 0;
                                    @endphp

                                    @foreach ($avilabilityArray as $a)
                                    @if($com != 0)
                                     ,
                                     @endif
                                     @php
                                     $com++;
                                        @$av = \DB::table('sys_delivery_boy_availabilities')->where('availability_id',$a)->first();
                                     @endphp

                                   

                                       {{ @$av->availabilable_days }} 
                                    @endforeach
                                      </td>
                                 </tr> --}}
                                  <tr>
                                    <td><strong>Commission Amount (Monthly) : </strong>
                                     {{ @$delivery_boy->delivery_boy_commision}}
                                      </td>
                                 </tr>
                                  <tr>
                                    <td><strong>Commission Amount (Per Order) : </strong>
                                     {{ @$delivery_boy->delivery_boy_commision_amount}}
                                      </td>
                                 </tr>


                              </tbody>
                           </table>


                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_delivery_boy') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                 </div>
                  <div class="tab-pane" id="tab-61">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Stores</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Store') }}</th>
                                    <th class="wd-15p">{{ __('Phone') }}</th>

                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                @if(!$assigned_stores->isEmpty())
                                 @foreach ($assigned_stores as $assigned_store)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{ @$assigned_store->store['store_name']}}@if(@$assigned_store->store['store_code']!=NULL)-{{@$assigned_store->store['store_code']}} @endif</td>
                                    <td>{{ @$assigned_store->store['store_contact_person_phone_number']}}</td>

                                 </tr>
                                 @endforeach
                                 @else
                                 <tr>
                                <td colspan="2"><center> No data available in the table</center></td>
                                  </tr>
                                  @endif
                              </tbody>
                           </table>
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_delivery_boy') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
                  
                  
                  <div class="tab-pane" id="tab-71">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <!--<h5><strong>Delivery boy Orders</strong></h5>-->
                        </div>
                         <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                     <tr>
                        <th class="wd-15p">SL.No</th>
                        <th class="wd-15p">{{ __('Order Date') }}</th>

                         <th class="wd-15p">{{ __('Order Number') }}</th>
                        <!--<th class="wd-15p">{{ __('Delivery Boy') }}</th>-->
                        <!--<th class="wd-15p">{{ __('Delivery Mobile') }}</th>-->
                        <th class="wd-20p">{{__('Store')}}</th>
                        <th class="wd-20p">{{__('Subadmin')}}</th>

                         <th class="wd-20p">{{__('Status')}}</th>
                        <!--<th class="wd-15p">{{__('Action')}}</th>-->
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $i = 0;
                     @endphp
                     @foreach ($delivery_boy_orders as $delivery_boy_order)
                     <tr>
                        @php
                           $orderDAta = Trn_store_order::find($delivery_boy_order->order_id);
                           $dBoyDAta = Mst_delivery_boy::find($delivery_boy_order->delivery_boy_id);
                           $subadmin = User::find($delivery_boy_order->subadmin_id);
                           $statusInfo = Sys_store_order_status::find($delivery_boy_order->status_id);
                        @endphp
                        <td>{{ ++$i }}</td>
                        <td>{{ \Carbon\Carbon::parse($orderDAta->updated_at)->format('M d, Y')}}</td>
                        <td>{{ @$orderDAta->order_number}}</td>
                        <!--<td>{{ @$dBoyDAta->delivery_boy_name }}</td>-->
                        <!--<td>{{ @$dBoyDAta->delivery_boy_mobile }}</td>-->
                        <td>{{@$delivery_boy_order->store['store_name']}}</td>
                        <td>{{@$subadmin->name}}</td>



                       <td>
                            {{@$statusInfo->status }}
                       </td>
                       <!--<td>-->
                       <!-- <a class="btn btn-sm btn-blue"href="{{url('admin/order/view/'.Crypt::encryptString($delivery_boy_order->order_id))}}">View</a>-->

                       <!--</td>-->

                        {{-- <td>
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{$delivery_boy_order->delivery_boy_order_id}}" > View</button>

                        </td> --}}
                     </tr>
                     @endforeach
                  </tbody>
                </table>
                        </div>
                     </div>
                 </div>
                 
             </div>
{{-- </div>
</div> --}}
</div>
</div>
</div>
@endsection
