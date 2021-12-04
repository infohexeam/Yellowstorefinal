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


                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Subadmin') }}</th>
                                    <th class="wd-15p">{{ __('Phone') }}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>

                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($subadmins as $value)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $value->name}}</td>
                                    <td>{{ @$value->subadmins['phone']}}</td>

                                   <td>
                                       {{-- <button type="button" class="btn btn-sm btn-cyan" data-toggle="modal" data-target="#StockModal{{$store_payment_settlment->settlment_id}}" > Status Update</button> --}}
                                      <a class="btn btn-sm btn-cyan"
                                       href="{{url('admin/subadmin/payment_settlment/list/'.$value->name.'/'.Crypt::encryptString($value->id))}}
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
                title: 'Subadmins',
                footer: true,
                exportOptions: {
                     columns: [0,1,2]
                 }
            },
            {
                extend: 'excel',
                title: 'Subadmins',
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
