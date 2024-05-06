@extends('admin.layouts.app')
@section('content')
<style>

</style>
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12 col-lg-12">
         <div class="card">
            <div class="row" style="min-height: 70vh;"> 
               <div class="col-12" >

                  @if ($message = Session::get('status'))
                  <div class="alert alert-success">
                     <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                  </div>
                  @endif
                    @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                            </div>
                            @endif
                  <div class="col-lg-12">
                     @if ($errors->any())
                     <div class="alert alert-danger">
                        <h5>Whoops!</h5> There were some problems with your input.<br><br>
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
                    <form action="{{route('admin.global_products')}}" method="GET"  enctype="multipart/form-data">
                                 @csrf

                                 <div class="row">

                                      <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Product Name</label>
                       <input type="text" class="form-control" 
                       name="product_name"  value="{{ request()->input('product_name') }}" placeholder="Product Name">

                  </div>
               </div>

                                    <div class="col-md-6">
                                       <div class="form-group">
                                           <label class="form-label" >Product Category  </label>
                                           <select name="product_cat_id"  id="category" class="form-control"  >
                                                <option value="">-Select-</option>
                                                @foreach($category as $key)
                                                <option {{old('product_cat_id',request()->input('product_cat_id')) == $key->category_id ? 'selected':''}} value="{{ @$key->category_id }}">{{ @$key->category_name }}</option>
                                                @endforeach
                                             </select>
                                       </div>
                                    </div>
                                 </div>
                                 
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <center>
                                       <button type="submit" class="btn btn-raised btn-primary">
                                       <i class="fa fa-check-square-o"></i> Filter</button>
                                       {{-- <button type="reset" class="btn btn-raised btn-success">Reset</button> --}}
                                       <a href="{{route('admin.global_products')}}"  class="btn btn-info">Cancel</a>
                                       </center>
                                    </div>
                                 </div>
                           </form>

                </div>

                    <div class="card-body">
                        <a href="  {{route('admin.create_global_product')}} " class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create Global Product
                        </a>
                          <a href="  {{route('admin.import_global_product')}} " class="btn btn-block btn-green">
                           <i class="fa fa-file-excel-o"></i>
                           Import Global Products
                        </a>
                        @if(auth()->user()->user_role_id == 0)
                        <a href=" {{ url('admin/global-product/restore-list') }}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i>
                          Restore Global Products
                        </a>
                        @endif
                        
                        </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                       <th class="wd-15p">{{ __('Product') }}</th>
                                       <th class="wd-15p">{{__('Category')}}</th>
                                       <th class="wd-15p">{{__('Sale Price')}}</th>
                                       <th class="wd-15p">{{__('Image')}}</th>
                                       <th class="wd-15p">{{__('Brand')}}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($global_product as $value)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{@$value->product_name}}</td>
                                    <td>{{@$value->product_cat['category_name']}}</td>
                                    <td>{{@$value->sale_price}}</td>
                                    <td><img data-toggle="modal" data-target="#viewModal{{$value->global_product_id}}" src="{{asset('/assets/uploads/products/base_product/base_image/'.$value->product_base_image)}}"  width="50" >&nbsp;</td>
                                    <td>
                                        @if(isset($value->product_brand))
                                        {{$value->product_brand}}
                                        @else
                                        ---
                                        @endif
                                        </td>

                                    
                                    <td>
                                       <form action="{{route('admin.destroy_global_product',$value->global_product_id)}}" method="POST">
                                         @csrf
                                          @method('POST')
                                          <a class="btn btn-sm btn-cyan"  href="{{url('/admin/global/product/edit/'.
                                          Crypt::encryptString($value->global_product_id))}}">Edit</a>
                                           <a class="btn btn-sm btn-cyan"
                       href="{{url('admin/global/product/view/'.Crypt::encryptString($value->global_product_id))}}">View</a> 
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

@foreach($global_product as $product)
  <div class="modal fade" id="viewModal{{$product->global_product_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
     <div class="modal-dialog" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <div class="modal-body">

            <img  src="{{asset('/assets/uploads/products/base_product/base_image/'.$product->product_base_image)}}"  width="600" >

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
                               title: 'Global Product',
                               footer: true,
                               exportOptions: {
                                    columns: [0,1,2,3,5]
                                }
                           },
                           {
                               extend: 'excel',
                               title: 'Global Product',
                               footer: true,
                               exportOptions: {
                                    columns: [0,1,2,3,5]
                                }
                           }
                        ]
                   } );
               
               } );
                          
             </script>


            @endsection
