@extends('store.layouts.app')
@section('content')
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

                        </br>
                        <div class="table-responsive">
                           <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Contact Person') }}</th>
                                    <th class="wd-20p">{{__('Email')}}</th>

                                    <th class="wd-20p">{{__('Status')}}</th>

                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($agencies as $agency)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $agency->agency['agency_name']}}</td>
                                    <td>{{ $agency->agency['agency_contact_person_name']}} </td>
                                    <td>{{ $agency->agency['agency_email_address']}} </td>


                                    <td>
                                   <a class="btn btn-sm btn-info" href="{{route('store.assign_agency')}}"> Agency</a>

                                  <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{$agency->link_id}}" > View</button>

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
         @foreach($agencies as $agency)
            <div class="modal fade" id="viewModal{{$agency->link_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">

                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-12 p-0">
                                 <tr>
                                    <input type="hidden" class="form-control" name="agency_id" value="{{$agency->link_id}}" >
                                    <td><h6 class="">Agency Name :{{$agency->agency['agency_name']}}</h6> </td>
                                 </tr>
                                 <tr>
                                    <td><h6>Contact Pereson Name  : {{$agency->agency['agency_contact_person_name']}}</h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Contact Pereson Number :{{$agency->agency['agency_contact_person_phone_number']}}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Contact Number 2 :{{ $agency->agency['agency_contact_number_2'] }}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Website Link :{{ $agency->agency['agency_website_link'] }}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Pincode  :{{ $agency->agency['agency_pincode'] }}
                                   </h6></td>
                                 </tr>
                                <tr>
                                    <td><h6>Primary Address :{{ $agency->agency['agency_primary_address'] }}
                                   </h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Email Address :{{ $agency->agency['agency_email_address'] }}
                                   </h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Username :{{ @$agency->agency['agency_username'] }}
                                   </h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Logo :<img src="{{asset('/assets/uploads/agency/logos/'.@$agency->agency['agency_logo'])}}"  width="50" >
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Country :{{ @$agency->agency->country['country_name'] }}
                                   </h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>State :{{ @$agency->agency->state['state_name'] }}
                                   </h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>District :{{ @$agency->agency->district['district_name'] }}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>BusinessType :{{ @$agency->agency->business_type['business_type_name'] }}
                                   </h6></td>
                                 </tr>

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
            @endforeach
            <!-- MESSAGE MODAL CLOSED -->
            @endsection

