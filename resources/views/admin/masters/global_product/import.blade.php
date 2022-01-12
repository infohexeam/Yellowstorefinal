@extends('admin.layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 72vh;"
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>
            <div class="card-body">
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
               <form action="{{route('admin.store_imported_global_products')}}" method="POST"  enctype="multipart/form-data">
                  @csrf
                  <div class="row">

                      <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Products File *</label>
                           <input type="file" accept=".xlsx" class="form-control" required name="products_file" value="{{old('products_file')}}" >
                        </div>
                     </div>
                    
                  </div>
                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-raised  btn-green"  href="{{asset('/assets/uploads/global_products.xlsx')}}"><i class="fa fa-file-excel-o"></i> Download Sample</a>
                           <a class="btn btn-danger" href="{{ route('admin.global_products') }}">Cancel</a>
                           </center>
                        </div>
               </form>

         </div>
      </div>
   </div>
</div>

 @endsection
