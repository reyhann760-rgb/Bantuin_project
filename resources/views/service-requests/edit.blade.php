{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}
@extends('layouts.app')

@section('title', 'Edit Butuh Jasa - Bantuin')

@section('content')

<div class="card">

    <h1>Edit Butuh Jasa</h1>

    <form action="{{ route('service-requests.update', $serviceRequest) }}" method="POST">

        @csrf
        @method('PUT')

        @include('service-requests._form')

        <div class="form-group">
            <label>Status</label>
            <select name="status">
                @foreach(['open', 'in_progress', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $serviceRequest->status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit">Simpan Perubahan</button>

    </form>

    <hr>

    <form action="{{ route('service-requests.destroy', $serviceRequest) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">

        @csrf
        @method('DELETE')

        <button type="submit" style="background: #dc2626;">Hapus</button>

    </form>

    <div class="link">
        <a href="{{ route('service-requests.show', $serviceRequest) }}">Kembali</a>
    </div>

</div>

@endsection