@extends('layouts.app')

@section('title', 'Pengumuman')
@section('page-title', 'Pengumuman')
@section('page-description', 'Kelola pengumuman yang tampil di portal mahasiswa dan koordinator')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>
    </div>
@endif

<div class="card dashboard-card border-0">

    <div class="card-header flex-wrap gap-3">
        <div>
            <h5>Daftar Pengumuman</h5>
            <small>Pengumuman aktif akan tampil otomatis di portal mahasiswa dan koordinator</small>
        </div>

        <a href="{{ route('pengumuman.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Pengumuman
        </a>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('pengumuman.index') }}" class="mb-4">
            <div class="input-group" style="max-width: 440px;">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search"></i>
                </span>

                <input
                    type="search"
                    name="search"
                    class="form-control"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari judul atau isi pengumuman..."
                >

                <button class="btn btn-outline-primary" type="submit">
                    Cari
                </button>

                @if (!empty($search))
                    <a href="{{ route('pengumuman.index') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">No.</th>
                        <th>Judul</th>
                        <th>Isi</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 160px;">Dibuat</th>
                        <th class="text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengumumans as $pengumuman)
                        <tr>
                            <td>{{ $pengumumans->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">
                                {{ $pengumuman->judul }}
                                @if ($pengumuman->lampiran_path)
                                    <a href="{{ route('pengumuman.lampiran', $pengumuman) }}" target="_blank" title="Lihat lampiran" class="ms-1 text-muted">
                                        <i class="bi bi-paperclip"></i>
                                    </a>
                                @endif
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($pengumuman->isi, 80) }}</td>
                            <td>
                                @if ($pengumuman->status_aktif)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $pengumuman->created_at->translatedFormat('d M Y H:i') }}</td>
                            <td class="text-end">
                                <a
                                    href="{{ route('pengumuman.edit', $pengumuman) }}"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="Edit"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form
                                    action="{{ route('pengumuman.destroy', $pengumuman) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Hapus pengumuman ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-megaphone fs-1 d-block mb-2"></i>
                                Belum ada pengumuman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            @include('partials.per-page-select')
            {{ $pengumumans->links() }}
        </div>

    </div>
</div>

@endsection
