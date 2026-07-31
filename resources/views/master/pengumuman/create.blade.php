@extends('layouts.app')

@section('title', 'Tambah Pengumuman')
@section('page-title', 'Tambah Pengumuman')
@section('page-description', 'Buat pengumuman baru untuk mahasiswa dan koordinator')

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-8">

        <div class="card dashboard-card border-0">

            <div class="card-header">
                <div>
                    <h5>Form Pengumuman</h5>
                    <small>Isi judul dan isi pengumuman</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('pengumuman.store') }}">
                    @include('master.pengumuman._form')
                </form>
            </div>

        </div>

    </div>
</div>

@endsection
