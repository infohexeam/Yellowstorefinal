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
                                  
                        @if(auth()->user()->user_role_id == 0)
                          <a href=" {{ url('admin/feedback-questions/list') }}" class=" text-white btn btn-block btn-success">
                            List Feedback Question </a>
                         @endif  
                         
                                <br>
                            <div class="table-responsive">
                            <table id="exampletable" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Question')}}</th>
                                        <th class="wd-15p">{{__('Product Category')}}</th>
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
                                            <form action="{{route('admin.restore_feedback_question',$data->feedback_question_id)}}" method="POST">
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


 <script> $(function(e) {
       $('#exampletable').DataTable( {
           dom: 'Bfrtip',
           buttons: [
               {
                   extend: 'pdf',
                   title: 'FeedbackQuestions',
                   // orientation:'landscape',
                   footer: true,
                   exportOptions: {
                        columns: [0,1,2],
                        alignment: 'right',
                    },
                     customize: function(doc) {
                         doc.content[1].margin = [ 100, 0, 100, 0 ]; //left, top, right, bottom
                  doc.content.forEach(function(item) {
                  if (item.table) {
                     item.table.widths = [40, '*','*']
                   }
                  })
                }
               },
               {
                   extend: 'excel',
                   title: 'FeedbackQuestions',
                   footer: true,
                   exportOptions: {
                        columns: [0,1,2]
                    }
               }
            ]
       } );
   
   } );
   </script>

@endsection
