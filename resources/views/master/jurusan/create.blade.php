@extends('layouts.app')

@section('title', 'Tambah Jurusan')
@section('page-title', 'Tambah Jurusan')
@section('page-description', 'Tambahkan jurusan untuk kampus mitra')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card dashboard-card border-0">
            <div class="card-header">
                <div>
                    <h5>Form Jurusan</h5>
                    <small>Pilih kampus dan masukkan nama jurusan</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('jurusan.store') }}">
                    @include('master.jurusan._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection