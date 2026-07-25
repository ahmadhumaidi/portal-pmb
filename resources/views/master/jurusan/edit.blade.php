@extends('layouts.app')

@section('title', 'Edit Jurusan')
@section('page-title', 'Edit Jurusan')
@section('page-description', 'Perbarui data jurusan')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card dashboard-card border-0">
            <div class="card-header">
                <div>
                    <h5>Edit Jurusan</h5>
                    <small>{{ $jurusan->nama_jurusan }}</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('jurusan.update', $jurusan) }}">
                    @include('master.jurusan._form', ['jurusan' => $jurusan])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
