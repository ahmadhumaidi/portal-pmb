@extends('layouts.app')

@section('title', 'Tambah Staff')
@section('page-title', 'Tambah Staff')
@section('page-description', 'Tambahkan staff baru ke Portal PMB')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card dashboard-card border-0">
            <div class="card-header">
                <div>
                    <h5>Form Staff</h5>
                    <small>Isi informasi staff dengan lengkap</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('staff.store') }}">
                    @include('staff._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

