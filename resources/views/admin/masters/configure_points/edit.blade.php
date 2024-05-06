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
               <form action="{{route('admin.update_configure_points',$configure_point->configure_points_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Points</label>
                           <input type="number" class="form-control" name="points" value="{{old('points',$configure_point->points)}}" placeholder="Points">
                        </div>
                        </div>
                     <div class="col-md-6">

                         <div class="form-group">
                           <label class="form-label">Order Amount</label>
                            <input type="number" required=""  name="order_amount" class="form-control"  value="{{old('order_amount',$configure_point->order_amount)}}" placeholder="Order Amount ">
                           </div>
                        </div>

                       <div class="col-md-6">
                          <div class="form-group">
                           <label class="form-label">Valid From</label>
                            <input type="date" required=""  name="valid_from" class="form-control"  value="{{old('valid_from',$configure_point->valid_from)}}" placeholder="Valid From ">
                           </div>
                        </div>

                         <div class="col-md-3">
                            <div class="form-group">
                                <br><br>
                                	<label class="custom-switch">
                                                        <input type="hidden" name="isActive" value=0 />
														<input type="checkbox" name="isActive" @if ($configure_point->isActive == 1)
                                                            checked
                                                        @endif  value=1 class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description">Active Status</span>
													</label>
                            </div>
                        </div>



                    </div>

                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Edit</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_configure_points') }}">Cancel</a>
                           </center>
                        </div>
               </form>

      </div>
   </div>
</div>
@endsection


