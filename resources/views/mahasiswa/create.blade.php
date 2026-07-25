@extends('layouts.app')

@section('title', 'Tambah Mahasiswa')
@section('page-title', 'Tambah Mahasiswa')
@section('page-description', 'Tambahkan calon mahasiswa baru ke Portal PMB')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card dashboard-card border-0">
            <div class="card-header">
                <div>
                    <h5>Form Mahasiswa</h5>
                    <small>Isi data pendaftaran mahasiswa dengan lengkap</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('mahasiswa.store') }}">
                    @include('mahasiswa._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

