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
          <a href=" {{ url('admin/attribute_group/list') }}" class=" text-white btn btn-block btn-success"> List Attribute Group </a>
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
                            
                            <form action="{{route('admin.restore_attr_group',$attributegroup->attr_group_id)}}" method="POST">
                              @csrf
                              @method('POST')
                              <button type="submit" onclick="return confirm('Do you want to restore this item?');"  class="btn btn-sm btn-warning">Restore</button>
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