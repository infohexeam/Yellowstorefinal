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
               <form action="{{route('admin.store_reward_transaction_type')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Transaction type</label>
                           <input type="text" class="form-control" name="transaction_type" value="{{old('transaction_type')}}" placeholder="Transaction Type">
                        </div>
                         <div class="form-group">
                           <label class="form-label">Transaction Rule</label>
                            <input type="text" required=""  name="transaction_rule" class="form-control"  value="{{old('transaction_rule')}}" placeholder="Transaction Rule">
                           </div>
                        </div>
                           
                        
                          <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Transaction Point value</label>
                          <input type="number" required name="transaction_point_value" class="form-control" placeholder="Transaction Point value" value="{{old('transaction_point_value')}}">
                           
                        </div>
                          <div class="form-group">
                            <label class="form-label"> Transaction Earning Point</label>
                          <input type="number"  name="transaction_earning_point" class="form-control" placeholder="Transaction Earning Type" value="{{old('transaction_earning_point')}}" required="">
                        </div>
                     </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Minimum Purchase Amount</label>
                          <input type="number" required name="min_purchase_amount" class="form-control" placeholder="Minimum Purchase Amount" value="{{old('min_purchase_amount')}}">
                           
                        </div>
                      </div>
                 </div>
                  
                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_reward_transaction_type') }}">Cancel</a>
                           </center>
                        </div>
               </form>
            {{-- </div>
         </div> --}}
      </div>
   </div>
</div>
@endsection

 