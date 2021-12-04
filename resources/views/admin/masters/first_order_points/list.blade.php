@extends('admin.layouts.app')
@section('content')
@php
use App\Models\admin\Trn_first_order_point;
@endphp
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
                        <div class="card-body">
                                @if (Trn_first_order_point::count() < 1)
                                    <a  data-toggle="modal" data-target="#StockModal" class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i> Add First Order points </a>
                                @else
                                    <h3 class="mb-0 card-title">First Order points</h3>
                                @endif
                            <div class="table-responsive">
                            <table id="example" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('First Order Point')}}</th>
                                        <th class="wd-15p">{{__('Valid From')}}</th>
                                        <th class="wd-15p">{{__('Status')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($first_order_points as $data)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $data->registration_point}}</td>
                                        <td>{{ $data->valid_from}}</td>
                                        <td>
                                            <form action="{{route('admin.status_first_order_points',$data->first_order_points_id)}}" method="POST">@if($data->isActive == 0)<input type="hidden" name="isActive" value=1>@else<input type="hidden" name="isActive" value=0>@endif
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                                        @if($data->isActive == 0) btn-danger @else btn-success @endif"> @if($data->isActive == 0) InActive @else Active @endif</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{route('admin.destroy_first_order_point',$data->first_order_points_id)}}" method="POST">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach -
                                </tbody>
                            </table>
                            {{-- table responsive end --}}
                            </div>
                        {{-- Card body end --}}
                        </div>
                    {{-- col 12 end --}}
                </div>
            {{-- row end --}}
            </div>
        {{-- card --}}


        <div class="card">
                <div class="row">
                    <div class="col-12" >
                            @if ($message = Session::get('status'))
                            <div class="alert alert-success">
                                <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                            </div>
                            @endif
                        <div class="card-body">
                                @if (Trn_first_order_point::count() < 1)
                                    <a  data-toggle="modal" data-target="#StockModal" class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i> Add First Order points </a>
                                @else
                                    <h3 class="mb-0 card-title">First Order points</h3>
                                @endif
                            <div class="table-responsive">
                            <table id="example" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">S.No</th>
                                        <th class="wd-15p">{{__('First Order Point')}}</th>
                                        <th class="wd-15p">{{__('Valid From')}}</th>
                                        <th class="wd-15p">{{__('Status')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($first_order_points as $data)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $data->registration_point}}</td>
                                        <td>{{ $data->valid_from}}</td>
                                        <td>
                                            <form action="{{route('admin.status_first_order_points',$data->first_order_points_id)}}" method="POST">@if($data->isActive == 0)<input type="hidden" name="isActive" value=1>@else<input type="hidden" name="isActive" value=0>@endif
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                                        @if($data->isActive == 0) btn-danger @else btn-success @endif"> @if($data->isActive == 0) InActive @else Active @endif</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{route('admin.destroy_first_order_point',$data->first_order_points_id)}}" method="POST">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach -
                                </tbody>
                            </table>
                            {{-- table responsive end --}}
                            </div>
                        {{-- Card body end --}}
                        </div>
                    {{-- col 12 end --}}
                </div>
            {{-- row end --}}
            </div>
        {{-- card --}}





        <div class="card">
                <div class="row">
                    <div class="col-12" >
                            @if ($message = Session::get('status'))
                            <div class="alert alert-success">
                                <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                            </div>
                            @endif
                        <div class="card-body">
                                @if (Trn_first_order_point::count() < 1)
                                    <a  data-toggle="modal" data-target="#StockModal" class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i> Add First Order points </a>
                                @else
                                    <h3 class="mb-0 card-title">First Order points</h3>
                                @endif
                            <div class="table-responsive">
                            <table id="example" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">S.No</th>
                                        <th class="wd-15p">{{__('First Order Point')}}</th>
                                        <th class="wd-15p">{{__('Valid From')}}</th>
                                        <th class="wd-15p">{{__('Status')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($first_order_points as $data)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $data->registration_point}}</td>
                                        <td>{{ $data->valid_from}}</td>
                                        <td>
                                            <form action="{{route('admin.status_first_order_points',$data->first_order_points_id)}}" method="POST">@if($data->isActive == 0)<input type="hidden" name="isActive" value=1>@else<input type="hidden" name="isActive" value=0>@endif
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                                        @if($data->isActive == 0) btn-danger @else btn-success @endif"> @if($data->isActive == 0) InActive @else Active @endif</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{route('admin.destroy_first_order_point',$data->first_order_points_id)}}" method="POST">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach -
                                </tbody>
                            </table>
                            {{-- table responsive end --}}
                            </div>
                        {{-- Card body end --}}
                        </div>
                    {{-- col 12 end --}}
                </div>
            {{-- row end --}}
            </div>
        {{-- card --}}






        </div>
        {{-- row justify end --}}
    </div>
{{-- container end --}}
</div>





















            <div class="modal fade" id="StockModal" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_first_order_points') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                    <label class="form-label">New Customer Registration Point</label>
                    <input type="number" class="form-control" name="registration_point" >

                     <label class="form-label">Valid From</label>
                    <input type="date" class="form-control" name="valid_from" >

                <br>
                       	<label class="custom-switch">
                                                        <input type="hidden" name="isActive" value=0 />
														<input type="checkbox" name="isActive"  checked value=1 class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description">Active Status</span>
													</label>


                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
<!-- MESSAGE MODAL CLOSED -->
@endsection
