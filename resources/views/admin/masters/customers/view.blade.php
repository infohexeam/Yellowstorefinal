@extends('admin.layouts.app')
@section('content')
@php
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_points_redeemed;

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
                        <li class=""><a href="#tab-52" class=" show" data-toggle="tab">Delivery Address</a></li>
                       
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card" style="min-height:70vh;" >
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
                                    <td><strong>Pincode :</strong> {{ @$customers->town['town_name']}}</td>
                                 </tr>
                                  @endif
                                 


                                

                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                  
                                    <tr>
                                    <td><strong>Moblie :</strong> {{ $customers->customer_mobile_number}}</td>
                                 </tr>
                                 
                                  @if(isset($customers->customer_email)) 
                                   <tr>
                                    <td><strong>Email :</strong> {{ $customers->customer_email}}</td>
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
                                    <td><strong>Current Location :</strong> {{ @$customers->place}}</td>
                                 </tr>
                                  @endif
                                  
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
                                    <td><strong>Current Location Pincode :</strong> {{ $customers->customer_pincode}}</td>
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
                           
                              //$totalCustomerRewardsCount = Trn_customer_reward::where('customer_id', $customers->customer_id)->where('reward_point_status', 1)->sum('reward_points_earned');
                            $totalCustomerRewardsCount= Trn_customer_reward::where('customer_id', $customers->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');
                            $totalusedPoints = Trn_store_order::where('customer_id', $customers->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                            $redeemedPointsSum=Trn_points_redeemed::where('customer_id', $customers->customer_id)->sum('points');;
                            $customerRewardsCount = ($totalCustomerRewardsCount - $totalusedPoints)-$redeemedPointsSum;
                           // $totalCustomerRewardsCount = number_format($totalCustomerRewardsCount, 0);
                             //$totalusedPoints=$totalusedPoints + $redeemedPointsSum ;
                             $tUsedPts=$totalusedPoints + $redeemedPointsSum;
                            if ($totalusedPoints >= 0)
                                $data['totalusedPoints']  = $totalusedPoints;
                            else
                                $data['totalusedPoints']  = '0';
                    
                           @endphp
                         <p>Total Points Earned : </p><h2>{{number_format(floor($totalCustomerRewardsCount),2)}}</h2> <br>
                         <p>Used Points : </p><h2>{{number_format(floor($totalusedPoints),2)}}</h2> <br>
                         <p>Admin Redeemed Points : </p><h2>{{number_format(floor($redeemedPointsSum),2)}}</h2> <br>
                         <p>Balance Points : </p><h2>{{number_format(floor($customerRewardsCount),2)}}</h2> <br>

                        <h3>Earned Points</h3>

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
                                 @foreach ($customerRewards as $row)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>
                                         {{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y')}}
                                    </td>
                                     <td>
                                                 
                                        {{ number_format(floor($row->reward_points_earned),2) }}</td>
                                    <td>
                                        @php
                                        $rewardDis = '';
                                          if (($row->discription == null) && ($row->discription == '')) {
                                                $orderInfo = Trn_store_order::find($row->order_id);
                                                $rewardDis = 'from order ' . $orderInfo->order_number;
                                            } else {
                                                $rewardDis = $row->discription;
                                            }
                                        @endphp
                                        {{ @$rewardDis }}</td>

                                 </tr>
                                 @endforeach
                             </tbody>
                           </table>
                         </div>
                         
                         
                        <h3>Admin Redeemed Points</h3>

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
                           <!--{{ \Carbon\Carbon::parse($s->created_at)->format('H:i')}}-->
                        
                        </td>
                        <td>{{ number_format(floor($s->points),2) }}</td>
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
                                 <td> 
                                 <h4><strong>Address {{ $loop->iteration }}  {{ ($val->default_status == 1)  ? '( Default )': '' }}</strong>  </h4>
                                 
                                    {{ $val->name}} <br/> {{ $val->phone}} <br/> {{ $val->address}} <br> {{ $val->place}} {{ $val->stateFunction->state_name }}, {{ $val->districtFunction->district_name}},  {{ $val->street}}  </td>
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