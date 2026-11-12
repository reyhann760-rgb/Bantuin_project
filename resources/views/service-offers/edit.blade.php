{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}
@extends('layouts.app')

@section('title', 'Edit Menyediakan Jasa - Bantuin')

@section('content')

<div class="card">

    <h1>Edit Menyediakan Jasa</h1>

    <form action="{{ route('service-offers.update', $serviceOffer) }}" method="POST">

        @csrf
        @method('PUT')

        @include('service-offers._form')

        <div class="form-group">
            <label>Status</label>
            <select name="status">
                @foreach(['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $serviceOffer->status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit">Simpan Perubahan</button>

    </form>

    <hr>

    <form action="{{ route('service-offers.destroy', $serviceOffer) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">

        @csrf
        @method('DELETE')

        <button type="submit" style="background: #dc2626;">Hapus</button>

    </form>

    <div class="link">
        <a href="{{ route('service-offers.show', $serviceOffer) }}">Kembali</a>
    </div>

</div>

@endsection