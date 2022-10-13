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
			         <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
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

                    <form action="{{ route('admin.update_profile') }}" method="POST" enctype="multipart/form-data" >
                        @csrf

		      <div class="form-body">
		      	<div class="row">

				<div class="col-md-4">
			<div class="form-group">
				<label class="form-label">Name</label>
					  <input type="text" required="" class="form-control" readonly="" name="name"  placeholder="Username" value="{{$admin->name}}">

               </div>
				   </div>
				  <div class="col-md-4">
				 <div class="form-group">
						<label class="form-label">Email</label>
					  <input type="email" class="form-control" name="email" value="{{$admin->email}}" placeholder="Email">
				</div>
      			</div>
				  <div class="col-md-4">
					<div class="form-group">
						   <label class="form-label">Number</label>
						 <input type="number" class="form-control" name="phone_number" value="{{$admin->phone_number}}" placeholder="Phone number">
				   </div>
					 </div>

				</div>


			<div class="col-md-12">
 			   <div  class="form-group">
 					<center>
              	<button type="submit" class="btn btn-raised btn-primary">
				          <i class="fa fa-check-square-o"></i> Update</button>

				         <a class="btn btn-danger" href="{{ route('home') }}">Cancel</a>
					</center>
               </div>

            </div>
         </div>


    </form>


                </div>
            </div>
        </div>
    </div>
@endsection

