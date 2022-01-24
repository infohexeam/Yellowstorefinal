@extends('admin.layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12" >
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
               <form action="{{route('admin.store_points_to_customer')}}" method="POST"enctype="multipart/form-data">
                  @csrf
                  <div class="row">


                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Customer Mobile Number *</label>
                           <input type="text" class="form-control" required name="customer_mobile_number" value="{{old('customer_mobile_number')}}" placeholder="Customer Mobile Number">
                        </div>
                    </div>

                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Reward Points *</label>
                           <input type="number" class="form-control" required name="reward_points" value="{{old('reward_points')}}" placeholder="Reward Points">
                        </div>
                    </div>
                    

                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label"> Reward Description *  </label>
                           <textarea  class="form-control" required  name="reward_discription"  placeholder="Reward Description">{{old('reward_discription')}}</textarea>
                        </div>
                    </div>

                  
                </div>


                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_points_to_customer') }}">Cancel</a>
                           </center>
                        </div>
               </form>

      </div>
      </div>
   </div>
</div>
</div>
@endsection


