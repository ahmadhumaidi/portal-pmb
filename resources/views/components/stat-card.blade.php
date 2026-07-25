@props([
    'title',
    'value' => '0',
    'icon' => 'bi-bar-chart',
    'variant' => 'primary',
    'change' => null,
])

<div class="card stat-card border-0">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="stat-title">{{ $title }}</div>
                <div class="stat-value">{{ $value }}</div>

                @if ($change)
                    <div class="stat-change">
                        <i class="bi bi-arrow-up-right"></i>
                        {{ $change }}
                    </div>
                @endif
            </div>

            <div class="stat-icon stat-icon-{{ $variant }}">
                <i class="bi {{ $icon }}"></i>
            </div>
        </div>

    </div>
</div>
