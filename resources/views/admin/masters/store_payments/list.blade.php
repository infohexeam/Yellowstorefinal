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

                     {{-- <div class="card-body">
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
              </div> --}}
                        </br> 
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Store') }}</th>
                                    <th class="wd-15p">{{ __('Phone') }}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>

                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($store as $value)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $value->store_name}}@if($value->store_code!=NULL)-{{$value->store_code}} @endif</td>
                                    <td>{{ $value->store_contact_person_phone_number}}</td>

                                   <td>
                                       {{-- <button type="button" class="btn btn-sm btn-cyan" data-toggle="modal" data-target="#StockModal{{$store_payment_settlment->settlment_id}}" > Status Update</button> --}}
                                      <a class="btn btn-sm btn-cyan"
                                       href="{{url('admin/stores/payment_settlment/list/'.$value->store_name.'/'.Crypt::encryptString($value->store_id))}}
                                       " > Make Payment</a>
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

                    <script>

$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Stores',
                footer: true,
                exportOptions: {
                     columns: [0,1,2]
                 }
            },
            {
                extend: 'excel',
                title: 'Stores',
                footer: true,
                exportOptions: {
                     columns: [0,1,2]
                 }
            }
         ]
    } );

} );
            </script>

            <!-- MESSAGE MODAL CLOSED -->
            @endsection
