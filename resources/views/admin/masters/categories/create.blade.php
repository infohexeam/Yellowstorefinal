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
               <form action="{{route('admin.store_category')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Category Type</label>
                           <input type="text" class="form-control" name="category_name" value="{{old('category_name')}}" placeholder="Category Type">
                        </div>
                     </div>
                     
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Category Icon [150*150]</label>
                           <input type="file" class="form-control" accept="image/x-png,image/jpg,image/jpeg" required
                           name="category_icon" value="{{old('category_icon')}}" placeholder="Category Icon">
                        </div>
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
                     


                       {{--  <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Parent Category</label>

                           <select class="form-control" name="parent_id" id="parent_id">
                              <option value=""> Select Category Type</option>
                              <optgroup label="Main Category">


                                 @foreach ($categories as $key)

                                 <option {{old('parent_id') == $key->category_id ? 'selected':''}} value=" {{ $key->category_id}} "> {{ $key->category_name }}
                                 </option>
                                 @endforeach
                              </optgroup>
                              <optgroup label="Sub Category">
                                 @foreach ($fetchSubcats as $subCats)
                                 <option value=" {{ $subCats->category_id}} "> {{ $subCats->category_name }}
                                 </option>
                                 @endforeach
                              </optgroup>
                           </select>
                        </div>
                     </div> --}}

                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Category Description</label>
                           <textarea class="form-control" id="category_description" name="category_description" rows="4" placeholder="Category Description">{{old('category_description')}}</textarea>
                        </div>
                     </div>
                     <div class="col-md-4"  style="display:none;">
                      <div class="form-group">
                   <label class="form-group custom-switch">
                   Is it just a product listing?
                         
														<input type="checkbox" name="is_product_listed"     class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description"></span>
													</label>
                          </div>
                          </div>
                      <div class="col-md-12">
                        <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
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
