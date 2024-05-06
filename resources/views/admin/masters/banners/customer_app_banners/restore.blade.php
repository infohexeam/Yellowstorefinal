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
                        <div class="card-body">
                                   
                         <a href=" {{ url('/admin/customer/app/banner/list') }}" class=" text-white btn btn-block btn-success">
                          List Customer App Banner
                        </a>
                                <br>
                            <div class="table-responsive">
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
                            <table id="example" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Banner Image')}}</th>
                                        <th class="wd-15p">{{__('Pincode')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                 <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($banners as $data)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                    <td><img src="{{asset('assets/uploads/customer_banner/'.$data->image)}}"  width="50" ></td>
                                        <td>{{ @$data->town['town_name'] }}</td>
                                   
                                        <td>
                                            <form action="{{route('admin.restore_cab',$data->banner_id)}}" method="POST">
                                              @csrf
                                              @method('POST')
                                              <button type="submit" onclick="return confirm('Do you want to restore this item?');"  class="btn btn-sm btn-warning">Restore</button>
                                           </form>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            {{-- table responsive end --}}
                            </div>
                        {{-- Card body end --}}
                        </div>
                    {{-- col 12 end --}}
                </div>
            {{-- row end --}}
            </div>
        {{-- card --}}


        </div>
        {{-- row justify end --}}
    </div>
{{-- container end --}}
</div>







@endsection
