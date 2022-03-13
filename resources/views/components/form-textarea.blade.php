@props(['label', 'name', 'value' , 'placeholder' => ''])

<div class="form-group">
  <label for="exampleFormControlTextarea1">{{ $label }}</label>
  <textarea name="{{ $name }}" class="form-control  @error($name) is-invalid @enderror" placeholder="{{ $placeholder }}" id="exampleFormControlTextarea1" rows="3">
    {{  old($name, $value ?? null) }}
  </textarea>

  @error($name)
    <span class="invalid-feedback">
      <strong>{{ $message }}</strong>
    </span>
  @enderror
</div>