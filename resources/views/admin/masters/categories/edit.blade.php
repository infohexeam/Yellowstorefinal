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
          <form action="{{ route('admin.update_category',$category->category_id) }}" method="POST" enctype="multipart/form-data" >
            @csrf
            <div class="form-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Category Type</label>
                    <input type="hidden" class="form-control" name="category_id" value="{{$category->category_id}}" >

                    <input type="text" class="form-control" name="category_name" value="{{old('category_name',$category->category_name)}}" placeholder="Category Type">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Business Type</label>
                    <select name="business_type_id" required="" class="form-control" >
                      <option value=""> Select Business Type</option>
                      @foreach($business_types as $key)
                       <option {{old('business_type_id',$category->business_type_id) == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id}}"> {{$key->business_type_name }} </option>
                        @endforeach
                      </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Icon [150*150]</label>
                    <input type="file"  class="form-control" accept="image/x-png,image/jpg,image/jpeg"
                    name="category_icon" value="{{old('category_icon',$category->category_icon)}}" placeholder="category Image">
                    <img src="{{asset('/assets/uploads/category/icons/'.$category->category_icon)}}"  width="100" style="height:60px" "width :50px">
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label class="form-label">Category Description</label>
                    <textarea class="form-control"
                    name="category_description" rows="4" id="category_description" placeholder="Category Description">{{old('category_description',$category->category_description)}}</textarea>
                  </div>
                  <div class="col-md-12">
                    <div  class="form-group">
                      <center>
                      <button type="submit" class="btn btn-raised btn-primary">
                      <i class="fa fa-check-square-o"></i> Update</button>
                      <button type="reset" class="btn btn-raised btn-success">
                      Reset</button>
                      <a class="btn btn-danger" href="{{ route('admin.list_category') }}">Cancel</a>
                      </center>
                    </div>
                  </div>
                </div>
                <script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>
                <script>CKEDITOR.replace('category_description');</script>
              </form>
           {{--  </div>
          </div> --}}
        </div>
      </div>
    </div>
    @endsection
