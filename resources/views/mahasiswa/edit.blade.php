@extends('layouts.app')

@section('title', 'Edit Mahasiswa')
@section('page-title', 'Edit Mahasiswa')
@section('page-description', $mahasiswa->nama_mahasiswa)

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card dashboard-card border-0">
            <div class="card-header"><div><h5>Form Edit Mahasiswa</h5><small>Perbarui data mahasiswa</small></div></div>
            <div class="card-body">
                <form method="POST" action="{{ route('mahasiswa.update', $mahasiswa) }}">
                    @method('PUT')
                    @include('mahasiswa._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
