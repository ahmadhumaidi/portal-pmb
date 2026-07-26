@extends('layouts.app')

@section('title', 'Tambah Koordinator')
@section('page-title', 'Tambah Koordinator')
@section('page-description', 'Tambahkan koordinator kelas baru ke Portal PMB')

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-8">

        <div class="card dashboard-card border-0">

            <div class="card-header">
                <div>
                    <h5>Form Koordinator</h5>
                    <small>Isi informasi koordinator kelas</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('koordinator.store') }}">
                    @include('master.koordinator._form')
                </form>
            </div>

        </div>

    </div>
</div>

@endsection
