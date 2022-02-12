@extends('store.layouts.app')
   
@section('content')
 <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>
            <div class="card-body">
                <a class="float-right btn btn-cyan" href="{{ url()->previous() }}"><i class="fa fa-arrow-left"></i> Back</a>
                    @if ($message = Session::get('status'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                </div>
                <div class="col-lg-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
             
                    <form action="{{ route('store.update_product_variant',$product_variant->product_varient_id) }}" method="POST" enctype="multipart/form-data" >
                        @csrf
                                
                                <div class="form-body">
                                    <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Variant Name</label>
                                    <input type="text" class="form-control" name="variant_name" value="{{old('variant_name',$product_variant->variant_name)}}" placeholder="Variant Name">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">MRP</label>
                                    <input type="text" class="form-control" name="product_varient_price" value="{{old('product_varient_price',$product_variant->product_varient_price)}}" placeholder="MRP">
                                </div>
                            </div>
                                
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Sale Price</label>
                                    <input type="text" class="form-control" name="product_varient_offer_price" value="{{old('product_varient_offer_price',$product_variant->product_varient_offer_price)}}" placeholder="Sale Price">
                                </div>
                            </div>
                                
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Stock Count</label>
                                    <input type="text" class="form-control" name="stock_count" value="{{old('stock_count',$product_variant->stock_count)}}" placeholder="Stock Count">
                                </div>
                            </div>
                                
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Product Variant Base Image(1000*800 or more) </label>
                                    <input type="file" class="form-control" name="base_image" >
                                </div>
                            </div>
                                

                                <div class="col-md-12">
                                <div  class="form-group">
                                        <center>
                                    <button type="submit" class="btn btn-raised btn-primary">
                                            <i class="fa fa-check-square-o"></i> Update</button>
                                            <button type="reset" class="btn btn-raised btn-success">
                                            Reset</button>
                                            <a class="btn btn-danger" href="{{ url()->previous() }}">Cancel</a>
                                        </center>
                                </div>  
                                    
                                </div>
                            </div>
                            
                        
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

