{{-- New Laborer --}}
<div class="modal fade" id="view{{$loan_a->id}}" tabindex="-1" role="dialog" aria-labelledby="EditHoldayData" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='row'>
                    <div class='col-md-12'>
                        <h5 class="modal-title" id="EditHoldayData">View Loan</h5>
                    </div>
                </div>
            </div>
                <div class="modal-body">
                    <div class='row '>
                        <div class='col-md-6'>
                           Company: {{ $loan_a->employee->company->company_code }}
                        </div>
                        <div class='col-md-6'>
                           Name: {{ $loan_a->employee->last_name }},{{ $loan_a->employee->first_name }}
                        </div>
                    </div>
                    <hr>
                    <div class='row'>
                        <div class='col-md-6'>
                           Loan Type: {{ $loan_a->loan_type->loan_name }}
                        </div>
                        <div class='col-md-6'>
                           Loan Amount: {{ number_format($loan_a->amount,2) }}
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-6'>
                            Ammortization Amount: {{ number_format($loan_a->monthly_ammort_amt,2) }}
                        </div>
                        <div class='col-md-6'>
                            Previous Balance Encoded: {{ number_format($loan_a->initial_amount,2)}}
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-12'>
                           
                           <b> Loan Balance: {{ number_format($loan_a->initial_amount-($loan_a->pay)->sum('amount')+($loan_a->refund)->sum('amount'),2) }}</b>
                        </div>
                    </div>
                    <hr>
                    <div class='row border'>
                        <div class='col-md-12 '>
                            <b> Guarantors: </b>
                        </div>
                        @foreach($loan_a->loan_beneficiaries as $loan_gua)
                        <div class='col-md-12 '>
                            {{$loan_gua->employee->last_name}}, {{$loan_gua->employee->first_name}}
                        </div>
                        @endforeach
                    </div>
                    <hr>
                    <div class='row border'>
                        <div class='col-md-12 border'>
                            <b> Total Payment: {{number_format(($loan_a->pay)->sum('amount')-($loan_a->refund)->sum('amount'),2)}} </b>
                        </div>
                    </div>
                    <div class='row border'>
                        
                        <div class='col-md-4 border'>
                            Cut Off
                         </div>
                        <div class='col-md-4 border'>
                           Amount
                        </div>
                        <div class='col-md-4 border'>
                           Balance
                        </div>
                    </div>
                    @php
                        $loan_balance = $loan_a->initial_amount;
                    @endphp
                    @foreach($loan_a->pay as $pay)
                    <div class='row border'>
                        @php
                            $loan_balance = $loan_balance-$pay->amount;
                        @endphp
                        <div class='col-md-4 border'>
                            {{ $pay->pay_reg->pay_period_from}} - {{ $pay->pay_reg->pay_period_to}}
                        </div>
                        <div class='col-md-4 border'>
                           -{{ number_format($pay->amount,2)}}
                        </div>
                        <div class='col-md-4 border'>
                           {{ number_format($loan_balance,2)}}
                        </div>
                    </div>
                    @php
                        $amen = ($loan_a->refund)->where('payreg_id',$pay->payreg_id)->first();
                    @endphp
                    @if($amen)
                    <div class='row border'>
                        @php
                            $loan_balance = $loan_balance+$amen->amount;
                        @endphp
                        <div class='col-md-4 border'>
                            {{$amen->instruction_name}} <br>
                            {{ $amen->pay_reg->pay_period_from}} - {{ $amen->pay_reg->pay_period_to}}
                        </div>
                        <div class='col-md-4 border'>
                           {{ number_format($amen->amount,2)}}
                        </div>
                        <div class='col-md-4 border'>
                           {{ number_format($loan_balance,2)}}
                        </div>
                    </div>
                    @endif
                    @endforeach
                
                   
                </div>
        </div>
    </div>
</div>