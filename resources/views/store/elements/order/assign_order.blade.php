@extends('store.layouts.app')
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
               <form action="{{route('store.assign_store_order',$order->order_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                          
                          <input type="hidden" name="order_id" value="{{$order->order_id}}">
                         
                           <label class="form-label">Delivery Boy </label>
                           <select name="delivery_boy_id" required="" class="form-control" >
                                 <option value=""> Select Delivery Boy</option>
                                @foreach($delivery_boys as $key)
                                <option {{old('delivery_boy_id') == $key->delivery_boy_id ? 'selected':''}} value="{{$key->delivery_boy_id}}"> {{$key->delivery_boy_name }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div>
                      </div>


                     <div class="form-group">
                           <center>
                           <button type="submit"  class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Submit</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('store.list_order') }}">Cancel</a>
                           </center>
                        </div>
                  </form>
                </div>


           {{--  </div>
         </div> --}}
      </div>
   </div>
</div>
@endsection
