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
                              <div class="card-header">
                        <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
                     </div>
                        <div class="card-body">
                                    <a  data-toggle="modal" data-target="#StockModal01" class="btn btn-block btn-info text-white">
                                    <i class="fa fa-plus"></i> Add Feedback Question </a>
                        @if(auth()->user()->user_role_id == 0)
                          <a href=" {{ url('admin/feedback-questions/restore-list') }}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i> Restore Feedback Question </a>
                         @endif  
                         
                                <br>
                            <div class="table-responsive">
                            <table id="example" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Product Category')}}</th>
                                        <th class="wd-15p">{{__('Question')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($questions as $data)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $data->feedback_question}}</td>
                                        <td>{{ @$data->category->category_name}}</td>

                                        <td>
                                            <form action="{{route('admin.remove_feedback_questions',$data->feedback_question_id)}}" method="POST">
                                                @csrf
                                                    <a class="btn btn-sm btn-cyan text-white"  data-toggle="modal" data-target="#StockModal{{$data->feedback_question_id}}"
                                            >Edit</a>
                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
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


      <div class="modal fade" id="StockModal01" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Add Feedback Question</h5>
                        <button type="button" class="close" data-dismiss="modal"  aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.store_feedback_question') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">
                      
                      
                    <div class="col-md-12">
                        <div class="form-group">
                          <label class="form-label">Category *</label>
                           <select required class="form-control" name="category_id" id="category_id">
                              <option value="">Category</option>
                                 @foreach ($categories as $key)
                                 <option {{old('category_id') == $key->category_id ? 'selected':''}} value=" {{ $key->category_id}} "> {{ $key->category_name }}</option>
                                 @endforeach
                           </select>
                        </div>
                     </div> 
                     
                     
                     <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" >Feedback Question *</label>
                            <textarea class="form-control" id="feedback_question" required
                                name="feedback_question" rows="2" cols="3" placeholder="Feedback Question">{{old('feedback_question')}}</textarea>
                        </div>
                     </div>
                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Add</button>
                        <button type="button" class="btn btn-secondary" onclick="clearTax()" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>



@foreach ($questions as $data)

              <div class="modal fade" id="StockModal{{$data->feedback_question_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Edit Feedback Question</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.update_feedback_question',$data->feedback_question_id) }}" method="POST" enctype="multipart/form-data" >
                 @csrf
                    <input type="hidden" value="{{ $data->feedback_question_id }}" name="feedback_question_id" />
                    <div class="modal-body">
                        
                        
                    <div class="col-md-12">
                        <div class="form-group">
                          <label class="form-label">Category *</label>
                           <select required class="form-control" name="category_id" id="category_id">
                              <option value="">Category</option>
                                 @foreach ($categories as $key)
                                 <option {{old('category_id',$data->category_id) == $key->category_id ? 'selected':''}} value=" {{ $key->category_id}} "> {{ $key->category_name }}</option>
                                 @endforeach
                           </select>
                        </div>
                     </div> 
                     
                     
                     <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" >Feedback Question *</label>
                            <textarea class="form-control" id="feedback_question" required
                                name="feedback_question" rows="2" cols="3" placeholder="Feedback Question">{{old('feedback_question',$data->feedback_question)}}</textarea>
                        </div>
                     </div>
                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
<!-- MESSAGE MODAL CLOSED -->


    @endforeach

@endsection
