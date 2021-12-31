@extends('admin.layouts.app')
@section('content')
<div class="container">
<div class="row justify-content-center">
<div class="col-md-12 col-lg-12">
<div class="card">
<div class="row">
   <div class="col-12" >
      
      @if ($message = Session::get('status'))
      <div class="alert alert-success">
         <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
      </div>
      @endif
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
         <div class="card-header">
            <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
         </div>
         <div class="card-body">
           <a href="{{route('admin.create_attribute_value')}}" class="btn btn-block btn-info">
               <i class="fa fa-plus"></i>
               Create Attribute Value
                  </a>
                 </br> 
         <div class="card-body">
          
            <div class="table-responsive">
               <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                  <thead>
                     <tr>
                        <th class="wd-15p">SL.No</th>
                        <th class="wd-15p">{{ __('Attribute Group') }}</th>
                        <th class="wd-15p">{{ __('Attribute Value') }}</th>
                        <th class="wd-15p">{{__('Action')}}</th>
                     </tr>
                  </thead>
              
                   <tbody>
                     @php
                     $i = 0;
                     @endphp
                     @foreach ($attributevalues as $attributevalue)
                     <tr>
                        <td>{{ ++$i }}</td>
                         <td>{{ $attributevalue->attr_group['group_name'] }}</td>
                        <td>{{ $attributevalue->group_value }}</td>
                        
                        
                        <td>
                           <form action="{{route('admin.destroy_attribute_value',$attributevalue->attr_value_id )}}" method="POST"> 
                              @csrf
                              @method('POST')
                           <a class="btn btn-sm btn-cyan" 
                                 href="{{url('admin/attribute_value/edit/'.
                                Crypt::encryptString($attributevalue->attr_value_id))}}">Edit</a>
                             
                            
                              <button type="submit"  class="btn btn-sm btn-danger">Delete</button>
                           </form>  
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
</div>
</div>
</div>

<!-- MESSAGE MODAL CLOSED -->
@endsection
<script type="text/javascript">
   
    var i = 0;
       
    $("#add").click(function(){
   
        ++i;
   
        $("#dynamicTable").append('<tr><td><input type="text" name="addmore['+i+'][name]" placeholder="Enter your Name" class="form-control" /></td><td><input type="text" name="addmore['+i+'][qty]" placeholder="Enter your Qty" class="form-control" /></td><td><input type="text" name="addmore['+i+'][price]" placeholder="Enter your Price" class="form-control" /></td><td><button type="button" class="btn btn-danger remove-tr">Remove</button></td></tr>');
    });
   
    $(document).on('click', '.remove-tr', function(){  
         $(this).parents('tr').remove();
    });  
   
</script>