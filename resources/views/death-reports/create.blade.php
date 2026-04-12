@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Lapor Kematian</h1>
            <p class="text-muted mb-0">Sila pilih individu yang meninggal dan lengkapkan maklumat laporan dengan tepat.</p>
        </div>
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

    @if(session('error'))
        <div class="alert alert-danger shadow-sm border-0">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('death-report.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card custom-card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">Maklumat Si Mati</h5>
                    </div>
                    <div class="card-body">

                        <div class="alert alert-light border mb-4">
                            <div class="fw-semibold mb-1">Panduan</div>
                            <div class="text-muted small">
                                Pilih kategori laporan dahulu. Sistem akan memaparkan individu yang berkaitan dengan keluarga anda.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kategori Laporan Kematian <span class="text-danger">*</span></label>
                                <select name="deceased_type" id="deceased_type" class="form-control">
                                    <option value="">-- Pilih Kategori Laporan --</option>

                                    @if(!$isMainMember)
                                        <option value="member" {{ old('deceased_type') == 'member' ? 'selected' : '' }}>
                                            Kematian Ahli Utama
                                        </option>
                                    @endif

                                    <option value="dependent" {{ old('deceased_type') == 'dependent' ? 'selected' : '' }}>
                                        Kematian Tanggungan
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Pilih Si Mati <span class="text-danger">*</span></label>
                                <select name="deceased_id" id="deceased_id" class="form-control">
                                    <option value="">-- Pilih Nama --</option>
                                </select>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 bg-light-subtle mt-2">
                            <div class="fw-semibold mb-3">Paparan Maklumat Si Mati</div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted mb-1">Nama Penuh</label>
                                    <div class="fw-semibold" id="preview_name">-</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted mb-1">No. Kad Pengenalan</label>
                                    <div class="fw-semibold" id="preview_nokp">-</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted mb-1">Jantina</label>
                                    <div class="fw-semibold" id="preview_jantina">-</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted mb-1">Umur</label>
                                    <div class="fw-semibold" id="preview_umur">-</div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Alamat Terakhir Si Mati <span class="text-danger">*</span></label>
                                <textarea name="alamat_terakhir" class="form-control" rows="3" placeholder="Sila nyatakan alamat terakhir si mati">{{ old('alamat_terakhir') }}</textarea>
                                <small class="text-muted">Maklumat ini perlu diisi oleh pelapor untuk rujukan pihak pentadbir.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tarikh Meninggal <span class="text-danger">*</span></label>
                                <input type="date" name="tarikh_meninggal" class="form-control"
                                       value="{{ old('tarikh_meninggal') }}"
                                       max="{{ now()->toDateString() }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">No. Permit Kebumi</label>
                                <input type="text" name="no_permit_kebumi" class="form-control"
                                       value="{{ old('no_permit_kebumi') }}"
                                       placeholder="Contoh: PKB-2026-001">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card custom-card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">Dokumen Sokongan</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-muted small mb-3">
                            Muat naik dokumen jika ada untuk memudahkan semakan pentadbir.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sijil Mati</label>
                            <input type="file" name="sijil_mati" class="form-control">
                            <small class="text-muted">Format: JPG, JPEG, PNG, PDF. Maksimum 2MB.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Permit Kebumi</label>
                            <input type="file" name="permit_kebumi" class="form-control">
                            <small class="text-muted">Format: JPG, JPEG, PNG, PDF. Maksimum 2MB.</small>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Dokumen Sokongan Tambahan</label>
                            <input type="file" name="dokumen_sokongan" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card custom-card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">Maklumat Pelapor</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning small mb-3">
                            Maklumat ini merujuk kepada individu yang membuat laporan kematian.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Pelapor <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pelapor" class="form-control"
                                   value="{{ old('nama_pelapor', $pelaporNama) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">No. Kad Pengenalan <span class="text-danger">*</span></label>
                            <input type="text" name="no_kp_pelapor" class="form-control"
                                   value="{{ old('no_kp_pelapor', $pelaporNoKp) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">No. Telefon <span class="text-danger">*</span></label>
                            <input type="text" name="no_tel_pelapor" class="form-control"
                                   value="{{ old('no_tel_pelapor', $pelaporNoTel) }}">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Pertalian dengan Si Mati <span class="text-danger">*</span></label>
                            <input type="text" name="pertalian_pelapor" class="form-control"
                                   value="{{ old('pertalian_pelapor', $pelaporPertalian) }}" readonly>
                            <small class="text-muted">Maklumat ini diambil daripada rekod sistem.</small>
                        </div>
                    </div>
                </div>

                <div class="card custom-card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Peringatan</div>
                        <ul class="text-muted small ps-3 mb-4">
                            <li>Pilih nama si mati yang betul sebelum menghantar laporan.</li>
                            <li>Alamat terakhir si mati perlu diisi oleh pelapor.</li>
                            <li>Laporan akan disemak oleh pentadbir terlebih dahulu.</li>
                        </ul>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bx bx-send me-1"></i> Hantar Laporan Kematian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const memberOptions = @json($memberOptions);
    const dependentOptions = @json($dependentOptions);
    const isMainMember = @json($isMainMember);

    const oldType = @json(old('deceased_type'));
    const oldId = @json(old('deceased_id'));

    const deceasedType = document.getElementById('deceased_type');
    const deceasedId = document.getElementById('deceased_id');

    const previewName = document.getElementById('preview_name');
    const previewNokp = document.getElementById('preview_nokp');
    const previewJantina = document.getElementById('preview_jantina');
    const previewUmur = document.getElementById('preview_umur');

    function getGenderFromIc(ic) {
        if (!ic) return '-';
        const digits = String(ic).replace(/\D/g, '');
        if (!digits.length) return '-';
        const lastDigit = parseInt(digits.slice(-1), 10);
        return lastDigit % 2 === 0 ? 'Perempuan' : 'Lelaki';
    }

    function getAgeFromIc(ic) {
        if (!ic) return '-';

        const digits = String(ic).replace(/\D/g, '');
        if (digits.length < 6) return '-';

        const yy = parseInt(digits.substring(0, 2), 10);
        const mm = parseInt(digits.substring(2, 4), 10);
        const dd = parseInt(digits.substring(4, 6), 10);

        if (mm < 1 || mm > 12 || dd < 1 || dd > 31) return '-';

        const currentYear = new Date().getFullYear() % 100;
        const fullYear = yy <= currentYear ? 2000 + yy : 1900 + yy;

        const birthDate = new Date(fullYear, mm - 1, dd);
        if (isNaN(birthDate.getTime())) return '-';

        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        return age >= 0 ? age : '-';
    }

    function getCurrentDataSet() {
        if (deceasedType.value === 'member') return memberOptions;
        if (deceasedType.value === 'dependent') return dependentOptions;
        return [];
    }

    function populateDeceasedOptions(selectedId = null) {
        const list = getCurrentDataSet();

        deceasedId.innerHTML = '<option value="">-- Pilih Nama --</option>';

        list.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.name} (${item.label})`;

            if (selectedId && String(selectedId) === String(item.id)) {
                option.selected = true;
            }

            deceasedId.appendChild(option);
        });

        updatePreview();
    }

    function updatePreview() {
        const list = getCurrentDataSet();
        const selected = list.find(item => String(item.id) === String(deceasedId.value));

        if (!selected) {
            previewName.textContent = '-';
            previewNokp.textContent = '-';
            previewJantina.textContent = '-';
            previewUmur.textContent = '-';
            return;
        }

        previewName.textContent = selected.name || '-';
        previewNokp.textContent = selected.no_kp || '-';
        previewJantina.textContent = getGenderFromIc(selected.no_kp);
        previewUmur.textContent = getAgeFromIc(selected.no_kp);
    }

    deceasedType.addEventListener('change', function () {
        populateDeceasedOptions();
    });

    deceasedId.addEventListener('change', updatePreview);

    window.addEventListener('load', function () {
        if (oldType) {
            deceasedType.value = oldType;
            populateDeceasedOptions(oldId);
        } else {
            if (isMainMember) {
                deceasedType.value = 'dependent';
                populateDeceasedOptions();
            }
        }
    });
</script>
@endsection