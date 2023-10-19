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
                        
                            <div class="table-responsive">
                            <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('User type')}}</th>
                                        <th class="wd-15p">{{__('IP Address')}}</th>
                                        <th class="wd-15p">{{__('Store Name')}}</th>
                                        <th class="wd-15p">{{__('Logged in At')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($user_logs as $user_log)
                                    
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ ucwords($user_log->user_type)}}</td>
                                        <td>{{ $user_log->user_ip_address }}</td>
                                        <td>@if($user_log->store_id==NULL)--- 
                                        @else 
                                        @php
                                        $store= \DB::table('mst_stores')->where('store_id',$user_log->store_id)->first();
                                        @endphp
                                        @if($store)
                                        {{@$store->store_name}}
                                        @else
                                         ---
                                        @endif
                                        @endif</td>
                                        <td>{{ $user_log->created_at }}</td>
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





<script>

    $(function(e) {
        $('#exampletable').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'pdf',
                    title: 'Vehicle_types',
                    footer: true,
                    exportOptions: {
                         columns: [0,1]
                     }
                     
                },
                {
                    extend: 'excel',
                    title: 'Vehicle_types',
                    footer: true,
                    exportOptions: {
                         columns: [0,1]
                     }
                }
             ]
        } );
    
    } );
                </script>


<script>
function clearText()
{


      $('#vehicle_type_name').val('');

}
</script>


@endsection
