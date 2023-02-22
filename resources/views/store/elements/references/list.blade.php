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
                 <form action="{{route('store.list_references')}}" method="GET"
                         enctype="multipart/form-data">
                   @csrf
            <div class="row">

                


             


                  {{-- @php
                   if(!@$datefrom)
                   {
                        $datefrom = $date->toDateString();
                   }

                    if(!@$dateto)
                   {
                        $dateto = $date->toDateString();
                   }
               @endphp --}}

                 <div class="col-md-6">
                 <div class="form-group">
                    <label class="form-label">From Date</label>
                    <div id="date_froml"></div>
                     <input type="date" class="form-control" name="date_from" id="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date">

                  </div>
               </div>
                 <div class="col-md-6">
               <div class="form-group">
                    <label class="form-label">To Date</label>
                    <div id="date_tol"></div>
                     <input type="date" class="form-control"  name="date_to"  id="date_to" value="{{ request()->input('date_to') }}" placeholder="To Date">

                  </div>
               </div>
           </div>
            <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                          <a href="{{route('store.list_references')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                   </form>
               </div>

                    <div class="card-body">
                       
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-10p">Referred on</th>
                                    <th class="wd-15p">Reference Completed</th>
                                    <th class="wd-15p">Referred By</th>
                                    <th class="wd-15p">Joined By</th>
                                    <th class="wd-15p">Store<br>Name</th>
                                    <th class="wd-10p">FOP</th>
                                    <th class="wd-20p">Referral Points</th>
                                    <th class="wd-20p">Joiner Points</th>
                                    <th class="wd-20p">Order Number</th>
                                     <th class="wd-20p">status</th>
                                    
                                    
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($references as $reference)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reference->created_at)->format('M d, Y')}}</td>

                                       @php
                                       
                                        $store = \DB::table('mst_stores')->where('store_id',$reference->store_id)->first();
                                        //$order = \DB::table('trn_store_orders')->where('store_id',$reference->store_id)->first();
                                        $order = \DB::table('trn_store_orders')->where('order_id',$reference->order_id)->first();
                                        $refer_by=\DB::table('trn_store_customers')->where('referral_id',$reference->refered_by_number)->first();
                                        $joined_by=\DB::table('trn_store_customers')->where('customer_id',$reference->joined_by_id)->first();
                                        @endphp
                                       

                                    <td>
                                    @if(@$reference->reference_status==1)
                                    {{ \Carbon\Carbon::parse($reference->updated_at)->format('M d, Y')}}
                                    @else
                                    Not Completed

                                    @endif
                                    </td>

                                    <td>@if($refer_by) {{@$refer_by->customer_first_name}} {{@$refer_by->customer_last_name}} @endif</td>
                                    <td>@if($joined_by) {{@$joined_by->customer_first_name}} {{@$joined_by->customer_last_name}} @endif</td>
                                    <td>{{ @$store->store_name}}</td>
                                    <td>{{ @$reference->fop}}</td>
                                    <td>{{ @$reference->referral_points}}</td>
                                    <td>{{ @$reference->joiner_points }}</td>
                                   <td>@if($order){{$order->order_number}}@endif</td> 
                                   <td>@if(@$reference->reference_status==1) Success @else Failed @endif</td>
                      

                                    {{-- <td>{{ $dispute->discription}}</td> --}}
                                  
                                 {{-- <td>
                                    <form action="#" method="GET">
                                          @csrf
                                          @method('GET')
                                          <button type="submit" onclick="return confirm('Do you want to delete this dispute?');"  class="btn btn-sm btn-danger">Delete</button>
                                       </form>
                                 </td> --}}
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
            <!-- MESSAGE MODAL CLOSED -->





<!-- MESSAGE MODAL CLOSED -->


                      <script>
 $(document).ready(function() {
$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Store Referreneces',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9]
                 }
            },
            {
                extend: 'excel',
                title: 'Store Referreneces',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9]
                 }
            }
         ]
    } );

} );
} );
</script>





@endsection


