@extends('store.layouts.app')
@section('content')
<style>

</style>
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
                              <form action="{{route('store.global_products')}}" method="GET"  enctype="multipart/form-data">
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
                                       <a href="{{route('store.global_products')}}"  class="btn btn-info">Cancel</a>
                                       </center>
                                    </div>
                                 </div>
                           </form>
                        </div>

                    <div class="card-body">
                        <form action="{{route('store.global_to_store_products')}}" name="ff" method="POST" enctype="multipart/form-data">
                        @csrf
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
                                    <th class="wd-15p"></th>
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

                                        <a class="btn btn-sm btn-cyan" href="{{url('store/global/product/view/'.Crypt::encryptString($value->global_product_id))}}">View</a>
                                          <a href="{{route('store.global_product_add_to_store',$value->global_product_id)}}" onclick="return confirm('Do you want to add this item to store products?');"  class="btn btn-sm btn-secondary">Add To Store</a>
                                    </td>
                                    <td>
                                       <input type="checkbox" class="gProClass" name="global_product_idz[]" value="{{$value->global_product_id}}">
                                    </td>

                                 </tr>
                                 @endforeach


                              </tbody>
                           </table>

                        </div>

                        <label class="custom-control custom-checkbox">
                           <input type="checkbox" onclick="checkAllBoxes(this.id)" id="checkAll"  class="custom-control-input" name="example-checkbox4" >
                           <span class="custom-control-label">Select All</span>
                        </label>
                        <button type="submit" value="" class="btn mt-4 btn-block btn-secondary">Add To Store</button>

       </form>

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


<script>
   function checkAllBoxes(id){
     // alert(id);
      if ($('#'+id).is(':checked')) {
         $('.gProClass').prop('checked', true);
      }else{
         $('.gProClass').prop('checked', false);
      }
   }
</script>
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
