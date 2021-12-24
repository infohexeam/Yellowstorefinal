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
                    <form id="myForm" action="{{route('admin.update_cus_tc')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                        <div class="col-md-12">
                         <div class="form-group">
                           <label class="form-label">Terms & Conditions</label>
                            <textarea id="tc" name="tc" class="form-control">{{ @$tc->terms_and_condition }}</textarea>
                           </div>
                        </div>
                        </div>

                            <div class="form-group">
                                <center>
                                <button type="submit" id="submit" class="btn btn-raised btn-primary">
                                <i class="fa fa-check-square-o"></i> Update</button>
                                <button type="reset" class="btn btn-raised btn-success">
                                Reset</button>
                                </center>
                            </div>
                    </form>
            </div>


      </div>
   </div>
</div>

<script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>
<script>CKEDITOR.replace('tc');</script>

@endsection

