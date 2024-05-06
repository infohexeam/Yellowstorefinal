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
             
           <form action="{{ route('admin.update_attribute_value',$attributevalue->attr_value_id) }}" method="POST" enctype="multipart/form-data" >
                        @csrf
               
		      
		      	<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-label">Attribute Group</label>
					<input type="hidden" class="form-control"  name="attr_value_id" value="{{$attributevalue->attr_value_id}}" >

					<select class="form-control" required name="attribute_group_id" id="attribute_group" value="">
                      <option value="">Select Attribute Group</option>
                        @foreach ($attributegroups as $key) 
                        <option {{old('attribute_group_id',$attributevalue->attribute_group_id) == $key->attr_group_id ? 'selected':''}} value=" {{ $key->attr_group_id}} "> {{ $key->group_name }} 
                        </option>
                        @endforeach
                      </select> 
					
						</div>
					</div>
				<div class="col-md-6">
				 <div class="form-group">
						<label class="form-label">Attribute Value</label>
					  		<input type="text" class="form-control" required name="group_value" value="{{old('attributevalue',$attributevalue->group_value)}}" placeholder="Attribute Value Name">
				</div>
					
				</div>
				
                          {{-- <div class="col-md-6">
                          	  <div class="color_code" style="display: none">
                        
                        <div class="form-group">
                        <label class="form-label">Color Code</label>
                           <input type="text" class="form-control" name="Hexvalue"  value="{{old('Hexvalue',$attributevalue->Hexvalue)}}" placeholder="Color Code">
                           </div>
                         </div>
                     </div> --}}
                 </div>

			<div class="col-md-12">
 			   <div  class="form-group">
 					<center>
              	<button type="submit" class="btn btn-raised btn-primary">
				          <i class="fa fa-check-square-o"></i> Update</button>
				          <button type="reset" class="btn btn-raised btn-success">
				         Reset</button>
				         <a class="btn btn-danger" href="{{ route('admin.list_attribute_value') }}">Cancel</a>
					</center>
               </div>  
                
            </div>
      
    </form>
</div>
 </div>
 </div>
</div>
</div>
@endsection
 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
$(function() {
        $('.form-control').on('keypress', function(e) {
            if (e.which == 45){
                alert('No negative data allowed');
                return false;
            }
             if (e.which == 46){
                alert('No Decimal data allowed');
                return false;
            }
        });
});
    $(document).ready(function(){
     $('#attribute_group').change(function(){
      //alert('dsd');
      var attribute_group_id = $(this).val();
     

        if($(this).val() == 2)

        {
          $('.color_code').show();

        } 
       
        else
        {
          $('.color_code').hide();
     
        }
     });
   });

</script>
