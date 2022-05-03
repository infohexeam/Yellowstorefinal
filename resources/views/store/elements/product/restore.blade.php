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
             @if ($message = Session::get('err_status'))
            <div class="alert alert-danger">
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
                  
                   
                </div>
             
               <div class="card-body">
                         <a href="{{route('store.list_product')}}" class="btn btn-block btn-success">
                           <i class="fa fa-plus"></i>
                           List Products
                        </a> <br/>
                        
                      
                        
                <div class="table-responsive">
                  <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                      <tr>
                        <th class="wd-15p">SL.No</th>
                        <th class="wd-15p">{{ __('Product') }}</th>
                        <th class="wd-15p">{{ __('Code') }}</th>
                        <th class="wd-15p">{{ __('Price') }}</th>
                        <th class="wd-15p">{{__('Image')}}</th>
                       <th class="wd-15p">{{__('Action')}}</th>
                       
                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $i = 0;
                      @endphp
                      @foreach ($products as $product)
                      <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{$product->product_name}}</td>
                        <td>{{$product->product_code}}</td> 
                        <td>{{$product->product_price_offer}}</td>
                        <td><img data-toggle="modal" data-target="#viewModal{{$product->product_id}}"  src="{{asset('/assets/uploads/products/base_product/base_image/'.$product->product_base_image)}}"  width="50" >&nbsp;</td>
                       
                      
                     
                   <td>

                       <a class="btn btn-sm btn-warning"
                       href="{{url('store/restore-product/'.$product->product_id)}}">Restore</a> 
                       
                     
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
    </div>
  </div>

  @foreach($products as $product)
  <div class="modal fade" id="viewModal{{$product->product_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
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



            
  @endsection
 