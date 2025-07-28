<div class="Logo_wrapper">
    @if ($logo)
        <img src="{{ asset($logo) }}" alt="logo.png">
    @else
        <img src="{{ asset('assets/images/web/logo.png') }}" alt="logo.png">
    @endif
</div>