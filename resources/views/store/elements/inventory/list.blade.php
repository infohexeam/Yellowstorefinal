@extends('store.layouts.app')
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
      <div class="card">
        <div class="row">
          <div class="col-12">


            @if ($message = Session::get('status'))
            <div class="alert alert-success">
              <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
            </div>
            @endif
            <div class="col-lg-12">
              @if ($errors->any())
              <div class="alert alert-danger">
                <strong>Whoops!</strong> 
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
                      <form action="{{route('store.list_inventory')}}" method="GET"  enctype="multipart/form-data">
                         @csrf

                         <div class="row">
                            <div class="col-md-6">
                               <div class="form-group">
                                   <label class="form-label" >Product Category  </label>
                                   <select name="product_cat_id"  id="category" class="form-control"  >
                                    <option value="">--Select--</option>
                                    @foreach($category as $key)
                                        <option {{old('product_cat_id',request()->input('product_cat_id')) == $key->category_id ? 'selected':''}} value="{{ @$key->category_id }}">{{ @$key->category_name }}</option>
                                        @endforeach
                                     </select>
                               </div>
                            </div>
                            
                             <div class="col-md-6">
                               <div class="form-group">
                                   <label class="form-label" >Product Name  </label>
                                   <input type="text" class="form-control" placeholder="Product Name"  value="{{old('product_name',request()->input('product_name'))}}" name="product_name" />
                               </div>
                            </div>
                            
                         </div>
                         
                         <div class="col-md-12">
                            <div class="form-group">
                               <center>
                               <button type="submit" class="btn btn-raised btn-primary">
                               <i class="fa fa-check-square-o"></i> Filter</button>
                               {{-- <button type="reset" class="btn btn-raised btn-success">Reset</button> --}}
                               <a href="{{route('store.list_inventory')}}"  class="btn btn-info">Cancel</a>
                               </center>
                            </div>
                         </div>
                   </form>
                </div>
         
               <div class="card-body">
              
               <div class="alert alert-danger alert-dismissible fade in" id="testdiv">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
   
  </div>
                       
                <div class="table-responsive">
                  <table  class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                      <tr>
                        <th class="wd-15p">S.No</th>
                        <th class="wd-15p">Product<br>Name</th>
                        <th class="wd-15p">Product<br>Category</th>
                        <th class="wd-15p">Current<br>Stock</th>

                       <th class="wd-15p">{{__('Action')}}</th>

                      </tr>
                    </thead>
                    <tbody>
                      @php
                      // $i = 0;
                      // if($_GET)
                      // $i = (request()->input('page') - 1) * 10;
                      $i = ($products->perPage() * ($products->currentPage() - 1)) + 1;
                      @endphp
                      @foreach ($products as $product)
                      <tr>
                        <td>{{ $i++ }}</td>
                        
                        <td>
                                                      {!! wordwrap($product->variant_name, 20, "<br />\n") !!}

                            
                      
                </td>
                        <td>
                          @php
                              $cat  = \DB::table('mst_store_categories')->where('category_id',$product->product_cat_id)->first();
                          @endphp
                          {{@$cat->category_name}}</td>
                        <td id="td{{$product->product_varient_id}}">
                         
                          @if($product->stock_count == 0 )
                      
                            Empty
                            
                            @else
                           
                            {{$product->stock_count}}

                          @endif

                        
                        </td>
                        <td>
                          <form>
                            <input style="display:inline-block; width:70%;" type="number" id="stock_id{{$product->product_varient_id}}" class="form-control"   placeholder="New Stock ">

                            <a onclick="updateStock({{$product->product_varient_id}})" class="btn btn-icon btn-green"><i style="color:#ffffff;" class="fa fa-check" ></i></a>
                            <a onclick="resetStock({{$product->product_varient_id}})" class="btn btn-icon btn-red"><i style="color:#ffffff;" class="fa fa-rotate-left"></i></a>
                         </form>
                           <span id="status_msg{{$product->product_varient_id}}"></span>
                        </td>



                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  
                      <div class="float-right"> {!! $products->appends(['product_cat_id' => $product_cat_id,'product_name' => $product_name])->links() !!} </div>
                    
                      @if(count($products) == 0)
                      <p style="text-align: center;" >No data found...</p>
                    @endif
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

  <script>

  function resetStock(product_varient_id)
  {
    $('#stock_id'+product_varient_id).val('');
    var _token = $('input[name="_token"]').val();
    $.ajax({
        url:"{{ route('store.stock_reset') }}",
        method:"POST",
        data:{product_varient_id:product_varient_id, _token:_token},
        success:function(result)
        {
          //alert(result);
          if(result == 0)
          { 
            $('#td'+product_varient_id).html('Empty');
          $("#stock_id"+product_varient_id).val('');
               var $el = $("#td"+product_varient_id),
                    x = 400,
                    originalColor = $el.css("background-color");

                $el.css("background", "#4871cc9c");
                setTimeout(function(){
                  $el.css("background-color", originalColor);
                }, x);
          }
        }
    });
  }



  function updateStock(product_varient_id)
  {
        var updated_stock = $('#stock_id'+product_varient_id).val();
        var _token = $('input[name="_token"]').val();
          var current_stock =    $('#td'+product_varient_id).text();
          if(updated_stock.toString().length>10)
          {
            alert('stock count should not exceed 10 digits');
             $('#stock_id'+product_varient_id).val('');
            return false;
          }

    $.ajax({
        url:"{{ route('store.stock_update') }}",
        method:"POST",
        data:{updated_stock:updated_stock,product_varient_id:product_varient_id, _token:_token},
        success:function(result)
        {
            if(result != "error"){
               // $('#status_msg'+product_varient_id).html('<label class="text-success">Stock Updated</label>');
                $("#status_msg"+product_varient_id).show().delay(1000).fadeOut();
                $("#stock_id"+product_varient_id).val('');

                if(result == 0){
                  $('#td'+product_varient_id).html('Empty');
                }else if(result>0){
                  $('#td'+product_varient_id).html(result);
                }
                else if(result=='incapable_stock')
                {
                  
                  alert('Negative stock is not allowed');
                  
                }


              if(result > current_stock)
              {
                var $el = $("#td"+product_varient_id),
                    x = 400,
                    originalColor = $el.css("background-color");

               // $el.css("background", "#49e3428a");
                $el.css("background", "#4871cc9c");
                setTimeout(function(){
                  $el.css("background-color", originalColor);
                }, x);
              }
              else
              {
                var $el = $("#td"+product_varient_id),
                    x = 400,
                    originalColor = $el.css("background-color");

               // $el.css("background", "#d3202094");
                $el.css("background", "#4871cc9c");
                setTimeout(function(){
                  $el.css("background-color", originalColor);
                }, x);
              }

            }
            else
            {
                $("#stock_id"+product_varient_id).val('');
               var $el = $("#td"+product_varient_id),
                    x = 400,
                    originalColor = $el.css("background-color");

                $el.css("background", "#4871cc9c");
                setTimeout(function(){
                  $el.css("background-color", originalColor);
                }, x);
            }
        }
    });
}
  </script>

  @endsection
