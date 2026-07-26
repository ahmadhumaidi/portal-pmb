@extends('layouts.app')

@section('title', 'Edit Koordinator')
@section('page-title', 'Edit Koordinator')
@section('page-description', 'Perbarui informasi koordinator kelas')

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-8">

        <div class="card dashboard-card border-0">

            <div class="card-header">
                <div>
                    <h5>Edit Koordinator</h5>
                    <small>{{ $koordinator->nama_koordinator }}</small>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('koordinator.update', $koordinator) }}">
                    @include('master.koordinator._form', ['koordinator' => $koordinator])
                </form>
            </div>

        </div>

    </div>
</div>

@endsection
