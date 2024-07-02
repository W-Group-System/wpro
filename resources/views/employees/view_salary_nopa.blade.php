<div class="modal fade" id="viewSalaryNopa{{$movement->id}}" tabindex="-1" role="dialog" aria-labelledby="view_salaryNopa" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="view_salaryNopa">View Salary NOPA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $oldValues = json_decode($movement->old_values, true);
                                $newValues = json_decode($movement->new_values, true);
                            
                            @endphp

                            @foreach(array_keys($oldValues) as $key)
                                <tr>
                                    <th>
                                        @if ($key == 'basic_salary')
                                            Basic Salary
                                        @elseif ($key == 'de_minimis')
                                            De Ninimis
                                        @elseif ($key == 'position')
                                            De Minimis Allowance
                                        @elseif ($key == 'other_allowance')
                                            Other Allowance
                                        @elseif ($key == 'classification')
                                            Employment Status
                                        @else
                                            {{ $key }}
                                        @endif
                                    </th>
                                    <td>
                                            {{ $oldValues[$key] ?? '' }}
                                    </td>
                                    <td>
                                            {{ $newValues[$key] ?? '' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
