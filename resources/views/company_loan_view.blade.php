{{-- New Laborer --}}
<div class="modal fade" id="view{{$loan_a->id}}" tabindex="-1" role="dialog" aria-labelledby="EditHoldayData" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='row'>
                    <div class='col-md-12'>
                        <h5 class="modal-title" id="EditHoldayData">View </h5>
                    </div>
                </div>
            </div>
                <div class="modal-body">
                    <div class='row'>
                        <div class='col-md-4'>
                            Previous Balance Encoded: {{ number_format($loan_a->initial_amount,2)}}
                        </div>

                    </div>
                    <div class='row border'>
                        <div class='col-md-12 border'>
                           Payment
                        </div>
                    </div>
                    <div class='row border'>
                        <div class='col-md-6 border'>
                           Amount
                        </div>
                        <div class='col-md-6 border'>
                           Cut Off
                        </div>
                    </div>
                    @foreach($loan_a->pay as $pay)
                    <div class='row border'>
                        <div class='col-md-6 border'>
                           {{ $pay->amount}}
                        </div>
                        <div class='col-md-6 border'>
                            {{ $pay->pay_reg->pay_period_from}} - {{ $pay->pay_reg->pay_period_to}}
                        </div>
                    </div>
                    @endforeach
                </div>
        </div>
    </div>
</div>