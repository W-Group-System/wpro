<div class="modal fade" id="loanDetailsedit{{ $loan->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModal" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="newLoanlabel">Loan Registration Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <form method='POST' action='{{url('update-loan/'.$loan->id)}}' onsubmit='show()'>
                    @csrf
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <label for="loanType">Loan Type</label>
                        <select data-placeholder="Select Loan Type"
                            class="form-control form-control-sm required js-example-basic-single " style='width:100%;' name='loan_type'
                            disabled>
                            <option value="">--Select Loan Type--</option>
                            @foreach ($loanTypes as $loanType)
                                <option value="{{ $loanType->id }}" {{  $loan->loan_type->loan_name ==  $loanType->loan_name ? 'selected' : '' }}>
                                    {{ $loanType->loan_name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="loan_type" value="{{ $loan->loan_type ? $loan->loan_type->id : '' }}">
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="employee">Employee</label>
                        {{-- <select data-placeholder="Select Employee" class="form-control form-control-sm required js-example-basic-single "
                            style='width:100%;' name='employee' required readonly>
                            <option value="">--Select Employee--</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{$loan->employee->id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name }}</option>
                            @endforeach
                        </select> --}}
                        <p style="font-size: 15px;">
                            {{ $loan->employee->last_name . ', ' . $loan->employee->first_name . ' ' . $loan->employee->middle_name }}
                        </p>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control form-control-sm" name="amount" id="amount" required min="1"
                            value="{{ $loan->amount}}" step=".01" placeholder="0.00" readonly>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="ammortAmt">Ammortization Amount</label>
                        <input type="number" class="form-control form-control-sm" name="monthly_ammort_amt" id="monthly_ammort_amt"
                            required min="1" placeholder="0.00" step=".01" value="{{ $loan->monthly_ammort_amt}}" required>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="startDate">Start Date</label>
                        <input type="date" class="form-control form-control-sm" name="start_date" id="start_date"
                            value="{{ $loan->start_date }}" readonly>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="expiryDate">End Date</label>
                        <input type="date" class="form-control form-control-sm" name="expiry_date" id="start_date"
                            value="{{ $loan->expiry_date }}" readonly>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="amount">Loan Balance</label>
                        @php
                            $loan_balance = number_format($loan->initial_amount-($loan->pay)->sum('amount'),2,'.','');
                        @endphp
                        <input type="number" class="form-control form-control-sm" name="initial_amount" id="amount" required readonly
                            min="1" value="{{$loan_balance}}" step=".01" placeholder="0.00">
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="frequency">Frequency</label>
                        <select data-placeholder="Frequency" class="form-control form-control-sm required js-example-basic-single "
                            style='width:100%;' name='frequency' required >
                            <option value="">--Frequency--</option>
                            <option value='Every cut off' {{ $loan->schedule == 'Every cut off' ? 'selected' : '' }}>Every cut off</option>
                            <option value='This cut off' {{ $loan->schedule == 'This cut off' ? 'selected' : '' }}>This cut off</option>
                            <option value='Every 1st cut off' {{ $loan->schedule == 'Every 1st cut off' ? 'selected' : '' }}>Every 1st cut off</option>
                        <option value='Every 2nd cut off' {{ $loan->schedule == 'Every 2nd cut off' ? 'selected' : '' }}>Every 2nd cut off</option>
                        </select>
                    </div>
                    <div class="col-lg-6 form-group">
                        <select data-placeholder="Select status" name="status" class="form-control form-control-sm required js-example-basic-single" style="width: 100%">
                            <option value="">--Status--</option>
                            <option value="Active" {{ $loan->status=="Active"?'selected':'' }}>Active</option>
                            <option value="Inactive" {{ $loan->status=="Inactive"?'selected':'' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- @if($loan->loan_beneficiaries->isNotEmpty())
                    @endif --}}
                    @php
                        $loanBeneficiaries = $loan->loan_beneficiaries->pluck('employee_id')->toArray();
                    @endphp
                    <div class="col-lg-12 form-group" id="loanBeneficiariesParent">
                        <label for="loanBeneficiaries">Guarantor</label>
                        <select data-placeholder="Loan Beneficiaries" class="form-control form-control-sm required js-example-basic-single"
                        style='width:100%;' name='loan_beneficiaries[]' multiple>
                            <option value="">--Loan Beneficiaries--</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{in_array($employee->id, $loanBeneficiaries) ? 'selected' : ''}}>{{ $employee->employee_code }} - {{ $employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name }}</option>
                            @endforeach
                        </select>
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
</div>
