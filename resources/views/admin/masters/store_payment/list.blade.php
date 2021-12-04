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
       <form action="{{route('admin.list_payment_settlment')}}" method="GET" 
                enctype="multipart/form-data">
          @csrf
            <div class="row">
               <div class="col-md-12">
                  <div class="form-group">
                     <label class="form-label">Store</label>
                      <select class="form-control" name="store_id">
                        <option value=""> Select Store </option>
                     
                              @foreach ($store as $key)
                                 
                                 <option {{request()->input('store_id') == $key->store_id ? 'selected':''}} value=" {{ $key->store_id}} "> {{ $key->store_name }}
                                 </option>
                                 @endforeach
                              
                           </select>
                  </div>
               </div> 
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('admin.list_payment_settlment')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
              </div>
                 </form>
              </div>
                        </br>
                        <div class="table-responsive">
                           <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Store') }}</th>
                                    <th class="wd-15p">{{ __('Order Number') }}</th>
                                    <th class="wd-15p">{{ __('Total') }}</th>
                                     <th class="wd-20p">{{__('Store Commision')}}</th>
                                      <th class="wd-20p">{{__('Admin Commision')}}</th>
                                    <th class="wd-20p">{{__('Amount Paid')}}</th>
                                    <th class="wd-15p">{{__('Amount to be Paid')}}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>

                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($store_payment_settlments as $store_payment_settlment)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $store_payment_settlment->store['store_name']}}</td>
                                    <td>{{ $store_payment_settlment->order['order_number']}}</td>
                                    <td>{{ $store_payment_settlment->total_amount }}</td>
                                     <td>{{ $store_payment_settlment->store_commision_amount }}</td>
                                      <td>{{ $store_payment_settlment->admin_commision_amount }}</td>
                                    <td>{{ $store_payment_settlment->commision_paid}}</td>
                                   <td>{{ $store_payment_settlment->commision_to_be_paid}}</td>
                                   
                                   <td>
                                       <button type="button" class="btn btn-sm btn-cyan" data-toggle="modal" data-target="#StockModal{{$store_payment_settlment->settlment_id}}" > Status Update</button>
                               </td> 
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
           @foreach($store_payment_settlments as $store_payment_settlment)
            <div class="modal fade" id="StockModal{{$store_payment_settlment->settlment_id }}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                           
                 <form action=" {{ route('admin.status_store_payment',$store_payment_settlment->settlment_id ) }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">
           
                    <label class="form-label">Order Status</label>
                    <input type="hidden" class="form-control" name="settlment_id" value="{{$store_payment_settlment->settlment_id }}" >
                    
                  <input type="text" name="commision_paid" placeholder="Commision" class="form-control" required="" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
                  </div>
              
                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
            @endforeach
            <!-- MESSAGE MODAL CLOSED -->
            @endsection