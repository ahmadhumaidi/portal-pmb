@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Ringkasan aktivitas Portal PMB')

@section('content')

<div class="welcome-card mb-4">
    <div>
        <span class="welcome-badge">
            <i class="bi bi-stars"></i>
            Portal PMB
        </span>

        <h2>Selamat datang, Administrator 👋</h2>

        <p>
            Kelola data mahasiswa, dokumen, pembayaran, dan hasil akademik
            dalam satu aplikasi.
        </p>
    </div>

    <div class="welcome-icon">
        <i class="bi bi-mortarboard"></i>
    </div>
</div>

<div class="row g-4 mb-4">

    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            title="Total Mahasiswa"
            value="{{ number_format($totalMahasiswa, 0, ',', '.') }}"
            icon="bi-people"
            variant="primary"
            change="{{ $mahasiswaChange }}"
        />
    </div>

    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            title="Total Pembayaran"
            value="Rp{{ number_format($totalPembayaran, 0, ',', '.') }}"
            icon="bi-wallet2"
            variant="success"
            change="{{ $pembayaranChange }}"
        />
    </div>

    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            title="Total Kampus"
            value="{{ number_format($totalKampus, 0, ',', '.') }}"
            icon="bi-buildings"
            variant="warning"
        />
    </div>

    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            title="Total Berkas"
            value="{{ number_format($totalBerkas, 0, ',', '.') }}"
            icon="bi-folder-check"
            variant="info"
        />
    </div>

</div>

<div class="row g-4">

    <div class="col-xl-8">
        <div class="card dashboard-card border-0">
            <div class="card-header">
                <div>
                    <h5>Grafik Pendaftaran</h5>
                    <small>Perkembangan pendaftaran mahasiswa</small>
                </div>

                <select class="form-select form-select-sm chart-filter" id="registrationChartFilter">
                    <option value="harian">7 hari terakhir</option>
                    <option value="bulanan30">30 hari terakhir</option>
                    <option value="tahunan">Tahun ini</option>
                </select>
            </div>

            <div class="card-body">
                <div id="registrationChartEmpty" class="chart-placeholder d-none">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Belum ada data pendaftaran</span>
                </div>
                <canvas id="registrationChart" data-chart='@json($chartData)' height="110"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card dashboard-card border-0">
            <div class="card-header">
                <div>
                    <h5>Status Berkas</h5>
                    <small>Ringkasan kelengkapan dokumen</small>
                </div>
            </div>

            <div class="card-body">

                <div class="status-item">
                    <div>
                        <span class="status-dot bg-success"></span>
                        Lengkap
                    </div>
                    <strong>{{ number_format($berkasLengkap, 0, ',', '.') }}</strong>
                </div>

                <div class="status-item">
                    <div>
                        <span class="status-dot bg-warning"></span>
                        Belum Lengkap
                    </div>
                    <strong>{{ number_format($berkasBelumLengkap, 0, ',', '.') }}</strong>
                </div>

                <div class="status-item">
                    <div>
                        <span class="status-dot bg-danger"></span>
                        Belum Upload
                    </div>
                    <strong>{{ number_format($berkasBelumUpload, 0, ',', '.') }}</strong>
                </div>

                <hr>

                <a href="{{ route('berkas.index') }}" class="btn btn-outline-primary w-100">
                    Lihat Semua Berkas
                </a>

            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    (function () {
        const canvas = document.getElementById('registrationChart');
        const emptyState = document.getElementById('registrationChartEmpty');
        const filter = document.getElementById('registrationChartFilter');

        if (!canvas) return;

        const chartData = JSON.parse(canvas.dataset.chart || '{}');
        const ctx = canvas.getContext('2d');

        function draw(key) {
            const points = chartData[key] || [];
            const hasData = points.some((point) => point.value > 0);

            canvas.classList.toggle('d-none', !hasData);
            emptyState?.classList.toggle('d-none', hasData);

            if (!hasData) return;

            const ratio = window.devicePixelRatio || 1;
            const width = canvas.clientWidth || canvas.parentElement.clientWidth;
            const height = canvas.height;

            canvas.width = width * ratio;
            canvas.style.height = height + 'px';
            canvas.height = height * ratio;
            canvas.style.width = width + 'px';
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
            ctx.clearRect(0, 0, width, height);

            const max = Math.max(...points.map((point) => point.value), 1);
            const paddingBottom = 24;
            const paddingTop = 10;
            const plotHeight = height - paddingBottom - paddingTop;
            const gap = points.length > 20 ? 2 : 8;
            const barWidth = (width - gap * (points.length - 1)) / points.length;

            ctx.fillStyle = '#2563eb';
            ctx.font = '11px system-ui, sans-serif';
            ctx.textAlign = 'center';

            points.forEach((point, index) => {
                const barHeight = (point.value / max) * plotHeight;
                const x = index * (barWidth + gap);
                const y = paddingTop + (plotHeight - barHeight);

                ctx.fillStyle = '#2563eb';
                ctx.beginPath();
                ctx.roundRect(x, y, barWidth, barHeight, 4);
                ctx.fill();

                if (points.length <= 12) {
                    ctx.fillStyle = '#6b7280';
                    ctx.fillText(point.label, x + barWidth / 2, height - 6);
                }
            });
        }

        filter?.addEventListener('change', () => draw(filter.value));
        draw(filter?.value || 'harian');
    })();
</script>
@endpush
