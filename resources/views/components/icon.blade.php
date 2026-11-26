@props([
    'name',
    'class' => 'w-5 h-5',
    'ariaHidden' => 'true',
    'ariaLabel' => null,
])

<i
    data-lucide="{{ $name }}"
    {{ $attributes->merge(['class' => $class]) }}
    @if($ariaLabel)
        aria-label="{{ $ariaLabel }}"
        role="img"
    @else
        aria-hidden="{{ $ariaHidden }}"
    @endif
></i>
