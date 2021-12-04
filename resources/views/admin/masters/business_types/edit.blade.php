@extends('admin.layouts.app')
@section('content')
<div class="container">
  <div class="row">
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
          <form action="{{ route('admin.update_business_type',$business_type->business_type_id) }}" method="POST" enctype="multipart/form-data" >
            @csrf
            <div class="form-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Business Type Name</label>
                    <input type="hidden" class="form-control" name="business_type_id" value="{{$business_type->business_type_id}}" >

                    <input type="text" class="form-control" name="business_type_name" value="{{old('business_type_name',$business_type->business_type_name)}}" placeholder="Business Type Name">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                  <label class="form-label">Icon [150*150]</label>
                    <input type="file"  class="form-control" accept="image/x-png,image/jpg,image/jpeg"
                    name="business_type_icon" value="{{old('business_type_icon',$business_type->business_type_icon)}}" placeholder="Business Type Image">
                    <img src="{{asset('/assets/uploads/business_type/icons/'.$business_type->business_type_icon)}}"  width="100" style="height:60px" "width :50px">
                  </div>
                </div>


                  <div class="col-md-12">
                    <div  class="form-group">
                      <center>
                      <button type="submit" class="btn btn-raised btn-primary">
                      <i class="fa fa-check-square-o"></i> Update</button>
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
