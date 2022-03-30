@extends('admin.layouts.app')
@section('content')
<link href="{{URL::to('/assets/plugins/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet"/>

<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12 col-lg-12">
         <div class="card">
            <div class="row" style="min-height:70vh;">
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
                      
                        
                         <a href=" {{ url('/admin/sub/category/list') }}" class=" text-white btn btn-block btn-success">
                            Sub Category List
                         </a>
                           
                        </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">Parent<br>Category</th>
                                    <th class="wd-15p">Sub Category<br>Type</th>
                                    <th class="wd-20p">Business<br>Type</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($sub_category as $category)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ @$category->categories->category_name}}</td>
                                    <td>{{ $category->sub_category_name}}</td>
                                     <td>{{ $category->business_type['business_type_name'] }}  </td>
                                    <td>
                                       @if($category->sub_category_icon == '')
                                       <img src="{{asset('/assets/uploads/avatar.jpg')}}"  width="50" >

                                       @else
                                       <img src="{{asset('/assets/uploads/category/icons/'.$category->sub_category_icon)}}"  width="50" >
                                    @endif
                                 </td>
                                  
                                    <td>
                                        
                                         <form action="{{route('admin.restore_sub_category',$category->sub_category_id)}}" method="POST">
                                          <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{$category->sub_category_id}}" > View</button>
                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to restore this item?');"  class="btn btn-sm btn-warning">Restore</button>
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
            @foreach($sub_category as $category)
            <div class="modal fade" id="viewModal{{$category->sub_category_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
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
                                    <td><h6>Sub Category Icon: </td><td>  <img src="{{asset('/assets/uploads/category/icons/'.$category->sub_category_icon)}}"  width="100" style="height:60px" "width :50px"></h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Sub Category Type: </td><td> {{ $category->sub_category_name }}</h6></td>
                                 </tr>
                                <tr>
                                    <td><h6>Business Type: </td><td> {{ $category->business_type['business_type_name'] }}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Description: </td><td> {!!  $category->sub_category_description !!}</h6></td>
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
            
            
            <script> $(function(e) {
               $('#exampletable').DataTable( {
                   dom: 'Bfrtip',
                   buttons: [
                       {
                           extend: 'pdf',
                           title: 'Sub Categories Restore List',
                           // orientation:'landscape',
                           footer: true,
                           exportOptions: {
                                columns: [0,1,2,3,4],
                                alignment: 'right',
                            },
                             customize: function(doc) {
                                 doc.content[1].margin = [ 100, 0, 100, 0 ]; //left, top, right, bottom
                          doc.content.forEach(function(item) {
                          if (item.table) {
                             item.table.widths = ['auto', 'auto','auto','auto','auto']
                           }
                          })
                        }
                       },
                       {
                           extend: 'excel',
                           title: 'Sub Categories Restore List',
                           footer: true,
                           exportOptions: {
                                columns: [0,1,2,3,4]
                            }
                       }
                    ]
               } );
           
           } );
           </script>
           
            @endsection
