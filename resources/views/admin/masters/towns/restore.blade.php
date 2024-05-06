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
                        <div class="card-body">
                                    <a  href=" {{ url('admin/pincode/list') }}" class="btn btn-block btn-info">
                                    List Pincodes </a>
                                     
                                <br>
                            <div class="table-responsive">
                            <table id="exampletable" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Pincode')}}</th>
                                        <th class="wd-15p">{{__('District')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($towns as $data)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $data->town_name}}</td>
                                        <td>{{ @$data->district['district_name']}}</td>

                                        <td>
                                            <form action="{{route('admin.restore_town',$data->town_id)}}" method="POST">
                                                @csrf

                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to restore this item?');"  class="btn btn-sm btn-danger">Restore</button>
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
