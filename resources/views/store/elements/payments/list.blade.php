@extends('store.layouts.app')
@section('content')
@php
$date = Carbon\Carbon::now();
@endphp
<div class="container">
<div class="row justify-content-center" style="min-height: 70vh;">
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
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
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
            <form action="{{ url('store/incoming-payments')}}" method="GET" enctype="multipart/form-data">
               @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">From Date</label>
                               <input type="date" class="form-control"  name="date_from" id="date_fromc"  value="{{@$datefrom}}" placeholder="From Date">
        
                         </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">To Date</label>
                             <input type="date" class="form-control" name="date_to"   id="date_toc" value="{{@$dateto}}" placeholder="To Date">
                        </div>
                     </div>
                  
                     
                     <div class="col-md-12">
                        <div class="form-group">
                            <center>
                               <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i> Filter</button>
                               <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button>
                               <a href="{{ url('store/incoming-payments')}}"  class="btn btn-info">Cancel</a>
                            </center>
                        </div>
                      </div>
 
 
                </div>
            </form>
        </div>

        
        <div class="card-body">
            <div class="table-responsive">
               <table id="exampletable" class="table table-striped table-bpaymented text-nowrap w-100">
                  <thead>
                      <tr>
                        <th class="wd-15p">SL.No</th>
                        <th class="wd-15p">Order<br>number</th>
                        <th class="wd-15p">{{ __('Customer') }}</th>
                        <th class="wd-15p">Customer Mobile</th>
                                               <th class="wd-20p">Reference Id</th>

                        <th class="wd-20p">Total<br>amount</th>
                        <th class="wd-20p">Split<br>Amount</th>
                        <th class="wd-20p">Delivery<br>Charge</th>
                        <th class="wd-20p">Transaction<br>Status</th>
                     </tr>
                     
                  </thead>
                  <tbody>
                     @php
                     $i = 0;
                     $store_distint[] = 0;
                     @endphp

                      @foreach ($payments as $row)
                     <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ @$row->order_number }}</td>
                        <td>{{ (new App\Helpers\Helper)->findCustomerName($row->customer_id) }}</td>
                        <td>{{ (new App\Helpers\Helper)->findCustomerPhone($row->customer_id) }}</td>
                        <td>{{ @$row->referenceId }}</td>

                        <td>{{ @$row->orderAmount }}</td>
                        <td>{{ @$row->settlementAmount }}</td>
                        <td>{{ @$row->delivery_charge }}</td>
                        <td>{{ @$row->txStatus }}</td>
                       

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
   </div>
</div>



@foreach($payments as $row)
            <div class="modal fade" id="viewModal{{$row->opt_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog modal-lg" role="document">
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
                              <tbody class="col-lg-12 col-xl-6 p-0">
                           
                                  <tr>
                                    <td><h6>Order Number : {{ @$row->order_number }}</h6></td>
                                 </tr>
                                 
                                <tr>
                                    <td><h6>Customer Name : {{ (new App\Helpers\Helper)->findCustomerName($row->customer_id) }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Customer Phone : {{ (new App\Helpers\Helper)->findCustomerPhone($row->customer_id) }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Order Amount :{{ @$row->orderAmount }}</h6></td>
                                 </tr>
                                
                                 <tr>
                                    <td><h6>Split Amount :{{ @$row->settlementAmount }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Payment Gateway Order ID :{{ @$row->PGOrderId }}</h6></td>
                                 </tr>
                                 
                                  <tr>
                                    <td><h6>Transaction Status :{{ @$row->txStatus }}</h6></td>
                                 </tr>
                                 
                                
                              </tbody>
                              
                            
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                  
                                 <!--<tr>-->
                                 <!--   <td><h6>Store Name  : {{ (new App\Helpers\Helper)->findStoreName($row->store_id) }}</h6></td>-->
                                 <!--</tr>-->
                                 
                                 <!--<tr>-->
                                 <!--   <td><h6>Store Phone  : {{ (new App\Helpers\Helper)->findStorePhone($row->store_id) }}</h6></td>-->
                                 <!--</tr>-->
                                 
                                 <!--<tr>-->
                                 <!--   <td><h6>Subadmin Name  : {{ (new App\Helpers\Helper)->findSubAdminName($row->store_id) }}</h6></td>-->
                                 <!--</tr>-->
                                 
                                 <tr>
                                    <td><h6>Delivery Charge :{{ @$row->delivery_charge }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Payment Mode :{{ @$row->paymentMode }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Reference ID :{{ @$row->referenceId }}</h6></td>
                                 </tr>
                                 
                                 
                                
                              
                                 
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



<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script type="text/javascript">





$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Incoming Payments',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9]
                 }
            },
            {
                extend: 'excel',
                title: 'Incoming Payments',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9]
                 }
            }
         ]
    } );

} );
</script>

@endsection
