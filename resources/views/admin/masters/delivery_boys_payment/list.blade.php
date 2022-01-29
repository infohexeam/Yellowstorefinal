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
       <form action="" method="GET"
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
                          <a href="{{route('admin.list_delivery_boys_payment_settlment')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
    </div>
       </form>
    </div>
                     <div class="card-body">

                        </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-20p">{{__('Delivery Boy')}}</th>
                                    <th class="wd-20p">{{__('Delivery Phone')}}</th>
                                    <th class="wd-15p">{{ __('Store') }}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>

                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($delivery_boys as $delivery_boy)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ @$delivery_boy->delivery_boy_name}}</td>
                                    <td>{{ @$delivery_boy->delivery_boy_mobile}}</td>
                                    @php
                                        $store_data = \DB::table('mst_store_link_delivery_boys')->select('store_id')
                                        ->where('delivery_boy_id',$delivery_boy->delivery_boy_id)->get();
                                    @endphp
                                    <td>
                                       @foreach ( $store_data  as  $s)
                                           {{-- {{$s->store['store_name']}} --}}
                                           @php
                                             $store_name = \DB::table('mst_stores')->select('store_name')
                                             ->where('store_id',$s->store_id)->first();
                                          @endphp
                                          {{@$store_name->store_name}} <br>
                                       @endforeach

                                    </td>

                                   <td>
                                       {{-- <button type="button" class="btn btn-sm btn-cyan" data-toggle="modal" data-target="#StockModal{{$delivery_boy_payment->delivery_boy_settlment_id }}" > View Payments</button> --}}
                                       <a class="btn btn-sm btn-cyan"
                                       href="{{url('admin/delivery_boys/payment_settlment/list/'.$delivery_boy->delivery_boy_name.'/'.Crypt::encryptString($delivery_boy->delivery_boy_id))}}
                                       " > Make Payment</button>
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
                title: 'Delivery Boys',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3]
                 }
            },
            {
                extend: 'excel',
                title: 'Delivery Boys',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3]
                 }
            }
         ]
    } );

} );
            </script>

             @endsection
