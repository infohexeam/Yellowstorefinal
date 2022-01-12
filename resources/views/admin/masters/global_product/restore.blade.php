@extends('admin.layouts.app')
@section('content')
<style>

</style>
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12 col-lg-12">
         <div class="card">
            <div class="row" style="min-height: 72vh;">
               <div class="col-12" >

                  @if ($message = Session::get('status'))
                  <div class="alert alert-success">
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
                    {{-- <div class="card-body border">

                </div> --}}

                    <div class="card-body">
                        <a href="  {{ url('admin/global/products/list')}} " class="btn btn-block btn-success">
                           List Global Product
                        </a>
                       
                        
                        </br>
                        <div class="table-responsive">
                           <table id="example" class="table table-striped table-bordered text-nowrap w-100">
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
                                    <td>{{$value->product_brand}}</td>

                                    
                                    <td>
                                       <form action="{{route('admin.restore_global_product',$value->global_product_id)}}" method="POST">
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
            @endsection
