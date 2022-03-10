@extends('admin.layouts.app')
@section('content')
@php
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_store_order;

@endphp
<div class="row" id="user-profile">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-body">
            <div class="wideget-user">
               <h4>{{$pageTitle}}</h4>
            </div>
         </div>

         <div class="border-top">
            <div class="wideget-user-tab">
               <div class="tab-menu-heading">
                  <div class="tabs-menu1">
                     <ul class="nav">
                        <li class=""><a href="#tab-51" class="active show" data-toggle="tab">Customer Information</a></li>
                        <li class=""><a href="#tab-53" class=" show" data-toggle="tab">Wallet Points</a></li>
                        <li class=""><a href="#tab-52" class=" show" data-toggle="tab">Customer Address</a></li>
                       
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card">
         <div class="card-body">
            <div class="border-0">
               <div class="tab-content">
                  <div class="tab-pane active show" id="tab-51">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Customer Information</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Name:</strong> {{ $customers->customer_first_name}}
                                    @if(isset($customers->customer_last_name)) 
                                         {{ $customers->customer_last_name}}
                                    @endif
                                    </td>
                                 </tr>
                                 
                                 <tr>
                                    <td><strong>Moblie :</strong> {{ $customers->customer_mobile_number}}</td>
                                 </tr>
                                 
                                  @if(isset($customers->customer_email)) 
                                   <tr>
                                    <td><strong>Email :</strong> {{ $customers->customer_email}}</td>
                                   </tr>
                                  @endif
                                  
                                  @if(isset($customers->country['country_name'])) 
                                   <tr>
                                    <td><strong>Country :</strong> {{@$customers->country['country_name']}}</td>
                                   </tr>
                                  @endif
                                  @if(isset($customers->state['state_name'])) 
                                <tr>
                                    <td><strong>State :</strong> {{ @$customers->state['state_name']}}</td>
                                 </tr>
                                  @endif
                                  
                                   @if(isset($customers->district['district_name'])) 
                                <tr>
                                    <td><strong>District :</strong> {{ @$customers->district['district_name']}}</td>
                                 </tr>
                                  @endif
                                  
                                   @if(isset($customers->town['town_name'])) 
                                <tr>
                                    <td><strong>Town :</strong> {{ @$customers->town['town_name']}}</td>
                                 </tr>
                                  @endif
                                  @if(isset($customers->gender)) 
                                    <tr>
                                    <td><strong>Gender :</strong> {{ @$customers->gender}}</td>
                                 </tr>
                                  @endif
                                  
                                      @if(isset($customers->dob)) 
                                    <tr>
                                    <td><strong>DOB :</strong> {{ @$customers->dob}}</td>
                                 </tr>
                                  @endif
                                  
                                    @if(isset($customers->place)) 
                                    <tr>
                                    <td><strong>Place :</strong> {{ @$customers->place}}</td>
                                 </tr>
                                  @endif


                                  @if(isset($customers->customer_address)) 

                                   <tr>
                                     <td><strong>Address :</strong>{!! $customers->customer_address!!}</td>
                                    </tr> 
                                  @endif

                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                  @if(isset($customers->customer_username)) 
                                <tr>
                                    <td><strong> Username :</strong> {{ $customers->customer_username}}</td>
                                 </tr>
                                  @endif
                                  @if(isset($customers->customer_location)) 
                                 <tr>
                                    <td><strong>Location :</strong>{{ $customers->customer_location}}</td>
                                 </tr>
                                  @endif
                                  @if(isset($customers->customer_pincode)) 
                                 <tr>
                                    <td><strong>Pincode :</strong> {{ $customers->customer_pincode}}</td>
                                 </tr>
                                  @endif
                             
                                   
                              </tbody>
                           </table>
                    
                           
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_customer') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
                  
                  
                  
                  
                  <div class="tab-pane show" id="tab-53">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Wallet Points</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           @php
                           
                              $totalCustomerRewardsCount = Trn_customer_reward::where('customer_id', $customers->customer_id)->where('reward_point_status', 1)->sum('reward_points_earned');
                            $totalusedPoints = Trn_store_order::where('customer_id', $customers->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
            
                            $customerRewardsCount = ($totalCustomerRewardsCount - $totalusedPoints)-$redeemedPointsSum;
                            $customerRewardsCount = number_format($customerRewardsCount, 0);
                            $totalCustomerRewardsCount = number_format($totalCustomerRewardsCount, 0);
            
                            if ($totalusedPoints >= 0)
                                $data['totalusedPoints']  = $totalusedPoints + $redeemedPointsSum ;
                            else
                                $data['totalusedPoints']  = '0';
                    
                           @endphp
                         <p>Total Points Earned : </p><h2>{{$totalCustomerRewardsCount}}</h2> <br>
                         <p>Used Points : </p><h2>{{$totalusedPoints + $redeemedPointsSum}}</h2> <br>
                         <p>Balance Pointsd : </p><h2>{{$totalCustomerRewardsCount - ($redeemedPointsSum + $totalusedPoints) }}</h2> <br>

                         

                         <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                             <thead>
                               <tr>
                                 <th class="wd-15p">SL.No</th>
                                 <th class="wd-15p">{{ __('Date') }}</th>
                                 <th class="wd-15p">{{ __('Point') }}</th>
                                 <th class="wd-15p">{{ __('Description') }}</th>
                               </tr>
                             </thead>
                             <tbody>
                                 @php
                                    $i = 0;
                                 @endphp
                                 @foreach ($redeemedpoints as $s)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                        <td>
                           {{ \Carbon\Carbon::parse($s->created_at)->format('d-m-Y')}}
                           {{ \Carbon\Carbon::parse($s->created_at)->format('H:i')}}
                        
                        </td>
                        <td>{{ $s->points }}</td>
                                    <td>{{ $s->discription }}</td>
                                 </tr>
                                 @endforeach
                             </tbody>
                           </table>
                         </div>
                         
                           
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_customer') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
                  
                  
                   <div class="tab-pane show" id="tab-52">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Customer Address</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                @if(count($customerAddress) > 0)
                                @foreach($customerAddress as $val)
                                 <tr>
                                    <td><strong>{{ $loop->iteration }} :</strong> {{ $val->address}}</td>
                                 </tr>
                                 @endforeach
                                 @else
                                  <tr>
                                      No data available.
                                 </tr>
                                 @endif
                                 
                            
                              </tbody>
                      
                           </table>
                    
                           
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_customer') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
            

 {{-- </div>             
</div> --}}
</div>
</div>
</div>
</div>
@endsection