<div class="modal fade" id="loan{{$name->employee_no}}" tabindex="-1" role="dialog" aria-labelledby="payrolldata" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="payrolldata">Loans</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
          <div class="modal-body">
            <div class="row">
              <div class='col-md-12 form-group'>
                    <table class='table table-hover table-bordered'>
                        <thead>
                            <tr>
                                <th>Loan Name</th>
                                <th>Amount</th>
                                <th>Frequency</th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach(($name->employee->loan)->where('schedule','Every cut off') as $ins)
                                    <tr>
                                        <td>{{$ins->loan_type->loan_name}}</td>
                                        <td>{{$ins->monthly_ammort_amt}}</td>
                                        <td>{{$ins->schedule}}</td>
                                    </tr>
                                @endforeach
                                @foreach(($name->employee->loan)->where('schedule','This cut off') as $insa)
                                <tr>
                                    <td>{{$ins->loan_type->loan_name}}</td>
                                    <td>{{$ins->monthly_ammort_amt}}</td>
                                    <td>{{$ins->schedule}}</td>
                                </tr>
                                @endforeach
                            @if($payroll_a)
                                @foreach(($name->employee->loan)->where('schedule','Every 1st cut off') as $ins)
                                <tr>
                                    <td>{{$ins->loan_type->loan_name}}</td>
                                    <td>{{$ins->monthly_ammort_amt}}</td>
                                    <td>{{$ins->schedule}}</td>
                                </tr>
                                @endforeach
                            @else
                                @foreach(($name->employee->loan)->where('schedule','Every 2nd cut off') as $ins)
                                <tr>
                                    <td>{{$ins->loan_type->loan_name}}</td>
                                    <td>{{$ins->monthly_ammort_amt}}</td>
                                    <td>{{$ins->schedule}}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                        
                    </table>
              </div>
            </div>
          </div>
        </form> 
      </div>
    </div>
</div>



