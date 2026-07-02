<div class="modal fade" id="loanDetails{{$loan->id}}" tabindex="-1" role="dialog" aria-labelledby="detailsModal" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="newLoanlabel">Loan Registration Details</h5>
                <a href="{{ url('audit_logs').'?'.http_build_query(['model' => 'App\\Loan', 'id' => $loan->id]) }}" target="_blank" class="btn btn-outline-info btn-sm ml-3">
                    <i class="fa fa-history"></i> History Audit Trail
                </a>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                @php
                    $postedLoanPayments = $loan->pay->filter(function($pay) {
                        return $pay->pay_reg && $pay->pay_reg->cut_off_date;
                    });
                    $postedLoanPaymentTotal = $postedLoanPayments->sum('amount');
                @endphp
				<div id="container">
                    <div class='row '>
                        <div class='col-md-6'>
                           Company: {{ $loan->employee->company->company_code }}
                        </div>
                        <div class='col-md-6'>
                           Name: {{ $loan->employee->last_name }},{{ $loan->employee->first_name }}
                        </div>
                    </div>
                    <hr>
                    <div class='row'>
                        <div class='col-md-6'>
                           Loan Type: {{ $loan->loan_type->loan_name }}
                        </div>
                        <div class='col-md-6'>
                           Loan Amount: {{ number_format($loan->amount,2) }}
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-6'>
                            Ammortization Amount: {{ number_format($loan->monthly_ammort_amt,2) }}
                        </div>
                        <div class='col-md-6'>
                            Previous Balance Encoded: {{ number_format($loan->initial_amount,2)}}
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-12'>
                           
                           <b> Loan Balance: {{ number_format($loan->initial_amount-$postedLoanPaymentTotal,2) }}</b>
                        </div>
                    </div>
				</div>
				<hr>
				<h3>Monthly Ammortization</h3>
				<div class="table-responsive-sm">
					<table class="table table-striped
          table-hover">
						<thead class="table-light">
							<tr>
								<th>Cut off</th>
								<th>Amount</th>
								<th>Balance</th>
							</tr>
						</thead>
						<tbody class="table-group-divider">
							{{-- <tr class="table-primary">
								<td scope="row">Item</td>
								<td>Item</td>
								<td>Item</td>
								<td>Item</td>
							</tr>
							<tr class="table-primary">
								<td scope="row">Item</td>
								<td>Item</td>
								<td>Item</td>
								<td>Item</td>
							</tr> --}}
                            @php
                                $loan_balance = $loan->initial_amount;
                            @endphp
                            @forelse($postedLoanPayments as $pay)
                                <tr>
                                    @php
                                        $loan_balance = $loan_balance-$pay->amount;
                                    @endphp
                                    <td>
                                        @if($pay->pay_reg->pay_period_from && $pay->pay_reg->pay_period_to)
                                            {{ $pay->pay_reg->pay_period_from}} - {{ $pay->pay_reg->pay_period_to}}
                                        @else
                                            {{ $pay->pay_reg->cut_off_date }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($pay->amount,2)}}
                                    </td>
                                    <td>
                                        {{ number_format($loan_balance,2)}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No posted loan deductions with cutoff yet.</td>
                                </tr>
                            @endforelse
						</tbody>
						<tfoot>

						</tfoot>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
