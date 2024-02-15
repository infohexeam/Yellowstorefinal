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
                    <div class="col-12">
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

                            <form action="{{route('store.enquiries_report')}}" method="GET" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">From Date</label>
                                            <input type="date" class="form-control" name="start_date" id="date_from" value="{{ request()->input('start_date') }}" placeholder="From Date">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">To Date</label>
                                            <input type="date" class="form-control" name="end_date" id="date_to" value="{{ request()->input('end_date') }}" placeholder="To Date">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Customer Name</label>
                                            <input type="text" class="form-control" name="customer_name" id="customer_name" value="{{ request()->input('customer_name') }}" placeholder="Customer Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Customer Mobile</label>
                                            <input type="text" class="form-control" name="customer_mobile" id="customer_mobile" value="{{ request()->input('customer_mobile') }}" placeholder="Customer Mobile">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" class="form-control" name="product_name" id="product_name" value="{{ request()->input('product_name') }}" placeholder="Product Name">
                                    </div>
                                </div>
                                </div>
                               
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <center>
                                            <button type="submit" class="btn btn-raised btn-primary">
                                                <i class="fa fa-check-square-o"></i> Filter
                                            </button>
                                            <a href="{{route('store.enquiries_report')}}" class="btn btn-info">Cancel</a>
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
                                             <th class="wd-15p">Enquiry Number</th>
                                            <th class="wd-15p">Product Name</th>
                                            <th class="wd-15p">Customer Name</th>
                                            <th class="wd-15p">Customer Mobile</th>
                                             <th class="wd-15p">Enquiry Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($enquiries as $enquiry)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>ENQ-{{$enquiry->enquiry_id}}</td>
                                            <td>{{$enquiry->variant_name}}</td>
                                            <td>{{$enquiry->customer_first_name}} {{$enquiry->customer_last_name}}</td>
                                            <td>{{$enquiry->customer_mobile_number}}</td>
                                            <td>
                                              {{ date('M d Y,h:i A', strtotime(@$enquiry->created_at)) }}
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

<script>
 ("#date_from").change(function () {
        $('#date_to').val('');
        $('#date_to').attr('min', $('#date_from').val());
    });
    $(document).ready(function () {
        $(function (e) {
            $('#exampletable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'pdf',
                        title: 'Customer Enquiries',
                        footer: true,
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Customer Enquiries',
                        footer: true,
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    }
                ]
            });
        });
    });
</script>

@endsection
