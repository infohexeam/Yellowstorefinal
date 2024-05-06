@extends('admin.layouts.app')
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
      <div class="card">
        <div class="row">
          <div class="col-12">
            
            
            @if ($message = Session::get('status'))
            <div class="alert alert-success">
              <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
            </div>
            @endif
             @if ($message = Session::get('err_status'))
            <div class="alert alert-danger">
              <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
            </div>
            @endif
            <div class="col-lg-12">
              @if ($errors->any())
              <div class="alert alert-danger">
                <strong>Whoops!</strong>
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
                  
                   
                </div>
             
               <div class="card-body">
                         <a href="{{route('admin.list_delivery_boy')}}" class="btn btn-block btn-success">
                           <i class="fa fa-plus"></i>
                           List Delivery Boys
                        </a> <br/>
                        
                      
                        
                <div class="table-responsive">
                  <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                      <tr>
                        <th class="wd-15p">SL.No</th>
                        <th class="wd-15p">{{ __('Name') }}</th>
                        <th class="wd-15p">{{ __('Mobile') }}</th>
                        <th class="wd-15p">{{ __('Email') }}</th>
                       
                       <th class="wd-15p">{{__('Action')}}</th>
                       
                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $i = 0;
                      @endphp
                      @foreach ($delivery_boys as $db)
                      <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{$db->delivery_boy_name}}</td>
                        <td>{{$db->delivery_boy_mobile}}</td> 
                        <td>{{$db->delivery_boy_email}}</td>
                      
                       
                      
                     
                   <td>

                       <a class="btn btn-sm btn-warning"
                       href="{{url('admin/delivery_boy/restore/'.@$db->delivery_boy_id)}}">Restore</a> 
                       
                     
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
    </div>
  </div>

 



            
  @endsection
 