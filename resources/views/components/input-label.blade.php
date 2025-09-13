@props(['value', 'error' => false, 'errorMessage' => null])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    <span>{{ $value ?? $slot }}</span>
    @if ($error)
        <span class="text-red-600 text-lg font-bold align-middle">*</span>
        @if ($errorMessage)
            @php
                $msg = strtolower($errorMessage);
            @endphp
            @if (str_contains($msg, 'required'))
                <span class="ml-1 text-xs text-red-600 align-middle">Required</span>
            @elseif (str_contains($msg, 'invalid'))
                <span class="ml-1 text-xs text-red-600 align-middle">Invalid</span>
            @elseif (str_contains($msg, 'failed') || str_contains($msg, 'credentials'))
                <span class="ml-1 text-xs text-red-600 align-middle">Wrong email or password</span>
            @endif
        @endif
    @endif
</label>
