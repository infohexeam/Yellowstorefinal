@extends('admin.layouts.app')
@section('content')
@php
use App\Models\admin\Trn_first_order_point;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Trn_registration_point;
use App\Models\admin\Trn_referal_point;
use App\Models\admin\Trn_points_to_rupee;
use App\Models\admin\Trn_points_redeemed;



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
                    <div class="card-body">
                    @if (Trn_configure_points::count() < 1)
                         <a href="  {{route('admin.create_configure_points')}} " class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create Configure Point
                        </a>
                    @endif
                        </br>
                        <div class="table-responsive">
                           <table  class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Points') }}</th>
                                    <th class="wd-15p">{{ __('Order Amount') }}</th>
                                    <th class="wd-15p">{{ __('Valid From') }}</th>
                                    <th class="wd-15p">{{ __('Status') }}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($configure_points as $configure_point)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $configure_point->points}}</td>
                                    <td>{{$configure_point->order_amount}} </td>
                                    <td>{{ $configure_point->valid_from}} </td>

                                 <td>
                                       <form action="{{route('admin.status_configure_points',$configure_point->configure_points_id)}}" method="POST">

                                            @if($configure_point->isActive == 0)
                                            <input type="hidden" name="isActive" value=1>
                                            @else
                                                <input type="hidden" name="isActive" value=0>

                                            @endif
                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                          @if($configure_point->isActive == 0) btn-danger @else btn-success @endif"> @if($configure_point->isActive == 0)
                                          InActive
                                          @else
                                          Active
                                          @endif</button>
                                       </form>
                                    </td>
                                   <td>
                                   <form action="{{route('admin.destroy_configure_point',$configure_point->configure_points_id)}}" method="POST">
                                        <a class="btn btn-sm btn-cyan"
                                             href="{{url('admin/configure_point/edit/'.
                                          $configure_point->configure_points_id)}}">Edit</a>

                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                       </form>
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





    {{-- Maximum Points redeemed --}}

  <div class="card">
                <div class="row">
                    <div class="col-12" >

                        <div class="card-body">
                                @if (Trn_points_redeemed::count() < 1)
                                    <a style="color:white;" data-toggle="modal" data-target="#StockModal08" class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i>
                                        Add Points Redeemed Per Order
                                    </a>
                                @else
                                  <div class="card-header">
                                    <h3 class="mb-0 card-title">  Points Redeemed Per Order</h3>
                                </div>
                                @endif
                            <div class="table-responsive">
                            <table  class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">S.No</th>
                                        <th class="wd-15p">{{__('Point in Pericentage')}}</th>
                                        {{-- <th class="wd-15p">{{__('Rupee')}}</th> --}}
                                        {{-- <th class="wd-15p">{{__('Status')}}</th> --}}
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                   @foreach ($points_redeemed as $data)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $data->point_in_percentage}}%</td>
                                            <td>
                                                <form action="{{route('admin.destroy_points_redeemed',$data->points_redeemed_id)}}" method="POST">
                                                   @csrf
                                                    @method('POST')
                                                    <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                           </tr>
                                        @endforeach
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




            {{-- Points to Rupee --}}

  <div class="card">
                <div class="row">
                    <div class="col-12" >

                        <div class="card-body">
                                @if (Trn_points_to_rupee::count() < 1)
                                    <a style="color:white;" data-toggle="modal" data-target="#StockModal05" class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i>
                                        Add Points To Rupee
                                    </a>
                                @else
                                  <div class="card-header">
                                    <h3 class="mb-0 card-title"> Points To Rupee Conversion</h3>
                                </div>
                                @endif
                            <div class="table-responsive">
                            <table  class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">S.No</th>
                                        <th class="wd-15p">{{__('Point')}}</th>
                                        <th class="wd-15p">{{__('Rupee')}}</th>
                                        {{-- <th class="wd-15p">{{__('Status')}}</th> --}}
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                   @foreach ($points_to_rupee as $data)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $data->point}}</td>
                                            <td>{{ $data->rupee}}</td>
                                            <td>
                                                <form action="{{route('admin.destroy_point_to_rupee',$data->points_to_rupees_id)}}" method="POST">
                                                   @csrf
                                                    @method('POST')
                                                    <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                           </tr>
                                        @endforeach
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







