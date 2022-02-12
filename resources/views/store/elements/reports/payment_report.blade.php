@extends('store.layouts.app')
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
                                <form action="{{route('store.payment_reports')}}" method="GET" enctype="multipart/form-data">
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
                                                   {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                                                   <a href="{{route('store.payment_reports')}}"  class="btn btn-info">Cancel</a>
                                                </center>
                                            </div>
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

                                            <th class="wd-15p">Date</th>
                                            <th class="wd-15p">Order Number</th>
                                            <th class="wd-15p">Order Status</th>
                                          
                                          
                                            <!--  <th class="wd-15p">Customer</th>-->
                                            <!--<th class="wd-15p">Customer Phone</th>-->
                                            
                                            
                                            <th class="wd-15p">Price</th>
                                            <th class="wd-15p">Discount</th>
                                            <th class="wd-15p">Tax Amount</th>

                                            <th class="wd-15p">Payment Type</th>
                                            <th class="wd-15p">Payment Status</th>
                                            <th class="wd-15p">Transaction ID</th>
                                            
                                         </tr>
                                      </thead>
                                      <tbody>
                                          
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($data as $d)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ \Carbon\Carbon::parse($d->created_at)->format('d-m-Y')}}</td>

                                            <td>{{ $d->order_number }}</td>
                                            <td>{{ @$d->status->status }}</td>
                                            
                                            <!--<td>{{ $d->customer_first_name }} {{ $d->customer_last_name }}</td>-->
                                            <!--<td>{{ $d->customer_mobile_number }}</td>-->

                                            
                                            <td>{{ $d->product_total_amount }}</td>
                                            <td>
                                                {{ (new \App\Helpers\Helper)->orderTotalDiscount($d->order_id) }}
                                            </td>
                                            <td>
                                                {{ (new \App\Helpers\Helper)->orderTotalTax($d->order_id) }}
                                            </td>

                                            <td> 
                                                @if($d->payment_type_id == 1)
                                                    COD
                                                @else
                                                    Online
                                                @endif
                                            </td>
                                            <td>
                                                @if(($d->payment_type_id == 2) && ($d->status_id == 4 || $d->status_id > 5))
                                                Success
                                                @else
                                                --
                                                @endif
                                            </td>
                                            <td>{{ $d->trn_id }}</td>


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


<script>
    $(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Payment Report',
                footer: true,

                 orientation : 'landscape',
                pageSize : 'LEGAL',
            },
            {
                extend: 'excel',
                title: 'Payment Report',
                footer: true,
              
            }
         ]
    } );

} );
</script>

@endsection

