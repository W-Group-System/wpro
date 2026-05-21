<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ $action }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach($definition['fields'] as $field)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ $field['label'] }} @if(!empty($field['required'])) <span class="text-danger">*</span> @endif</label>
                                    @if(($field['type'] ?? null) == 'textarea')
                                        <textarea name="{{ $field['name'] }}" class="form-control form-control-sm" rows="2">{{ old($field['name'], $record ? $record->{$field['name']} : '') }}</textarea>
                                    @elseif(($field['type'] ?? null) == 'select')
                                        <select name="{{ $field['name'] }}" class="form-control form-control-sm">
                                            @foreach($field['options'] as $value => $label)
                                                <option value="{{ $value }}" @if(old($field['name'], $record ? $record->{$field['name']} : 'active') == $value) selected @endif>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="{{ $field['type'] ?? 'text' }}" name="{{ $field['name'] }}" step="0.001" class="form-control form-control-sm" value="{{ old($field['name'], $record ? $record->{$field['name']} : '') }}">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
