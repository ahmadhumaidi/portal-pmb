<select name="per_page" class="form-select form-select-sm w-auto" onchange="
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', this.value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
">
    @foreach ([10 => '10 / halaman', 50 => '50 / halaman', 100 => '100 / halaman', 'semua' => 'Semua'] as $value => $label)
        <option value="{{ $value }}" @selected(request('per_page', 10) == $value)>{{ $label }}</option>
    @endforeach
</select>
