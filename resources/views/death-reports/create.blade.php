@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Lapor Kematian</h1>
    </div>

    @if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berjaya',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK',
            timer: 2500,
            timerProgressBar: true
        }).then(() => {
            window.location.href = "{{ route('home') }}";
        });
    </script>
@endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card custom-card">
        <div class="card-body">
            <form action="{{ route('death-report.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Jenis Si Mati</label>
                    <select name="deceased_type" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="member" {{ old('deceased_type') == 'member' ? 'selected' : '' }}>Ahli Utama</option>
                        <option value="dependent" {{ old('deceased_type') == 'dependent' ? 'selected' : '' }}>Tanggungan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Penuh Si Mati</label>
                    <input type="text" name="nama_si_mati" class="form-control" value="{{ old('nama_si_mati') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">No KP Si Mati</label>
                    <input type="text" name="no_kp_si_mati" class="form-control" value="{{ old('no_kp_si_mati') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jantina</label>
                    <select name="jantina" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="lelaki" {{ old('jantina') == 'lelaki' ? 'selected' : '' }}>Lelaki</option>
                        <option value="perempuan" {{ old('jantina') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Terakhir</label>
                    <textarea name="alamat_terakhir" class="form-control" rows="3">{{ old('alamat_terakhir') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tarikh Meninggal</label>
                    <input type="date" name="tarikh_meninggal" class="form-control" value="{{ old('tarikh_meninggal') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Umur</label>
                    <input type="number" name="umur" class="form-control" value="{{ old('umur') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">No Permit Kebumi</label>
                    <input type="text" name="no_permit_kebumi" class="form-control" value="{{ old('no_permit_kebumi') }}">
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label">Nama Waris / Pelapor</label>
                    <input type="text" name="nama_pelapor" class="form-control" value="{{ old('nama_pelapor') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">No KP Waris / Pelapor</label>
                    <input type="text" name="no_kp_pelapor" class="form-control" value="{{ old('no_kp_pelapor') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">No Telefon Waris / Pelapor</label>
                    <input type="text" name="no_tel_pelapor" class="form-control" value="{{ old('no_tel_pelapor') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pertalian dengan Si Mati</label>
                    <input type="text" name="pertalian_pelapor" class="form-control" value="{{ old('pertalian_pelapor') }}">
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label">Upload Sijil Mati</label>
                    <input type="file" name="sijil_mati" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Permit Kebumi</label>
                    <input type="file" name="permit_kebumi" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Dokumen Sokongan</label>
                    <input type="file" name="dokumen_sokongan" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Hantar Laporan</button>
            </form>
        </div>
    </div>

</div>
@endsection