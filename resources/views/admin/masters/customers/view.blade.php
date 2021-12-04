@extends('admin.layouts.app')
@section('content')
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