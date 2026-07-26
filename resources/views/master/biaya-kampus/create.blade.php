@extends('layouts.app')

@section('title', 'Tambah Biaya Kampus')
@section('page-title', 'Tambah Biaya Kampus')
@section('page-description', 'Atur biaya yang harus disetorkan ke kampus mitra')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card dashboard-card border-0">
            <div class="card-header">
                <div>
                    <h5>Form Biaya Kampus</h5>
                    <small>Pilih kampus, lalu tentukan berlaku untuk semua prodi atau prodi tertentu</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('biaya-kampus.store') }}">
                    @include('master.biaya-kampus._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
