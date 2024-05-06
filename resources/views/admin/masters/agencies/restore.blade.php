@extends('admin.layouts.app')
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
                      
                         <a href=" {{ url('admin/agency/list') }}" class=" text-white btn btn-block btn-success">
                          List Agency
                        </a>
                        
                        </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Agency Name') }}</th>
                                    <th class="wd-15p">{{ __('Contact Person') }}</th>
                                    <th class="wd-20p">{{__('Email')}}</th>
                                    <th class="wd-20p">{{__('Country')}}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($agencies as $agency)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $agency->agency_name}}</td>
                                    <td>{{$agency->agency_contact_person_name}} </td>
                                    <td>{{ $agency->agency_email_address}} </td>
                                    <td>{{ $agency->country['country_name']}} </td>
                                
                                    <td>
                                        
                                        <form action="{{route('admin.restore_agency',$agency->agency_id)}}" method="POST">
                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to restore this item?');"  class="btn btn-sm btn-warning">Restore</button>
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

            <!-- MESSAGE MODAL CLOSED -->
            @endsection

