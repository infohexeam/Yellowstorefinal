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
                        <a href=" {{ url('admin/subadmin/list')}}" class="btn btn-block btn-success">
                           List Sub Admin
                        </a>
                        
                      
                        </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Mobile') }}</th>
                                    <th class="wd-15p">{{ __('Pincode') }}</th>
                                    <th class="wd-15p">{{ __('Commision') }}<br> Amount</th>
                                    <th class="wd-15p">{{ __('Commision') }}<br> Perentage</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($subadmins as $subadmin)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ @$subadmin->name}}</td>
                                    <td>{{ @$subadmin->subadmins->phone}}</td>
                                    <td>{{ @$subadmin->subadmins->town['town_name']}}</td>
                                    <td>{{ @$subadmin->subadmins->subadmin_commision_amount}}</td>
                                    <td>{{ @$subadmin->subadmins['subadmin_commision_percentage']}}</td>
                                   

                                    <td>
                                        
                                         <form action="{{route('admin.restore_subadmin',$subadmin->id)}}" method="POST">
                                     <a class="btn btn-sm btn-success text-white" data-toggle="modal" data-target="#viewModal{{$subadmin->id}}" >View</a>
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



@foreach($subadmins as $subadmin)
            <div class="modal fade" id="viewModal{{$subadmin->id}}" tabindex="-1" role="dialog"  aria-hidden="true">
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
                                    <td><h6>Name:</td><td>{{ @$subadmin->name}}</h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Phone:</td><td>{{  @$subadmin->subadmins->phone }}</h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Pincode:</td><td>{{ @$subadmin->subadmins->town['town_name']}}</h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Address:</td><td>{{ @$subadmin->subadmins['subadmin_address']}}</h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Commision Amount:</td><td>{{ @$subadmin->subadmins['subadmin_commision_amount']}}</h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Commision Perentage:</td><td>{{ @$subadmin->subadmins['subadmin_commision_percentage']}}</h6></td>
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


@endsection