{{-- //                                    Add Registration points --}}


      <div class="card">
                <div class="row">
                    <div class="col-12" >

                        <div class="card-body">
                                @if (Trn_registration_point::count() < 1)
                                    <a style="color:white;" data-toggle="modal" data-target="#StockModal02" class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i>
                                    Add Registration points
                                    </a>
                                @else
                                  <div class="card-header">
                                    <h3 class="mb-0 card-title">Registration points</h3>
                                </div>
                                @endif
                            <div class="table-responsive">
                            <table  class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">S.No</th>
                                        <th class="wd-15p">{{__('Registration Point')}}</th>
                                        <th class="wd-15p">{{__('Valid From')}}</th>
                                        <th class="wd-15p">{{__('Status')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                   @foreach ($registration_points as $data)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $data->registration_point}}</td>
                                            <td>{{ $data->valid_from}}</td>
                                            <td>
                                                <form action="{{route('admin.status_registration_points',$data->registration_points_id)}}" method="POST">

                                                    @if($data->isActive == 0)
                                                    <input type="hidden" name="isActive" value=1>
                                                    @else
                                                        <input type="hidden" name="isActive" value=0>

                                                    @endif
                                                @csrf
                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                                @if($data->isActive == 0) btn-danger @else btn-success @endif"> @if($data->isActive == 0)
                                                InActive
                                                @else
                                                Active
                                                @endif</button>
                                            </form>
                                            </td>
                                            <td>
                                                <form action="{{route('admin.destroy_registration_point',$data->registration_points_id)}}" method="POST">
                                                    {{-- <a class="btn btn-sm btn-cyan"
                                                        href="{{url('admin/data/edit/'.
                                                    $data->datas_id)}}">Edit</a> --}}

                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                           </tr>
                                        @endforeach
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


                                    {{-- Add First Order points --}}

          <div class="card">
                <div class="row">
                    <div class="col-12" >

                        <div class="card-body">

                                    @if (Trn_first_order_point::count() < 1)
                                    <a  style="color:white;" data-toggle="modal" data-target="#StockModal03" class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i>
                                    Add First Order points
                                    </a>
                                @else
                                  <div class="card-header">
                                    <h3 class="mb-0 card-title">First Order points</h3>
                                </div>
                                @endif
                            <div class="table-responsive">
                            <table  class="table table-striped table-bdataed text-nowrap w-100">
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

                        <div class="card-body">

                                 @if (Trn_referal_point::count() < 1)
                                    <a style="color:white;" data-toggle="modal" data-target="#StockModal04" class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i>
                                    Add Referal points
                                    </a>
                                @else
                                   <div class="card-header">
                                  <h3 class="mb-0 card-title">Referal Points</h3>
                                </div>
                                @endif

                            <div class="table-responsive">
                               <table  class="table table-striped table-bdataed text-nowrap w-100">
                  <thead>
                     <tr>
                        <th class="wd-15p">S.No</th>
                        <th class="wd-15p">{{__('Referal Point')}}</th>
                        <th class="wd-15p">{{__('Valid From')}}</th>
                        <th class="wd-15p">{{__('Status')}}</th>
                        <th class="wd-15p">{{__('Action')}}</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $i = 0;
                     @endphp
                     @foreach ($referal_points as $data)
                     <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $data->point}}</td>
                        <td>{{ $data->valid_from}}</td>
                            <td>
                                                                <form action="{{route('admin.status_referal_points',$data->referal_points_id)}}" method="POST">

                            @if($data->isActive == 0)
                            <input type="hidden" name="isActive" value=1>
                            @else
                                <input type="hidden" name="isActive" value=0>

                            @endif
                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                          @if($data->isActive == 0) btn-danger @else btn-success @endif"> @if($data->isActive == 0)
                                          InActive
                                          @else
                                          Active
                                          @endif</button>
                                       </form>
                                    </td>
                                   <td>
                                   <form action="{{route('admin.destroy_status_referal_points',$data->referal_points_id)}}" method="POST">

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
            <!-- MESSAGE MODAL CLOSED -->




{{-- points redeems StockModal08 --}}
 <div class="modal fade" id="StockModal08" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3"> Points Redeemd Per Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_points_redeemed') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                    <label class="form-label"> Point redeemed in single order(%)</label>
                    <input type="number" class="form-control" name="point_in_percentage" >


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







{{-- Points to Rupee Modal --}}
 <div class="modal fade" id="StockModal05" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3"> Points To Rupee</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_points_to_rupee') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                    <label class="form-label"> Point</label>
                    <input type="number" class="form-control" name="point" >

                     <label class="form-label">Rupee</label>
                    <input type="number" class="form-control" name="rupee" >

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



{{-- referal points --}}
 <div class="modal fade" id="StockModal04" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Referal points</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_referal_points') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                    <label class="form-label"> Point</label>
                    <input type="number" class="form-control" name="point" >

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


{{-- first order points modal --}}
            <div class="modal fade" id="StockModal03" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">First Order Points</h5>
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




{{-- //registraion --}}

 <div class="modal fade" id="StockModal02" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Registration Point</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_registration_points') }} " method="POST" enctype="multipart/form-data" >
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


@endsection
