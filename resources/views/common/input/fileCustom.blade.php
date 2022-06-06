{{--

@param $idContainer
@param $classContainer
@param $name
@param $id
@param $disabled
@param $accept
@param $label
@param $hiddenName
@param $hiddenValue
@param $originalValue

usage: @include('common.input.fileCustom', [
    'idContainer' => '',
    'classContainer' => '',
    'name' => '',
    'id' => '',
    'disabled' => '',
    'accept' => '',
    'label' => '',
    'hiddenName' => '',
    'hiddenValue' => '',
    'originalValue' => ''
])

--}}
<div {!! isset($idContainer) ? 'id="' . $idContainer . '" ' : '' !!}class="custom-input-file{{ isset($classContainer) ? ' ' . $classContainer : '' }}">
    <input type="file"{!! isset($name) ? ' name="' . $name . '"' : '' !!}{!! isset($id) ? ' id="' . $id . '"' : '' !!}{{ isset($disabled) ? ($disabled ? ' disabled' : '') : '' }}{!! isset($accept) ? ' accept="' . $accept . '"' : '' !!}>
    <button type="button">{{ __('words.SelectFiles') }}</button>
    <span class="label">{!! isset($label) ? (!empty($label) ? $label : '') : '' !!}</span>
    <span>{{ __('words.NotSelected') }}</span>
    <i class="fas fa-sync fa-spin"></i>
    <input type="hidden"
        {!! isset($hiddenName) ? ' name="' . $hiddenName . '"' : '' !!}
        {!! isset($hiddenValue) ? ' value="' . $hiddenValue . '"' : '' !!}
        {!! isset($originalValue) ? ' data-original-value="' . $originalValue . '"' : '' !!}>
</div>
