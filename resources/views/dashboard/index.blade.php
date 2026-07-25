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
            value="0"
            icon="bi-people"
            variant="primary"
            change="0% bulan ini"
        />
    </div>

    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            title="Total Pembayaran"
            value="Rp0"
            icon="bi-wallet2"
            variant="success"
            change="0% bulan ini"
        />
    </div>

    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            title="Total Kampus"
            value="0"
            icon="bi-buildings"
            variant="warning"
        />
    </div>

    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            title="Total Berkas"
            value="0"
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

                <select class="form-select form-select-sm chart-filter">
                    <option>7 hari terakhir</option>
                    <option>30 hari terakhir</option>
                    <option>Tahun ini</option>
                </select>
            </div>

            <div class="card-body">
                <div class="chart-placeholder">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Grafik akan ditampilkan di sini</span>
                </div>
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
                    <strong>0</strong>
                </div>

                <div class="status-item">
                    <div>
                        <span class="status-dot bg-warning"></span>
                        Belum Lengkap
                    </div>
                    <strong>0</strong>
                </div>

                <div class="status-item">
                    <div>
                        <span class="status-dot bg-danger"></span>
                        Belum Upload
                    </div>
                    <strong>0</strong>
                </div>

                <hr>

                <a href="#" class="btn btn-outline-primary w-100">
                    Lihat Semua Berkas
                </a>

            </div>
        </div>
    </div>

</div>

@endsection
