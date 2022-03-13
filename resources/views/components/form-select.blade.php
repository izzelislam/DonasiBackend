@props(['label', 'name', 'value', 'options', 'default']) {{-- options format: [id => 'name'] (created using pluck) --}}


<div class="form-group">
  <label for="exampleFormControlSelect1">{{ $label }}</label>
  <select class="form-control" id="exampleFormControlSelect1" name={{ $name }}>
    <option value=""></option>
		@isset($default)
    	<option value="{{ $default['value'] }}" selected>{{ $default['label'] }}</option>
		@endisset
		@foreach ($options as $optionId => $optionName)
			<option
				value="{{ $optionId }}"
				@if((string)$optionId === (string)old($name, $value ?? null))
					selected
				@endif
			>
				{{ $optionName }}
			</option>
		@endforeach
  </select>

  <small class="form-text text-muted">Pilih salah satu.</small>
	@error($name)
		<span class="invalid-feedback">
			{{ $message }}
		</span>
	@enderror
</div>