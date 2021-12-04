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
                           <input type="hidden" class="form-control" name="company_id" value="{{$company->company_id}}" >
                           
                           <img class="avatar-xl rounded-circle mCS_img_loaded" src=" {{URL::to('assets/uploads/company/logos/'.$company->company_logo)}}" alt="img" style="width: 150px; height: 150px;">
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
      <input type="hidden" name="company_id" value="{{$company->company_id}}">
      <div class="card">
         <div class="card-body">
            <div class="border-0">
               <div class="tab-content">
                  <div class="tab-pane active show" id="tab-51">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>company Information</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Company Name :</strong> {{ $company->company_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Name :</strong>{{ $company->company_contact_person_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Number :</strong> {{ $company->company_contact_person_phone_number}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Number 2:</strong> {{ $company->company_contact_number_2}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Website Link :</strong>
                                    <a target="_blank" href="{{ $company->company_website_link}}" >{{ $company->company_website_link}}</a>

                                    </td>
                                 </tr>
                                    <tr>
                                    <td><strong> PinCode :</strong> {{ $company->company_pincode}}</td>
                                 </tr>
                               <tr>
                                    <td><strong>Business Type :</strong> {{ $company->business_type['business_type_name']}}</td>  
                                 </tr>
   
                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                     <td><strong>Email :</strong> {{ $company->company_email_address}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Country :</strong>{{ $company->country['country_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>State :</strong> {{ $company->state['state_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>District :</strong> {{ $company->district['district_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Username :</strong> {{ $company->company_username}}</td>  
                                 </tr>
                                  
                                   <tr>
                                     <td><strong>Address :</strong>{{ $company->company_primary_address}}</td>
   
                                 </tr>
                              </tbody>
                           </table>
                    
                           
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_company') }}">Cancel</a>
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