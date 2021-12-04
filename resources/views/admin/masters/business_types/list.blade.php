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

                     <div class="card-body">
                        <a href=" {{route('admin.create_business_type')}}" class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create Business Type
                        </a>
                        @if(auth()->user()->user_role_id == 0)
                        <a href=" {{ url('admin/business_type/restore-list') }}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i>

                          Restore Business Type
                        </a>
                        @endif
                        </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Icon') }}</th>
                                    <th class="wd-20p">{{__('Status')}}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($business_types as $business_type)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $business_type->business_type_name}}</td>
                                    <td><img data-toggle="modal" data-target="#viewModals{{$business_type->business_type_id}}" src="{{asset('/assets/uploads/business_type/icons/'.$business_type->business_type_icon)}}"  width="50" ></td>

                                     <td>
                                       <form action="{{route('admin.status_business_type',$business_type->business_type_id)}}" method="POST">

                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                          @if($business_type->business_type_status == 0) btn-danger @else btn-success @endif"> @if($business_type->business_type_status == 0)
                                          InActive
                                          @else
                                          Active
                                          @endif</button>
                                       </form>
                                    </td>
                                    <td>
                                       <form action="{{route('admin.destroy_business_type',$business_type->business_type_id)}}" method="POST">
                                         <a class="btn btn-sm btn-cyan"
                                             href="{{url('admin/business_type/edit/'.
                                          $business_type->business_type_name_slug)}}">Edit</a>

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


              @foreach($business_types as $business_type)
  <div class="modal fade" id="viewModals{{$business_type->business_type_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
     <div class="modal-dialog" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <div class="modal-body">

            <img  src="{{asset('/assets/uploads/business_type/icons/'.$business_type->business_type_icon)}}"  width="600" >

           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
           </div>
        </div>
     </div>
  </div>
  @endforeach

                         <script>

$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Business Types',
                footer: true,
                exportOptions: {
                     columns: [0,1,3]
                 }
            },
            {
                extend: 'excel',
                title: 'Business Types',
                footer: true,
                exportOptions: {
                     columns: [0,1,3]
                 }
            }
         ]
    } );

} );
            </script>

            <!-- MESSAGE MODAL CLOSED -->
            @endsection
