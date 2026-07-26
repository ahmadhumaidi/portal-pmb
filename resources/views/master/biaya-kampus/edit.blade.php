@extends('layouts.app')

@section('title', 'Edit Biaya Kampus')
@section('page-title', 'Edit Biaya Kampus')
@section('page-description', 'Perbarui aturan biaya kampus')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card dashboard-card border-0">
            <div class="card-header">
                <div>
                    <h5>Edit Biaya Kampus</h5>
                    <small>{{ $biayaKampus->kampus->nama_kampus }}{{ $biayaKampus->jurusan ? ' - ' . $biayaKampus->jurusan->nama_jurusan : ' (Semua Prodi)' }}</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('biaya-kampus.update', $biayaKampus) }}">
                    @include('master.biaya-kampus._form', ['biayaKampus' => $biayaKampus])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
