<select name="per_page" class="form-select form-select-sm w-auto js-per-page-select">
    @foreach ([10 => '10 / halaman', 50 => '50 / halaman', 100 => '100 / halaman', 'semua' => 'Semua'] as $value => $label)
        <option value="{{ $value }}" @selected(request('per_page', 10) == $value)>{{ $label }}</option>
    @endforeach
</select>

@once
    <script>
        document.addEventListener('change', function (event) {
            if (!event.target.matches('.js-per-page-select')) {
                return;
            }

            const url = new URL(window.location.href);
            url.searchParams.set('per_page', event.target.value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    </script>
@endonce
