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
                        <h5>Whoops!</h5> There were some problems with your input.<br><br>
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
                    {{-- <div class="card-body border">

                </div> --}}

                    <div class="card-body" style="min-height: 70vh;">
                        <a href="  {{route('store.create_store_admin')}} " class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Add Store Admin
                        </a>
                        </br>
                        <div class="table-responsive">
                           <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Phone') }}</th>
                                    <th class="wd-15p">{{ __('Username') }}</th>
                                    <th class="wd-20p">{{__('Role')}}</th>
                                    <th class="wd-20p">{{__('Active Status')}}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($store_admin as $user)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $user->admin_name}}</td>
                                    <td>{{$user->store_mobile}} </td>
                                    <td>{{$user->username}} </td>
                                    <td> 
                                      @if($user->role_id == '2')
                                     Admin
                                      @elseif($user->role_id == '3')
                                          Manager
                                      @elseif($user->role_id == '4')
                                          Staff
                                          @else

                                          @endif
                                         
                                    </td>
                                   

                                 <td>
                                  @if($user->store_account_status == 0)
                                          Inactive
                                          @else
                                          Active
                                          @endif
                                         
                                    </td>
                                    <td>
                                       <form action="{{route('store.destroy_store_admin',$user->store_admin_id)}}" method="POST">
                                         @csrf
                                          @method('POST')
                                          <a class="btn btn-sm btn-cyan"  href="{{url('store/admin/edit/'.
                                          Crypt::encryptString($user->store_admin_id))}}">Edit</a>
                                          <a style="color:white;" class="btn btn-sm btn-cyan" data-toggle="modal" data-target="#viewModal{{$user->store_admin_id}}">View</a>
                                          <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
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

  @foreach($store_admin as $user)
            <div class="modal fade" id="viewModal{{$user->store_admin_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
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
                                    <td><h5 class=""><b>Name</b> : {{$user->admin_name}}</h5> </td>
                                 </tr>
                                 <tr>
                                    <td><h5><b>Username</b> : {{ $user->username }}  </h5></td>
                                 </tr>
                                  <tr>
                                    <td><h5><b>Phone</b> : {{ $user->store_mobile }}  </h5></td>
                                 </tr>
                                  <tr>
                                    <td><h5><b>Email</b> : {{ $user->email }}  </h5></td>
                                 </tr>
                                 <tr>
                                    <td><h5><b>Role</b> :   
                                    @if($user->role_id == '2')
                                          Admin
                                          @elseif($user->role_id == '3')
                                          Manager
                                           @elseif($user->role_id == '4')
                                           Staff
                                           @else
                                           --
                                          @endif
                                    </h5></td>
                                 </tr>
                                  <tr>
                                    <td><h5><b>Status</b> :   
                                    @if($user->store_account_status == 0)
                                          Inactive
                                          @else
                                          Active
                                          @endif
                                    </h5></td>
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
