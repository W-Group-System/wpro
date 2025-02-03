@extends('layouts.header')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
            @if (count($errors))
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show " role="alert">
                        <span class="alert-inner--icon"><i class="ni ni-fat-remove"></i></span>
                        <span class="alert-inner--text"><strong>Error!</strong> {{ $error }}</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endforeach
            @endif
        
        <div id="payroll" class="tab-pane  active">
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Payroll Instruction
                    <button type="button" class="btn btn-outline-warning btn-icon-text btn-sm"  data-toggle="modal" data-target="#payrollInstruction">
                        <i class="ti-upload btn-icon-prepend"></i>                                                    
                        Upload
                    </button>
                    <a href="{{url('export-intruction-template')}}" class="btn btn-outline-success btn-icon-text btn-sm">
                        <i class="ti-plus btn-icon-prepend"></i>                                                    
                        Export Template
                    </a>
                    
                    <button type="button" class="btn btn-outline-primary btn-icon-text btn-sm" data-toggle="modal" data-target="#addInstruction">
                        <i class="ti-plus"></i>  
                    </button>
                </h4>
                <form method='get' onsubmit='show();' enctype="multipart/form-data">
                    <div class=row>
                    </div>
                  </form>
                  <button id="btnExport" onclick="fnExcelReport();"> EXPORT </button>
                <div class="table-responsive">
                  <table class="table table-hover table-bordered" id='pay_instruction'>
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Location </th>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Benefit Name</th>
                            <th>Amount</th>
                            <th>Frequency</th>
                            <th>Deductible</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($names as $key => $name)
                        <tr>
                            <td>
                                <form method="POST" action="{{url('deletePayRegInstruction/'.$name->id)}}" id="deletePayRegInstructionForm{{$name->id}}" onsubmit="show()">
                                    @csrf 
                                    
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deletePayReg({{$name->id}})">
                                        <i class="ti-trash"></i>
                                    </button>
                                </form>
                            </td>
                            <td>{{$name->location}}</td>
                            <td>{{$name->site_id}}</td>
                            <td>{{$name->name}}</td>
                            <td>{{$name->start_date}}</td>
                            <td>{{$name->end_date}}</td>
                            <td>{{$name->benefit_name}}</td>
                            <td>{{$name->amount}}</td>
                            <td>{{$name->frequency}}</td>
                            <td>{{$name->deductible}}</td>
                            <td>{{$name->remarks}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
           </div>
        </div>
    </div>
</div>
<script>
     function fnExcelReport() {
    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
    var j = 0;
    var tab = document.getElementById('pay_instruction'); // id of table

    for (j = 0; j < tab.rows.length; j++) {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text = tab_text + "</table>";
    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var msie = window.navigator.userAgent.indexOf("MSIE ");

    // If Internet Explorer
    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        txtArea1.document.open("txt/html", "replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus();

        sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
    } else {
        // other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
    }

    return sa;
}


function deletePayReg(id)
{
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this Payroll Instruction",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, submit it!'
    })
    .then((confirmed) => {
        if (confirmed) {
            
            document.getElementById("deletePayRegInstructionForm"+id).submit()

        }
    });
}
</script>
@include('payroll.add_instruction')
@include('payroll.upload_pay_instruction')
@endsection


