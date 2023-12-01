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

              {{--   <div class="col-md-6">
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
                       {{--   <a href="{{route('store.list_references')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>--}}
                   </form>
               </div>

                    <div class="card-body">
                       
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">Product Name</th>
                                    <th class="wd-15p">Customer Name</th>
                                    <th class="wd-15p">Customer Mobile</th>
                                    <th class="wd-15p">Visited Date</th>
    
                                    
                                    
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($enquiries as $enquiry)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{$enquiry->variant_name}}</td>
                                    <td>{{$enquiry->customer_first_name}}</td>
                                    <td>{{$enquiry->customer_mobile_number}}</td>
                                    <td>
                                 
                                    {{ date('M d, Y', strtotime(@$enquiry->visited_date)) }}
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
                title: 'Customer Enquiries',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4]
                 }
            },
            {
                extend: 'excel',
                title: 'Customer Enquiries',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4]
                 }
            }
         ]
    } );

} );
} );
</script>





@endsection


