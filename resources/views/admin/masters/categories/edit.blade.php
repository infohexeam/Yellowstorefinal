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
                    <label class="form-label">Icon [150*150]</label>
                    <input type="file"  class="form-control" accept="image/x-png,image/jpg,image/jpeg"
                    name="category_icon" value="{{old('category_icon',$category->category_icon)}}" placeholder="category Image"><br>
                    <img src="{{asset('/assets/uploads/category/icons/'.$category->category_icon)}}"  width="100" style="height:60px" "width :50px">
                  </div>
                </div>

                <div class="col-md-12">
                  <table  class="table table-striped table-bordered text-nowrap w-100">
                    @foreach (@$category->business_types as $row)
                      <tr>
                        <td>
                          {{ @$row->business_type->business_type_name }} 
                        </td>
                        <td>
                          <a href="{{ url('admin/remove-cb/'.$row->cbt_id ) }}" class="btn brn-sm btn-danger">
                            Delete
                          </a>
                        </td>
                      </tr>
                      
                      <input type="hidden" name="business_type_ids[]" value="{{ $row->business_type_id }}">

                    @endforeach
                </table>
              </div>

                <div class="col-md-6">
                  <div class="form-group">
                     <div id="store">
                        <label class="form-label"> Business Type</label>
                        <select name="business_type_ids[]" required="" class="form-control" >
                        <option value=""> Select Business Type</option>
                           @foreach($business_types as $key)
                           <option  value="{{$key->business_type_id}}"> {{$key->business_type_name }} </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
               </div>

               <div class="col-md-2">
                  <div class="form-group">
                     <label class="form-label">Add more</label>
                      <button type="button" id="addStore" class="btn btn-raised btn-success"> Add More</button>
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
     </div>
    </div>
    </div>


    

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script type="text/javascript">

$(document).ready(function() {
  var wrapper      = $("#store"); //Fields wrapper
 var add_button      = $("#addStore"); //Add button ID

 var x = 1; //initlal text box count


 $(add_button).click(function(e){ //on add input button click
   e.preventDefault();
   //max input box allowed
     x++; //text box increment
     $(wrapper).append('<div> <br> <label class="form-label"> Business Type</label><select name="business_type_ids[]" required="" class="form-control" ><option value=""> Select Business Type</option>@foreach($business_types as $key)<option  value="{{$key->business_type_id}}"> {{$key->business_type_name }} </option>@endforeach</select> <a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box

 });



 $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
   e.preventDefault(); $(this).parent('div').remove(); x--;
 })
});


</script>

    @endsection
