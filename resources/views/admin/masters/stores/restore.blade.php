@extends('admin.layouts.app')
@section('content')
<div class="container">
   <div class="row justify-content-center " >
      <div class="col-md-12 col-lg-12">
         <div class="card">
            <div class="row" style="min-height: 72vh;">
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
                        <a href="  {{  url('/admin/store/list')}} " class="btn btn-block btn-success">
                           List Store
                        </a>
                        </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL<br>No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Mobile')}}</th>
                                    <th class="wd-20p">{{__('Email')}}</th>
                                     @if(auth()->user()->user_role_id  == 0)
                                    <th class="wd-20p">Sub<br>Admin</th>
                                    @endif

                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($stores as $store)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $store->store_name}}</td>
                                   
                                    <td>{{$store->store_mobile}} </td>
                                    <td>{{ $store->email}} </td>
                                    @if(auth()->user()->user_role_id  == 0)
                                    <td>{{ @$store->subadmin->name}} </td>
                                    @endif
                               
                                    <td>
                                       <form action="{{route('admin.restore_store',$store->store_id)}}" method="POST">
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

     
            @endsection


