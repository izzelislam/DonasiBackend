@props(['label', 'name', 'value', 'placeholder' => '','type'=> 'text', 'class', 'readonly', 'uppercase' => false])
<div class="form-group">
  <label for="example-text-input" class="form-control-label">{{ $label }}</label>
  <input 
    class="form-control @error($name) is-invalid @enderror {{ $class ?? '' }}" 
    type={{ $type }}
    placeholder="{{ $placeholder }}"  
    value="{{ old($name, $value ?? null) }}" 
    id="example-text-input"
    name="{{ $name }}"
    {{ $readonly ?? '' }}
    style="{{ $uppercase ? 'text-transform: uppercase' : '' }}"
    
  >

  @error($name)
    <span class="invalid-feedback">
      <strong>{{ $message }}</strong>
    </span>
  @enderror
</div>