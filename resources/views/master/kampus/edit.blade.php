@extends('layouts.app')

@section('title', 'Edit Kampus')
@section('page-title', 'Edit Kampus')
@section('page-description', 'Perbarui informasi kampus')

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-8">

        <div class="card dashboard-card border-0">

            <div class="card-header">
                <div>
                    <h5>Edit Kampus</h5>
                    <small>{{ $kampus->nama_kampus }}</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('kampus.update', $kampus) }}">
                    @include('master.kampus._form', ['kampus' => $kampus])
                </form>
            </div>

        </div>

    </div>
</div>

@endsection