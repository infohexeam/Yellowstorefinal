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
             
                    <form action="{{ route('admin.update_attribute_group',$attributegroup->attr_group_id) }}" method="POST" enctype="multipart/form-data" >
                        @csrf
               
              <div class="form-body">
                <div class="row" style="min-height: 72em;"
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Atribute Group Name</label>
                    <input type="hidden" class="form-control" name="attr_group_id" value="{{$attributegroup->attr_group_id }}" >

                    
                    <input type="text" class="form-control" name="group_name" value="{{old('group_name',$attributegroup->group_name)}}" placeholder="Atribute Group Name">
                        </div>
                    </div>
            

            <div class="col-md-12">
               <div  class="form-group">
                    <center>
                <button type="submit" class="btn btn-raised btn-primary">
                          <i class="fa fa-check-square-o"></i> Update</button>
                          <button type="reset" class="btn btn-raised btn-success">
                         Reset</button>
                         <a class="btn btn-danger" href="{{ route('admin.list_attribute_group') }}">Cancel</a>
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
<script type="text/javascript">
$(document).ready(function(){
$(".switchselector").bootstrapSwitch();
$(".datepicker" ).datepicker({ format: 'mm/dd/yyyy'  });
$(".timepicker").timepicki();
  }); 
</script>
