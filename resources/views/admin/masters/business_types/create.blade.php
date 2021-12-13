@extends('admin.layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
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
               <form action="{{route('admin.store_business_type')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Business Type Name</label>
                           <input type="text" class="form-control" required name="business_type_name" value="{{old('business_type_name')}}" placeholder="Business Type Name">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">

                           <label class="form-label">Icon [150*150]</label>
                           <input type="file" class="form-control" required accept="image/x-png,image/jpg,image/jpeg"
                           name="business_type_icon"  value="{{old('business_type_icon')}}" placeholder="Business Type Icon">
                        </div>
                     </div>
                  </div>


                        <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_business_type') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>

               </form>

      </div>
   </div>
</div>
@endsection
