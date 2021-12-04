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
               <form action="{{route('admin.update_customer_reward',$customer_reward->reward_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Transaction Type</label>
                           <input type="hidden" name="reward_id" value="{{old('reward_id,$customer_reward->reward_id')}}">
                            <select name="transaction_type_id" required="" class="form-control" >
                              <option value=""> Select Transaction Type</option>
                                @foreach($transaction_types as $key)
                                <option {{old('transaction_type_id',$customer_reward->transaction_type_id) == $key->transaction_type_id ? 'selected':''}} value="{{$key->transaction_type_id}}"> {{$key->transaction_type }} </option>
                                @endforeach
                              </select>
                        </div>
                         <div class="form-group">
                           <label class="form-label">Reward Approved Date</label>
                            <input type="date" required=""  name="reward_approved_date" class="form-control"  value="{{old('reward_approved_date',$customer_reward->reward_approved_date)}}" placeholder="Transaction Rule">
                           </div>
                        </div>
                           
                        
                          <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Expiry Date</label>
                          <input type="date" required name="reward_point_expire_date" class="form-control" placeholder="Transaction Point value" value="{{old('reward_point_expire_date',$customer_reward->reward_point_expire_date)}}">
                           
                        </div>
                          <div class="form-group">
                            <label class="form-label"> Reward Point Earned</label>
                          <input type="number"  name="reward_points_earned" class="form-control" placeholder="Transaction Earning Type" value="{{old('reward_points_earned',$customer_reward->reward_points_earned)}}" required="">
                        </div>
                     </div>
                 </div>
                
                         <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_customer_reward') }}">Cancel</a>
                           </center>
                        </div>
                  </form>
                </div>
                    
         {{--    </div>
         </div> --}}
      </div>
   </div>
</div>
@endsection
