<div class="modal fade" id="payroll_instruction{{$name->employee_no}}" tabindex="-1" role="dialog" aria-labelledby="payrolldata" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="payrolldata">Instructions</h5>
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
                                <th>Benefit Name</th>
                                <th>Amount</th>
                                <th>Frequency</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach(($name->employee->pay_instructions)->where('frequency','Every cut off') as $ins)
                                    <tr>
                                        <td>{{$ins->benefit_name}}</td>
                                        <td>{{$ins->amount}}</td>
                                        <td>{{$ins->frequency}}</td>
                                        <td>{{$ins->remarks}}</td>
                                    </tr>
                                @endforeach
                                @foreach(($name->employee->pay_instructions)->where('frequency','This cut off') as $insa)
                                    <tr>
                                        <td>{{$insa->benefit_name}}</td>
                                        <td>{{$insa->amount}}</td>
                                        <td>{{$insa->frequency}}</td>
                                        <td>{{$insa->remarks}}</td>
                                    </tr>
                                @endforeach
                            @if($payroll_a)
                                @foreach(($name->employee->pay_instructions)->where('frequency','Every 1st cut off') as $ins)
                                    <tr>
                                        <td>{{$ins->benefit_name}}</td>
                                        <td>{{$ins->amount}}</td>
                                        <td>{{$ins->frequency}}</td>
                                        <td>{{$ins->remarks}}</td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach(($name->employee->pay_instructions)->where('frequency','Every 2nd cut off') as $ins)
                                <tr>
                                    <td>{{$ins->allowance->name}}</td>
                                    <td>{{$ins->amount}}</td>
                                    <td>{{$ins->frequency}}</td>
                                    <td>{{$ins->remarks}}</td>
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



