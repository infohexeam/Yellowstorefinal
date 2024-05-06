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
                        <a href=" {{route('admin.create_reward_transaction_type')}}" class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create Reward Transaction
                        </a>
                        </br>
                        <div class="table-responsive">
                           <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Type') }}</th>
                                    <th class="wd-15p">{{ __('Rule') }}</th>
                                    <th class="wd-20p">{{__('Point Value')}}</th>
                                    <th class="wd-20p">{{__('Earning Point')}}</th>
                                  
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($reward_transactions as $reward_transaction)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ substr($reward_transaction->transaction_type, 0,50)}}</td>
                                    <td>{{substr($reward_transaction->transaction_rule, 0,30)}} </td>
                                    <td>{{ $reward_transaction->transaction_point_value}} </td>
                                    <td>{{ $reward_transaction->transaction_earning_point}} </td>
                                   
                                    <td>
                            <form action="{{route('admin.destroy_reward_transaction_type',$reward_transaction->transaction_type_id)}}" method="POST">
                                    <a class="btn btn-sm btn-cyan" href="{{url('admin/reward_transaction_type/edit/'.Crypt::encryptString($reward_transaction->transaction_type_id))}}">Edit</a> 
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{$reward_transaction->transaction_type_id }}" > View</button>
                                          @csrf
                                          @method('POST')
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
 
            <!-- MESSAGE MODAL CLOSED -->
            @foreach($reward_transactions as $reward_transaction)
            <div class="modal fade" id="viewModal{{$reward_transaction->transaction_type_id }}" tabindex="-1" role="dialog"  aria-hidden="true">
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
                                    <input type="hidden" class="form-control" name="reward_transaction_id" value="{{$reward_transaction->transaction_type_id }}" >
                                    <td><h6 class="">Transaction Type :{{$reward_transaction->transaction_type}}</h6> </td>
                                 </tr>
                                 <tr>
                                    <td><h6>Transaction Rule : {{$reward_transaction->transaction_rule}} </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Transaction Point: {{ $reward_transaction->transaction_point_value }}
                                    </h6></td>
                                 </tr>
                                <tr>
                                    <td><h6>Transaction Earning Point:
                                     
                                       {{ $reward_transaction->transaction_earning_point }}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Minimum Purchase Amount :<br><br>
                                    {{ $reward_transaction->min_purchase_amount }}</h6></td>
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

