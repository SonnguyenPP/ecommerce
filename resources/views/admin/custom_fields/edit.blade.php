@extends('admin.layouts.app')
@section('page_title', __('Edit :x', ['x' => __('Custom Field')]))
@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="custom-field-edit-container">
        <form action="{{ route('custom_fields.update', ['id' => $customField->id]) }}" method="post" id="custom_field_form" class="mb-3">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h5>
                        <a href="{{ route('custom_fields.index') }}">{{ __('Custom Field') }}</a>
                        <i class="fas fa-angle-right mx-2"></i>
                        {{ __('Edit :x', ['x' => __('Custom Field')]) }}
                    </h5>
                    <div class="card-header-right my-2">
                        <button class="btn btn-dark btn-sm has-spinner-loader mb-0" id="save" type="submit">
                            <i class="feather icon-save ltr:me-1 rtl:ms-1"></i>{{ __('Save') }}
                        </button>
                    </div>
                </div>
                <div class="card-block table-border-style">
                    <div class="row form-tabs">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group row mb-10">
                                    <label for="name" class="control-label require ltr:ps-3 rtl:pe-3">
                                        {{ __('Field Belongs To') }}
                                    </label>
                                    <div class="col-sm-12">
                                        <input type="hidden" name="field_to" value="{{ $customField->field_to }}">
                                        <select class="form-control select2-hide-search inputFieldDesign" id="field_to" disabled>
                                            @foreach ($fieldBelongs as $table => $data)
                                                <option value="{{ $table }}"
                                                    {{ old('field_to', $customField->field_to) == $table ? 'selected' : '' }}>
                                                    {{ $data['title'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">{{ __('Indicates the entity to which the custom field is associated.') }}</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-10">
                                    <label for="name" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Field Name') }}
                                    </label>
                                    <div class="col-sm-12">
                                        <input type="text" placeholder="{{ __('Name') }}"
                                            class="form-control form-width inputFieldDesign" id="name"
                                            name="name" value="{{ old('name', $customField->name) }}" required
                                            oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                        <small class="form-text text-muted">{{ __('Unique identifier or label for the custom field.') }}</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-10">
                                    <label for="type" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Type') }}
                                    </label>
                                    <div class="col-sm-12">
                                        <select class="form-control select2-hide-search inputFieldDesign"
                                            name="type" id="type">
                                            @foreach ($inputTypes as $type => $data)
                                                <option value="{{ $type }}"
                                                    {{ old('type', $customField->type) == $type ? 'selected' : '' }}>
                                                    {{ $data['title'] }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">{{ __('Defines the data format or input method.') }}</small>
                                    </div>
                                </div>
                                
                                <div id="custom_field_option">
                                    @if ($inputTypes[$customField->type]['need_option'])
                                        <div class="form-group row mb-10">
                                            <label for="name" class="control-label require ltr:ps-3 rtl:pe-3">
                                                {{ __('Options') }}
                                            </label>
                                            <div class="col-sm-12">
                                                <textarea class="form-control" name="options" id="options">{{ $customField->options }}</textarea>
                                                @if ($inputTypes[$customField->type]['option_note'])
                                                    <small class="form-text text-muted">{{ $inputTypes[$customField->type]['option_note'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div id="custom_field_default_value">
                                    @php
                                        $default = $inputTypes[$customField->type]['default'];
                                    @endphp
                                    <div class="form-group row mb-10">
                                        <label for="default_value" class="control-label ltr:ps-3 rtl:pe-3">{{ __('Default Value') }}
                                        </label>
                                        <div class="col-sm-12">
                                            <{{ $default['tag'] }} type="{{ $default['type'] }}"
                                                class="form-control form-width {{ $default['tag'] == 'textarea' ? '' : 'inputFieldDesign' }}" id="default_value"
                                                name="default_value" value="{{ old('default_value', $customField->default_value) }}">{{ $default['tag'] == 'textarea' ? $customField->default_value : '' }}
                                                </{{ $default['tag'] }}>
                                            <small class="form-text text-muted">{{ __('Predefined value assumed if no other value is provided.') }}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-10">
                                    <label for="rules" class="control-label ltr:ps-3 rtl:pe-3">{{ __('Rules') }}
                                    </label>
                                    <div class="col-sm-12">
                                        <input type="text"
                                            class="form-control form-width inputFieldDesign" id="rules"
                                            name="rules" value="{{ old('rules', $customField->rules) }}" placeholder="required|max:50|min:3|numeric">
                                        <small class="form-text text-muted">{!! __('Please visit the official documentation for more details: :x', ['x' => '<a href="https://laravel.com/docs/10.x/validation#available-validation-rules" target="_blank">' . __('Click here') . '</a>']) !!}</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-10">
                                    <label for="order" class="control-label ltr:ps-3 rtl:pe-3">
                                        {{ __('Order') }}
                                    </label>
                                    <div class="col-sm-12">
                                        <input type="number"
                                            class="form-control form-width inputFieldDesign" id="order"
                                            name="order" value="{{ old('order', $customField->order) }}">
                                        <small class="form-text text-muted">{{ __('Determines the position or sequence of the custom field.') }}</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-10">
                                    <label for="order" class="require control-label ltr:ps-3 rtl:pe-3">{{ __('Grid') }}
                                    </label>
                                    <div class="col-sm-12">
                                        <div class="input-group" style="background-color: white;">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text rounded-0 ltr:rounded-start rtl:rounded-end">col-md-</span>
                                            </div>
                                            <input class="form-control" type="number" name="column" id="column" min="0" max="12" value="{{ old('column', $customField->column) }}" required oninvalid="this.setCustomValidity('This field is required.')" data-min="The value must be greater than or equal to 0" data-max="The value must be less than or equal to 12">
                                        </div>
                                        <small class="form-text text-muted">{{ __('(Bootstrap Column eq. 12) - Max is 12. Work only admin and vendor panel.') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 offset-md-1">
                                <div class="mt-10p b-bottom-ddd pb-2">
                                    <label for="">{{ __('Options') }}</label>
                                </div>
                                
                                <div id="custom_field_options">
                                    @foreach ($fieldBelongs[$customField->field_to]['options'] as $type => $data)
                                        <div class="d-flex mt-10p">
                                            <div class="ltr:me-3 rtl:ms-3">
                                                <input type="hidden" name="{{ $type }}" value="0">
                                                <div class="switch switch-bg d-inline m-r-10">
                                                    @php
                                                        $shouldChecked = false;
                                                        if ($type == 'status') {
                                                            $shouldChecked = $customField->status;
                                                        } else {
                                                            $pattern = '/\[([^]]+)\]/';
                                                            $key = null;
                                                            preg_match($pattern, $type, $matches);

                                                            if (isset($matches[1])) {
                                                                $key = $matches[1];
                                                            }
                                                            
                                                            $meta = $customField->meta->where('key', $key)->first();
                                                            $shouldChecked = $meta ? $meta->value : $data['default_value'];
                                                        }
                                                    @endphp
                                                    <input type="checkbox" name="{{ $type }}" class="checkActivity" id="{{ $type }}" value="1" @checked($shouldChecked) @disabled($data['is_disabled'])>
                                                    <label for="{{ $type }}" class="cr"></label>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span>{{ $data['title'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-30p b-bottom-ddd pb-2">
                                    <label for="">{{ __('Accessibility') }}</label>
                                </div>
                                <div id="custom_field_accessibility">
                                    @foreach ($fieldBelongs[$customField->field_to]['accessibility']['roles'] as $slug => $role)
                                        <div class="mt-10p">
                                            <div class="form-group row">
                                                <label class="col-4 control-label">{{ $role }}</label>
                                                <div class="col-8 mt-7 accessibility-parent">
                                                    @php
                                                        $meta = $customField->meta->where('key', $slug)->first();
                                                        $metaValues = $meta ? json_decode($meta->value, true) : [];
                                                        $isReadable = array_key_exists('read', $metaValues) && $metaValues['read'] == 1;
                                                        $isWritable = array_key_exists('write', $metaValues) && $metaValues['write'] == 1;
                                                    @endphp
                                                    <div class="checkbox checkbox-warning checkbox-fill d-inline me-3">
                                                        <input type="hidden" name="meta[{{ $slug }}][read]" value="0">
                                                        <input type="checkbox" name="meta[{{ $slug }}][read]" id="{{ $slug . '_read' }}" value="1" @checked($isReadable || $isWritable) @disabled($isWritable)>
                                                        <label class="cr {{ $isWritable ? 'opacity-50' : '' }}" for="{{ $slug . '_read' }}">{{ __('Read') }}</label>
                                                    </div>
                                                    <div class="checkbox checkbox-warning checkbox-fill d-inline me-3">
                                                        <input type="hidden" name="meta[{{ $slug }}][write]" value="0">
                                                        <input type="checkbox" name="meta[{{ $slug }}][write]" id="{{ $slug . '_write' }}" value="1" @checked($isWritable)>
                                                        <label class="cr" for="{{ $slug . '_write' }}">{{ __('Write') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-30p b-bottom-ddd pb-2">
                                    <label for="">{{ __('Visibility') }}</label>
                                </div>
                                <div id="custom_field_visibility">
                                    @foreach ($fieldBelongs[$customField->field_to]['visibility'] as $type => $data)
                                        <div class="d-flex mt-10p">
                                            <div class="ltr:me-3 rtl:ms-3">
                                                <input type="hidden" name="{{ $type }}" value="0">
                                                <div class="switch switch-bg d-inline m-r-10">
                                                    @php
                                                        $shouldChecked = false;
                                                        $key = null;
                                                        $pattern = '/\[([^]]+)\]/';

                                                        preg_match($pattern, $type, $matches);

                                                        if (isset($matches[1])) {
                                                            $key = $matches[1];
                                                        }
                                                        
                                                        $meta = $customField->meta->where('key', $key)->first();
                                                        $shouldChecked = $meta ? $meta->value : $data['default_value'];
                                                    @endphp
                                                    <input type="checkbox" name="{{ $type }}" class="checkActivity" id="{{ $type }}" value="1" @checked($shouldChecked) @disabled($data['is_disabled'])>
                                                    <label for="{{ $type }}" class="cr"></label>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span>{{ $data['title'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div id="custom_field_location">
                                    @if (isset($fieldBelongs[$customField->field_to]['location']))
                                        <div class="mt-30p b-bottom-ddd pb-2">
                                            <label for="">{{ __('Location') }}</label>
                                        </div>
                                        <div id="custom_field_location">
                                            @foreach ($fieldBelongs[$customField->field_to]['location'] as $type => $data)
                                                <div class="form-group row">
                                                    <label for="{{ $type }}"
                                                        class="col-sm-12 control-label require">{{ $data['title'] }}</label>
                                                    <div class="col-sm-12">
                                                        @php
                                                            $key = null;
                                                            $pattern = '/\[([^]]+)\]/';

                                                            preg_match($pattern, $type, $matches);

                                                            if (isset($matches[1])) {
                                                                $key = $matches[1];
                                                            }
                                                            $meta = $customField->meta->where('key', $key)->first();
                                                        @endphp
                                                        <select class="form-control select2 inputFieldDesign"
                                                            name="{{ $type }}" id="{{ $type }}">
                                                            
                                                            @foreach ($data['values'] as $value)
                                                                <option value="{{ $value }}" @selected($value == $meta?->value)>
                                                                    {{ str_replace('_', ' ', ucfirst($value)) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('js')
    <script>
        const belongsTo = @json($fieldBelongs);
        const inputTypes = @json($inputTypes);
        const fieldOptions = "{{ $customField->options }}";
        const defaultValue = "{{ $customField->default_value }}";
    </script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/custom-fields.min.js') }}"></script>
@endsection