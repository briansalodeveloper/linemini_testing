@if($errors->has($key))
    <span class="error invalid-feedback">{{ $errors->first($key) }}</span>
@endif