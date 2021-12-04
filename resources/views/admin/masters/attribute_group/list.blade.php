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
          <div class="card-body border">
            <br><h5><b>Add Attribute Group</b></h5></br>
            <center>
           
         <form action="{{route('admin.store_attribute_group')}}" method="POST" 
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Attribute Group Name</label>
                           <input type="text" class="form-control" name="group_name" value="{{old('group_name')}}" placeholder="Attribute Group Name">
                        </div>
                  
                        <div class="form-group">
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                      Reset</button>
                        </div>
                        </div>
                       
                     </div>
               
                
               </form>
            </center>
           
         </div>
         <div class="card-body">
          
         @if(auth()->user()->user_role_id == 0) 
          <a href=" {{ url('admin/attribute-group/restore-list') }}" class=" text-white btn btn-block btn-danger m-2">
                           <i class="fa fa-recycle"></i> Restore Attribute Group
                         </a>
                         @endif
            <div class="table-responsive">
               <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                  <thead>
                     <tr>
                        <th class="wd-15p">SL.No</th>
                        <th class="wd-15p">{{ __('Name') }}</th>
                        
                        <th class="wd-15p">{{__('Action')}}</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $i = 0;
                     @endphp
                     @foreach ($attributegroups as $attributegroup)
                     <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $attributegroup->group_name }}</td>
                        
                        <td>
                          
                             
                             <form action="{{route('admin.destroy_attribute_group',$attributegroup->attr_group_id )}}" method="POST"> 
                              <a class="btn btn-sm btn-cyan" 
                                 href="{{url('admin/attribute_group/edit/'.
                                Crypt::encryptString($attributegroup->attr_group_id) )}}">Edit</a>
                             @csrf
                              @method('POST')
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

<!-- MESSAGE MODAL CLOSED -->
@endsection