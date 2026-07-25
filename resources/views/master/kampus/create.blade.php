@extends('layouts.app')

@section('title', 'Tambah Kampus')
@section('page-title', 'Tambah Kampus')
@section('page-description', 'Tambahkan kampus baru ke Portal PMB')

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-8">

        <div class="card dashboard-card border-0">

            <div class="card-header">
                <div>
                    <h5>Form Kampus</h5>
                    <small>Isi informasi kampus dengan lengkap</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('kampus.store') }}">
                    @include('master.kampus._form')
                </form>
            </div>

        </div>

    </div>
</div>

@endsection