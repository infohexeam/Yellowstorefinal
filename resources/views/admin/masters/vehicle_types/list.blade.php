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
                        <div class="card-body">
                                    <a  data-toggle="modal" data-target="#StockModal01" class="btn btn-block btn-info text-white">
                                    <i class="fa fa-plus"></i> Add Vehicle Type </a>
                                   @if(auth()->user()->user_role_id == 0)
                                     <a href=" {{ url('admin/vihicle-types/restore-list') }}" class=" text-white btn btn-block btn-danger">
                                   <i class="fa fa-recycle"></i> Restore Vehicle Type </a> @endif
                                <br>
                            <div class="table-responsive">
                            <table id="example" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Vehicle Type')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($vehicle_types as $vehicle_type)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $vehicle_type->vehicle_type_name}}</td>

                                        <td>
                                            <form action="{{route('admin.destroy_vehicle_type',$vehicle_type->vehicle_type_id)}}" method="POST">
                                                @csrf
                                                    <a class="btn btn-sm btn-cyan"  data-toggle="modal" data-target="#StockModal{{$vehicle_type->vehicle_type_id}}"
                                            >Edit</a>
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



        </div>
        {{-- row justify end --}}
    </div>
{{-- container end --}}
</div>


      <div class="modal fade" id="StockModal01" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Add Vehicle Type</h5>
                        <button type="button" class="close" data-dismiss="modal" onclick="clearText()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_vehicle_type') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">
                     <label class="form-label">Vehicle Type</label>
                    <input type="text" required class="form-control" placeholder="Vehicle Type" name="vehicle_type_name"  id="vehicle_type_name" >

  	{{-- <label class="custom-switch">
                                                        <input type="hidden" name="isActive" value=0 />
														<input type="checkbox" name="isActive"  checked value=1 class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description">Active Status</span>
													</label> --}}
                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Add</button>
                        <button type="button" class="btn btn-secondary" onclick="clearText()" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>



@foreach ($vehicle_types as $vehicle_type)

              <div class="modal fade" id="StockModal{{$vehicle_type->vehicle_type_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Add Vehicle Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.update_vehicle_type',$vehicle_type->vehicle_type_id) }}" method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">
                     <label class="form-label">Vehicle Type</label>
                    <input type="text" required class="form-control" value="{{$vehicle_type->vehicle_type_name}}" placeholder="Vehicle Type" name="vehicle_type_name" >


                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
<!-- MESSAGE MODAL CLOSED -->
                                    @endforeach



<script>
function clearText()
{


      $('#vehicle_type_name').val('');

}
</script>


@endsection
