@extends('layouts.app')

@section('title', 'Edit Pengumuman')
@section('page-title', 'Edit Pengumuman')
@section('page-description', 'Perbarui pengumuman')

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-8">

        <div class="card dashboard-card border-0">

            <div class="card-header">
                <div>
                    <h5>Edit Pengumuman</h5>
                    <small>{{ $pengumuman->judul }}</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('pengumuman.update', $pengumuman) }}">
                    @include('master.pengumuman._form', ['pengumuman' => $pengumuman])
                </form>
            </div>

        </div>

    </div>
</div>

@endsection
