<div class="modal fade" id="createSalaryNopa" tabindex="-1" role="dialog" aria-labelledby="createSalaryNopaInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSalaryNopaLabel">Create Salary Notice of Personnel Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method='POST' action='updateEmpSalaryMovementHR/{{ optional($user->employee->employee_salary)->id }}' onsubmit='show()' enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-card">
                        <div class='row mb-2'>
                            <div class='col-lg-12 mt-1'>
                                <div class="table-responsive">
                                    <strong>Salary Details</strong>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    Item
                                                </th>
                                                <th class="text-center">
                                                    From
                                                </th>
                                                <th class="text-center">
                                                    To
                                                </th>
                                                <th class="text-center">
                                                    % Increase
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p>Basic Salary</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="basic_salary_from" class="form-control" type="text" name="basic_salary_from" value="{{ optional($user->employee->employee_salary)->basic_salary ?? 'empty' }}" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="basic_salary_to" class="form-control" type="text" name="basic_salary_to">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="basic_increase" class="form-control" type="text" name="basic_increase" readonly>
                                                    </div>
                                                </td>
                                            </tr>   
                                            <tr>
                                                <td>
                                                    <p>De Minimis Allowance</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="de_minimis_from" class="form-control" type="text" name="de_minimis_from" value="{{ optional($user->employee->employee_salary)->de_minimis ?? 'empty' }}" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="de_minimis_to" class="form-control" type="text" name="de_minimis_to">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="de_minimis_increase" class="form-control" type="text" name="de_minimis_increase" readonly>
                                                    </div>
                                                </td>
                                            </tr>  
                                            <tr>
                                                <td>
                                                    <p>Other Allowances</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="other_allowance_from" class="form-control" type="text" name="other_allowance_from" value="{{ optional($user->employee->employee_salary)->other_allowance ?? 'empty' }}" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="other_allowance_to" class="form-control" type="text" name="other_allowance_to">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="other_allowance_increase" class="form-control" type="text" name="other_allowance_increase" readonly>
                                                    </div>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td><p>Total</p></td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="total_from" class="form-control" type="text" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="total_to" class="form-control" type="text" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input id="total_increase" class="form-control" type="text" readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>Attachment</p>
                                                </td>
                                                <td colspan="3">
                                                    <div class='col-md-12'>
                                                        <input type="file" name="file" class="form-control form-control-sm required js-example-basic-single ">  
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function updateTotals() {
        var basicSalaryFrom = parseFloat(document.getElementById('basic_salary_from').value) || 0;
        var basicSalaryTo = parseFloat(document.getElementById('basic_salary_to').value) || 0;
        var deMinimisFrom = parseFloat(document.getElementById('de_minimis_from').value) || 0;
        var deMinimisTo = parseFloat(document.getElementById('de_minimis_to').value) || 0;
        var otherAllowanceFrom = parseFloat(document.getElementById('other_allowance_from').value) || 0;
        var otherAllowanceTo = parseFloat(document.getElementById('other_allowance_to').value) || 0;

        var totalFrom = basicSalaryFrom + deMinimisFrom + otherAllowanceFrom;
        var totalTo = basicSalaryTo + deMinimisTo + otherAllowanceTo;

        document.getElementById('total_from').value = totalFrom.toFixed(2);
        document.getElementById('total_to').value = totalTo.toFixed(2);

        var totalIncreaseField = document.getElementById('total_increase');
        if (totalFrom > 0 && totalTo > 0) {
            var totalIncrease = ((totalTo - totalFrom) / totalFrom) * 100;
            totalIncreaseField.value = totalIncrease.toFixed(2);
        } else {
            totalIncreaseField.value = '0';
        }
    }

    document.getElementById('basic_salary_to').addEventListener('input', function() {
        var basicSalaryFrom = parseFloat(document.getElementById('basic_salary_from').value) || 0;
        var basicSalaryTo = parseFloat(this.value) || 0;
        var increaseField = document.getElementById('basic_increase');

        if (basicSalaryFrom > 0 && basicSalaryTo > 0) {
            var increase = ((basicSalaryTo - basicSalaryFrom) / basicSalaryFrom) * 100;
            increaseField.value = increase.toFixed(2);
        } else {
            increaseField.value = '0';
        }
        updateTotals();
    });

    document.getElementById('de_minimis_to').addEventListener('input', function() {
        var deMinimisFrom = parseFloat(document.getElementById('de_minimis_from').value) || 0;
        var deMinimisTo = parseFloat(this.value) || 0;
        var deminimisIncreaseField = document.getElementById('de_minimis_increase');

        if (deMinimisFrom > 0 && deMinimisTo > 0) {
            var increase = ((deMinimisTo - deMinimisFrom) / deMinimisFrom) * 100;
            deminimisIncreaseField.value = increase.toFixed(2);
        } else {
            deminimisIncreaseField.value = '0';
        }
        updateTotals();
    });

    document.getElementById('other_allowance_to').addEventListener('input', function() {
        var otherAllowanceFrom = parseFloat(document.getElementById('other_allowance_from').value) || 0;
        var otherAllowanceTo = parseFloat(this.value) || 0;
        var otherAllowanceIncreaseField = document.getElementById('other_allowance_increase');

        if (otherAllowanceFrom > 0 && otherAllowanceTo > 0) {
            var increase = ((otherAllowanceTo - otherAllowanceFrom) / otherAllowanceFrom) * 100;
            otherAllowanceIncreaseField.value = increase.toFixed(2);
        } else {
            otherAllowanceIncreaseField.value = '0';
        }
        updateTotals();
    });

    window.onload = function() {
        updateTotals();
    };
</script>
