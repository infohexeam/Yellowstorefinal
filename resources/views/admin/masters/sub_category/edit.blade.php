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
          <form action="{{ route('admin.update_sub_category',$sub_category->sub_category_id) }}" method="POST" enctype="multipart/form-data" >
            @csrf
            <div class="form-body">
              <div class="row">

                <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Parent Category *</label>
                           <select required class="form-control" name="category_id" id="category_id">
                              <option value="">Parent Category</option>
                                 @foreach ($categories as $key)
                                 <option {{old('category_id',$sub_category->category_id) == $key->category_id ? 'selected':''}} value=" {{ $key->category_id}} "> {{ $key->category_name }}</option>
                                 @endforeach
                           </select>
                        </div>
                     </div> 


                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Sub Category Type *</label>
                    <!--<input type="hidden" class="form-control" name="category_id" value="{{$sub_category->category_id}}" >-->

                    <input type="text" required class="form-control" name="sub_category_name" value="{{old('sub_category_name',$sub_category->sub_category_name)}}" placeholder="Category Type">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Business Type *</label>
                    <select name="business_type_id" required="" class="form-control" >
                      <option value=""> Select Business Type</option>
                      @foreach($business_types as $key)
                       <option {{old('business_type_id',$sub_category->business_type_id) == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id}}"> {{$key->business_type_name }} </option>
                        @endforeach
                      </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Sub Category Icon [150*150]</label>
                    <input type="file"   class="form-control" accept="image/x-png,image/jpg,image/jpeg"
                    name="sub_category_icon" value="{{old('category_icon',$sub_category->sub_category_icon)}}" placeholder="Category Image">
                    <img src="{{asset('/assets/uploads/category/icons/'.$sub_category->sub_category_icon)}}"  width="100" style="height:60px" "width :50px">
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label class="form-label">Category Description *</label>
                    <textarea class="form-control"
                    name="sub_category_description" required rows="4" id="category_description" placeholder="Category Description">{{old('sub_category_description',$sub_category->sub_category_description)}}</textarea>
                  </div>
                  <div class="col-md-12">
                    <div  class="form-group">
                      <center>
                      <button type="submit" class="btn btn-raised btn-primary">
                      <i class="fa fa-check-square-o"></i> Update</button>
                      <button type="reset" class="btn btn-raised btn-success">
                      Reset</button>
                      <a class="btn btn-danger" href="{{ route('admin.sub_category') }}">Cancel</a>
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
