@extends('admin.layouts.app')
@section('content')
<link href="{{URL::to('/assets/plugins/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet"/>

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
       <form action="{{route('admin.list_category')}}" method="GET"
                enctype="multipart/form-data">
          @csrf
            <div class="row">
               <div class="col-md-12">
                  <div class="form-group">
                     <label class="form-label">Business Type</label>
                      <select class="form-control" name="business_type_id">
                        <option value=""> Select Business Type </option>

                                 @foreach ($business_types as $key)

                                 <option {{request()->input('business_type_id') == $key->business_type_id ? 'selected':''}} value=" {{ $key->business_type_id}} "> {{ $key->business_type_name }}
                                 </option>
                                 @endforeach

                           </select>
                  </div>
               </div>
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary" style="border: none;">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('admin.list_category')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
    </div>
       </form>
    </div>
                     <div class="card-body">
                        <a href=" {{route('admin.create_category')}}" class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create Product Category
                        </a>
                        @if(auth()->user()->user_role_id == 0)

                         <a href=" {{ url('admin/category/restore-list') }}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i>
                          Restore Product Category
                        </a>
                        @endif

                        </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Category Type') }}</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>
                                    <th class="wd-20p">{{__('Business Type')}}</th>
                                    <th class="wd-20p">{{__('Status')}}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($categories as $category)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $category->category_name}}</td>
                                    <td>
                                       @if($category->category_icon == '')
                                       <img src="{{asset('/assets/uploads/avatar.jpg')}}"  width="50" >

                                       @else
                                       <img src="{{asset('/assets/uploads/category/icons/'.$category->category_icon)}}"  width="50" >
                                    @endif
                                 </td>
                                    <td>{{ $category->business_type['business_type_name'] }}  </td>
                                     <td>
                                       <form action="{{route('admin.status_category',$category->category_id)}}" method="POST">

                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                          @if($category->category_status == 0) btn-danger @else btn-success @endif"> @if($category->category_status == 0)
                                          InActive
                                          @else
                                          Active
                                          @endif</button>
                                       </form>
                                    </td>
                                    <td>
                                       <form action="{{route('admin.destroy_category',$category->category_id)}}" method="POST">
                                         <a class="btn btn-sm btn-cyan"
                                             href="{{url('admin/categories/edit/'.
                                          $category->category_name_slug)}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
                                          <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{$category->category_id}}" ><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
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
            @foreach($categories as $category)
            <div class="modal fade" id="viewModal{{$category->category_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Product Category -  {{ $category->category_name   }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">

                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-12 p-0">
                                 <tr>
                                    <input type="hidden" class="form-control" name="category_id" value="{{$category->category_id}}" >
                                 </tr>
                                 <tr>
                                    <td><h6>Category Image: </td><td>  <img src="{{asset('/assets/uploads/category/icons/'.$category->category_icon)}}"  width="100" style="height:60px" "width :50px"></h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Category Type: </td><td> {{ $category->category_name }}</h6></td>
                                 </tr>
                                <tr>
                                    <td><h6>Business Type: </td><td> {{ $category->business_type['business_type_name'] }}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Description: </td><td> {!!  $category->category_description !!}</h6></td>
                                 </tr>
                              </tbody>
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
            <script>

               $(function(e) {
                   $('#exampletable').DataTable( {
                       dom: 'Bfrtip',
                       buttons: [
                           {
                               extend: 'pdf',
                               title: 'Product Categories',
                               footer: true,
                               exportOptions: {
                                    columns: [0,1,3,4]
                                },
                                 customize: function(doc) {
                                       doc.content[1].margin = [ 100, 0, 100, 0 ]; //left, top, right, bottom
                                 }
                           },
                           {
                               extend: 'excel',
                               title: 'Product Categories',
                               footer: true,
                               exportOptions: {
                                    columns: [0,1,3,4]
                                }
                           }
                        ]
                   } );
               
               } );
                           </script>
            @endsection
