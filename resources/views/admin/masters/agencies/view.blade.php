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
                           <input type="hidden" class="form-control" name="agency_id" value="{{$agency->agency_id}}" >

                           <img class="avatar-xl rounded-circle mCS_img_loaded" src=" {{URL::to('assets/uploads/agency/logos/'.$agency->agency_logo)}}" alt="img" style="width: 150px; height: 150px;">
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

                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" name="agency_id" value="{{$agency->agency_id}}">
      <div class="card">
         <div class="card-body">
            <div class="border-0">
               <div class="tab-content">
                  <div class="tab-pane active show" id="tab-51">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Agency Information</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Agency Name:</strong> {{ $agency->agency_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Name:</strong> {{ $agency->agency_contact_person_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Number:</strong> {{ $agency->agency_contact_person_phone_number}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Number 2:</strong> {{ $agency->agency_contact_number_2}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Website Link:</strong> 
                                    <a target="_blank" href="{{ $agency->agency_website_link}}" >{{ $agency->agency_website_link}}</a>
                                    </td>
                                 </tr>
                                    <tr>
                                    <td><strong> PinCode:</strong> {{ $agency->agency_pincode}}</td>
                                 </tr>
                               <tr>
                                    <td><strong>Business Type:</strong> {{ $agency->business_type['business_type_name']}}</td>
                                 </tr>

                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                     <td><strong>Email:</strong> {{ $agency->agency_email_address}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Country:</strong> {{ $agency->country['country_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>State:</strong> {{ $agency->state['state_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>District:</strong> {{ $agency->district['district_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Username:</strong> {{ $agency->agency_username}}</td>
                                 </tr>

                                   <tr>
                                     <td><strong>Address:</strong> {{ $agency->agency_primary_address}}</td>

                                 </tr>
                              </tbody>
                           </table>


                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_agency') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                 </div>
             </div>


</div>
</div>
</div>
@endsection
