@extends('admin.layouts.app')
@section('content')
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
<img class="avatar-xl rounded-circle mCS_img_loaded" src=" {{URL::to(assets/uploads/admin.png)}}" alt="img" style="width: 150px; height: 150px;">
   
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
                                    <td><strong>Country :</strong>{{ $delivery_boy->country['country_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>State :</strong> {{ $delivery_boy->state['state_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>District :</strong> {{ $delivery_boy->district['district_name']}}</td>
                                 </tr>
                                 <tr>
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
                                 </tr>
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
                                    <th class="wd-15p">{{ __('Order Delivered') }}</th>

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
                                    <td>{{ @$assigned_store->store['store_name']}}</td>
                                    <td>{{ @$assigned_store->store['store_contact_person_phone_number']}}</td>
                                    <td>{{ (new \App\Services\Helper)->countOrders($assigned_store->store_id,$delivery_boy->delivery_boy_id) }}</td>

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
             </div>
{{-- </div>
</div> --}}
</div>
</div>
</div>
@endsection
