<div class="modal fade" id="viewNopa{{$movement->id}}" tabindex="-1" role="dialog" aria-labelledby="view_nopa" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="view_nopa">View NOPA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                @php
                                    $oldValues = json_decode($movement->old_values, true);
                                    $newValues = json_decode($movement->new_values, true);
                                
                                @endphp
                                <th colspan='3'>Effective Date:  @foreach(array_keys($newValues) as $key) @if($key == 'date_to') {{date('M d, Y',strtotime($newValues[$key]))}} @break @endif @endforeach</th>
                            </tr>
                            <tr>
                                <th>Field</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_keys($oldValues) as $key)
                                <tr>
                                    <th>
                                        @if ($key == 'department_id')
                                            Department
                                        @elseif ($key == 'project')
                                            Project Name
                                        @elseif ($key == 'position')
                                            Position
                                        @elseif ($key == 'level')
                                            Job Level
                                        @elseif ($key == 'classification')
                                            Employment Status
                                        @elseif ($key == 'immediate_sup')
                                            Immediate Supervisor
                                        @else
                                            {{ $key }}
                                        @endif
                                    </th>
                                    <!-- <td>
                                        @if ($key == 'department_id' && isset($oldValues[$key]))
                                            {{ ($departments->where('id',$oldValues[$key])->first())->name }}
                                        @elseif ($key == 'level' && isset($oldValues[$key]))
                                        {{ ($levels->where('id',$oldValues[$key])->first())->name }}
                                        @elseif ($key == 'classification' && isset($oldValues[$key]))
                                        {{ ($classifications->where('id',$oldValues[$key])->first())->name }}
                                        @elseif ($key == 'immediate_sup' && isset($oldValues[$key]))
                                        {{ optional(($users->where('id',$oldValues[$key])->first()))->name }}
                                        @else
                                            {{ $oldValues[$key] ?? '' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($key == 'department_id' && isset($newValues[$key]))
                                        {{ ($departments->where('id',$newValues[$key])->first())->name }}
                                        @elseif ($key == 'level' && isset($newValues[$key]))
                                        {{ ($levels->where('id',$newValues[$key])->first())->name }}
                                        @elseif ($key == 'classification' && isset($newValues[$key]))
                                        {{ ($classifications->where('id',$newValues[$key])->first())->name }}
                                        @elseif ($key == 'immediate_sup' && isset($newValues[$key]))
                                        {{ optional(($users->where('id',$oldValues[$key])->first()))->name }}
                                        @else
                                            {{ $newValues[$key] ?? '' }}
                                        @endif
                                    </td> -->
                                    <td>
                                        @if ($key == 'department_id' && isset($oldValues[$key]))
                                            {{ optional($departments->where('id', $oldValues[$key])->first())->name }}
                                        @elseif ($key == 'level' && isset($oldValues[$key]))
                                            {{ optional($levels->where('id', $oldValues[$key])->first())->name }}
                                        @elseif ($key == 'classification' && isset($oldValues[$key]))
                                            {{ optional($classifications->where('id', $oldValues[$key])->first())->name }}
                                        @elseif ($key == 'immediate_sup' && isset($oldValues[$key]))
                                            {{ optional($users->where('id', $oldValues[$key])->first())->name }}
                                        @else
                                            {{ $oldValues[$key] ?? '' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($key == 'department_id' && isset($newValues[$key]))
                                            {{ optional($departments->where('id', $newValues[$key])->first())->name }}
                                        @elseif ($key == 'level' && isset($newValues[$key]))
                                            {{ optional($levels->where('id', $newValues[$key])->first())->name }}
                                        @elseif ($key == 'classification' && isset($newValues[$key]))
                                            {{ optional($classifications->where('id', $newValues[$key])->first())->name }}
                                        @elseif ($key == 'immediate_sup' && isset($newValues[$key]))
                                            {{ optional($users->where('id', $newValues[$key])->first())->name }}
                                        @else
                                            {{ $newValues[$key] ?? '' }}
                                        @endif
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
