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
                      <div class="card-body border">
       <form action="{{route('admin.list_delivery_boy_payment_settlment')}}" method="GET"
                enctype="multipart/form-data">
          @csrf
            <div class="row">
               <div class="col-md-12">
                  <div class="form-group">
                     <label class="form-label">Delivery Boy</label>
                      <select class="form-control" name="delivery_boy_id">
                        <option value=""> Select Delivery Boy </option>

                              @foreach ($delivery_boy as $key)

                                 <option {{request()->input('delivery_boy_id') == $key->delivery_boy_id ? 'selected':''}} value=" {{ $key->delivery_boy_id}} "> {{ $key->delivery_boy_name }}
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
                          <a href="{{route('admin.list_delivery_boy_payment_settlment')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
    </div>
       </form>
    </div>
                     <div class="card-body">

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
                                    <th class="wd-20p">{{__('Delivery Boy Commision')}}</th>
                                    <th class="wd-20p">{{__('Delivery Boy')}}</th>
                                    <th class="wd-20p">{{__('Amount Paid')}}</th>
                                    <th class="wd-15p">{{__('Amount to be Paid')}}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>

                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($delivery_boy_payments as $delivery_boy_payment)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ @$delivery_boy_payment->store['store_name']}}</td>
                                    <td>{{ @$delivery_boy_payment->order['order_number']}}</td>
                                    <td>{{ @$delivery_boy_payment->total_amount }}</td>
                                     <td>{{ @$delivery_boy_payment->store_commision_amount }}</td>
                                      <td>{{ @$delivery_boy_payment->delivery_boy_commision_amount }}</td>
                                      <td>{{ @$delivery_boy_payment->delivery_boy['delivery_boy_name']}}</td>
                                    <td>{{ $delivery_boy_payment->commision_paid}}</td>
                                   <td>{{ $delivery_boy_payment->commision_to_be_paid}}</td>

                                   <td>
                                       <button type="button" class="btn btn-sm btn-cyan" data-toggle="modal" data-target="#StockModal{{$delivery_boy_payment->delivery_boy_settlment_id }}" > Status Update</button>
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
           @foreach($delivery_boy_payments as $delivery_boy_payment)
            <div class="modal fade" id="StockModal{{$delivery_boy_payment->delivery_boy_settlment_id  }}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.status_delivery_boy_payment',$delivery_boy_payment->delivery_boy_settlment_id) }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                    <label class="form-label">Order Status</label>
                    <input type="hidden" class="form-control" name="delivery_boy_settlment_id " value="{{$delivery_boy_payment->delivery_boy_settlment_id  }}" >

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
