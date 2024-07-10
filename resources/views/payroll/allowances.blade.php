<div class="modal fade" id="allowances{{$name->employee_no}}" tabindex="-1" role="dialog" aria-labelledby="payrolldata" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="payrolldata">Allowances</h5>
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
                                <th>Allowance Name</th>
                                <th>Amount</th>
                                <th>Frequency</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($name->employee->allowances)->where('schedule','Every cut off') as $all)
                            <tr>
                                <td>{{$all->allowance->name}}</td>
                                <td>{{$all->allowance_amount}}</td>
                                <td>{{$all->schedule}}</td>
                            </tr>
                        @endforeach
                        @foreach(($name->employee->allowances)->where('schedule','This cut off') as $all)
                        <tr>
                            <td>{{$all->allowance->name}}</td>
                            <td>{{$all->allowance_amount}}</td>
                            <td>{{$all->schedule}}</td>
                        </tr>
                        @endforeach
                        @if($payroll_a)
                            @foreach(($name->employee->allowances)->where('schedule','Every 1st cut off') as $all)
                            <tr>
                                <td>{{$all->allowance->name}}</td>
                                <td>{{$all->allowance_amount}}</td>
                                <td>{{$all->schedule}}</td>
                            </tr>
                            @endforeach
                        @else
                            @foreach(($name->employee->allowances)->where('schedule','Every 2nd cut off') as $all)
                            <tr>
                                <td>{{$all->allowance->name}}</td>
                                <td>{{$all->allowance_amount}}</td>
                                <td>{{$all->schedule}}</td>
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



