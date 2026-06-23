@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h4 class="mb-1">Pilih Ahli Utama Baharu</h4>
                    <p class="text-muted mb-0">
                        Sistem akan mengutamakan anak yang layak sebagai pengganti. Jika tiada anak yang layak,
                        pasangan yang masih hidup boleh dipilih sebagai Ahli Utama Baharu.
                    </p>
                </div>

                <a href="{{ route('admin.khairat.members.index') }}" class="btn btn-light">
                    Kembali
                </a>
            </div>
        </div>

        <div class="card-body p-4">


            <div class="alert alert-warning rounded-3">
                <h6 class="mb-2">Maklumat Ahli Utama Lama</h6>

                <div>
                    <strong>Nama:</strong>
                    {{ $oldMainUser->profile->nama ?? $oldMainUser->name }}
                </div>

                <div>
                    <strong>No. Kad Pengenalan:</strong>
                    {{ $oldMainUser->profile->no_kp ?? '-' }}
                </div>

                <div>
                    <strong>Status Kehidupan:</strong>
                    {{ ucwords(str_replace('_', ' ', $oldMainUser->profile->status_kehidupan ?? '-')) }}
                </div>

                @if(!empty($oldMainUser->profile?->replaced_by_user_id))
                    <hr>
                    <div class="text-success">
                        Ahli Utama ini telah digantikan oleh:
                        <strong>
                            {{ $oldMainUser->profile->replacedByUser->name ?? 'User ID: '.$oldMainUser->profile->replaced_by_user_id }}
                        </strong>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <h6 class="mb-2">Syarat Calon Pengganti</h6>

                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <strong>1. Keutamaan Anak</strong>
                            <div class="text-muted small">
                                Anak yang layak akan diberi keutamaan sebagai Ahli Utama Baharu.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <strong>2. Status Kehidupan</strong>
                            <div class="text-muted small">
                                Calon mestilah masih hidup dan berstatus tanggungan aktif.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <strong>3. Umur Anak</strong>
                            <div class="text-muted small">
                                Anak mestilah berumur 18 tahun ke atas berdasarkan no. kad pengenalan.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <strong>4. Status Anak</strong>
                            <div class="text-muted small">
                                Untuk anak, status perkahwinan mestilah bujang.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <strong>5. Pasangan Sebagai Fallback</strong>
                            <div class="text-muted small">
                                Jika tiada anak layak, pasangan yang aktif boleh dipilih.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <strong>6. Pengesahan Admin</strong>
                            <div class="text-muted small">
                                Pemilihan pengganti hanya sah selepas disahkan oleh admin.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $dependentList = $allDependents ?? collect();
                $successorList = $eligibleSuccessors ?? collect();

                $hasChildCandidates = isset($eligibleChildren) && $eligibleChildren->count() > 0;
                $hasSpouseCandidates = isset($spouses) && $spouses->count() > 0;
            @endphp

            <div class="card border rounded-4 mb-4">
                <div class="card-header bg-white p-4">
                    <h5 class="mb-1">Senarai Tanggungan Keluarga</h5>
                    <p class="text-muted mb-0">
                        Paparan ini membantu admin menyemak status kehidupan, hubungan dan kelayakan sebelum memilih pengganti.
                    </p>
                </div>

                <div class="card-body p-0">
                    @if($dependentList->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>Nama Tanggungan</th>
                                        <th>No. KP</th>
                                        <th>Umur</th>
                                        <th>Pertalian</th>
                                        <th>Status Kehidupan</th>
                                        <th>Status Tanggungan</th>
                                        <th>Status Perkahwinan</th>
                                        <th>Kelayakan Pengganti</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($dependentList as $index => $dependent)
                                        @php
                                            $lifeStatus = strtolower($dependent->status_kehidupan ?? 'aktif');
                                            $isAlive = in_array($lifeStatus, ['aktif', 'hidup', 'masih hidup']);

                                            $dependentStatus = strtolower($dependent->status_tanggungan ?? 'aktif');
                                            $isActiveDependent = $dependentStatus === 'aktif';

                                            $isPromoted = !empty($dependent->promoted_user_id);

                                            $isChildEligible = $dependent->is_child_successor_eligible ?? false;
                                            $isSpouseEligible = $dependent->is_spouse_successor_eligible ?? false;

                                            if ($isChildEligible) {
                                                $eligibilityLabel = 'Layak sebagai anak';
                                                $eligibilityClass = 'success';
                                            } elseif ($isSpouseEligible) {
                                                $eligibilityLabel = 'Layak sebagai pasangan';
                                                $eligibilityClass = 'primary';
                                            } elseif ($isPromoted) {
                                                $eligibilityLabel = 'Telah dinaikkan';
                                                $eligibilityClass = 'secondary';
                                            } elseif (!$isAlive) {
                                                $eligibilityLabel = 'Tidak layak - meninggal';
                                                $eligibilityClass = 'danger';
                                            } elseif (!$isActiveDependent) {
                                                $eligibilityLabel = 'Tidak layak - bukan tanggungan aktif';
                                                $eligibilityClass = 'warning';
                                            } elseif ($dependent->pertalian === 'anak' && ($dependent->umur_dari_ic ?? 0) < 18) {
                                                $eligibilityLabel = 'Tidak layak - bawah 18 tahun';
                                                $eligibilityClass = 'warning';
                                            } elseif ($dependent->pertalian === 'anak' && $dependent->status_perkahwinan !== 'bujang') {
                                                $eligibilityLabel = 'Tidak layak - bukan bujang';
                                                $eligibilityClass = 'warning';
                                            } else {
                                                $eligibilityLabel = 'Tidak memenuhi syarat';
                                                $eligibilityClass = 'secondary';
                                            }
                                        @endphp

                                        <tr>
                                            <td class="fw-semibold text-muted">
                                                {{ $index + 1 }}
                                            </td>

                                            <td>
                                                <strong>{{ $dependent->name }}</strong>
                                                @if($isPromoted)
                                                    <div class="small text-muted">
                                                        User ID baharu: {{ $dependent->promoted_user_id }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td>{{ $dependent->no_kp }}</td>

                                            <td>
                                                @if(!empty($dependent->umur_dari_ic))
                                                    {{ $dependent->umur_dari_ic }} tahun
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td>
                                                {{ ucwords(str_replace('_', ' ', $dependent->pertalian ?? '-')) }}
                                            </td>

                                            <td>
                                                @if($isAlive)
                                                    <span class="badge bg-success">
                                                        Masih Hidup
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        Meninggal Dunia
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($isActiveDependent)
                                                    <span class="badge bg-success-subtle text-success border">
                                                        Aktif
                                                    </span>
                                                @elseif($dependentStatus === 'meninggal')
                                                    <span class="badge bg-danger-subtle text-danger border">
                                                        Meninggal
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border">
                                                        {{ ucwords(str_replace('_', ' ', $dependent->status_tanggungan ?? '-')) }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ ucwords(str_replace('_', ' ', $dependent->status_perkahwinan ?? '-')) }}
                                            </td>

                                            <td>
                                                <span class="badge bg-{{ $eligibilityClass }}">
                                                    {{ $eligibilityLabel }}
                                                </span>

                                                @if(!empty($dependent->sebab_tidak_layak))
                                                    <div class="small text-muted mt-1">
                                                        {{ $dependent->sebab_tidak_layak }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4">
                            <div class="alert alert-light border mb-0">
                                Tiada rekod tanggungan untuk Ahli Utama ini.
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border rounded-4">
                <div class="card-header bg-white p-4">
                    <h5 class="mb-1">Calon Ahli Utama Baharu</h5>
                    <p class="text-muted mb-0">
                        Pilih seorang calon yang layak untuk meneruskan pengurusan keluarga ini.
                    </p>
                </div>

                <div class="card-body p-4">

                    @if($successorList->count() > 0)

                        @if($hasChildCandidates)
                            <div class="alert alert-info rounded-3">
                                <strong>Calon anak dijumpai.</strong>
                                Sistem mengutamakan anak yang memenuhi syarat sebagai Ahli Utama Baharu.
                            </div>
                        @elseif($hasSpouseCandidates)
                            <div class="alert alert-primary rounded-3">
                                <strong>Tiada anak yang layak.</strong>
                                Sistem memaparkan pasangan yang masih hidup sebagai calon pengganti.
                            </div>
                        @endif

                        <form action="{{ route('admin.members.successor.store', $oldMainUser->id) }}" method="POST">
                            @csrf

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 70px;">Pilih</th>
                                            <th>Nama Calon</th>
                                            <th>Jenis Calon</th>
                                            <th>No. KP</th>
                                            <th>Umur</th>
                                            <th>Pertalian</th>
                                            <th>Status Kehidupan</th>
                                            <th>Status Perkahwinan</th>
                                            <th>No. Telefon</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($successorList as $candidate)
                                            <tr>
                                                <td class="text-center">
                                                    <input type="radio"
                                                           name="dependent_id"
                                                           value="{{ $candidate->id }}"
                                                           required>
                                                </td>

                                                <td>
                                                    <strong>{{ $candidate->name }}</strong>
                                                </td>

                                                <td>
                                                    @if(($candidate->successor_type ?? '') === 'anak')
                                                        <span class="badge bg-info-subtle text-info border">
                                                            Anak
                                                        </span>
                                                    @elseif(($candidate->successor_type ?? '') === 'pasangan')
                                                        <span class="badge bg-primary-subtle text-primary border">
                                                            Pasangan
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary border">
                                                            Calon
                                                        </span>
                                                    @endif
                                                </td>

                                                <td>{{ $candidate->no_kp }}</td>

                                                <td>
                                                    @if(!empty($candidate->umur_dari_ic))
                                                        {{ $candidate->umur_dari_ic }} tahun
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ ucwords(str_replace('_', ' ', $candidate->pertalian ?? '-')) }}
                                                </td>

                                                <td>
                                                    {{ ucwords(str_replace('_', ' ', $candidate->status_kehidupan ?? '-')) }}
                                                </td>

                                                <td>
                                                    {{ ucwords(str_replace('_', ' ', $candidate->status_perkahwinan ?? '-')) }}
                                                </td>

                                                <td>{{ $candidate->no_tel ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @error('dependent_id')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror

                            <div class="alert alert-info rounded-3 mt-4">
                                <strong>Makluman:</strong>
                                Selepas disahkan, sistem akan menyemak sama ada calon sudah mempunyai akaun tanggungan.
                                Jika sudah ada, akaun calon akan terus dinaik taraf sebagai Ahli Utama Baharu.
                                Jika belum ada akaun, calon perlu mendaftar akaun menggunakan No. KP yang sama sebelum proses penggantian diselesaikan.
                                Rekod tanggungan keluarga dan yuran akan dipindahkan selepas proses penggantian berjaya.
                            </div>

                           <button type="button"
                                    id="confirmSuccessorBtn"
                                    class="btn btn-info">
                                Sahkan Calon Pengganti
                            </button>
                        </form>

                    @else

                        <div class="alert alert-danger rounded-3 mb-0">
                            <h6 class="mb-1">Tiada Calon Pengganti Yang Layak</h6>
                            Tiada anak atau pasangan yang memenuhi syarat untuk dijadikan Ahli Utama Baharu.
                            Sila semak status tanggungan, umur anak, status perkahwinan anak atau sama ada calon telah menjadi Ahli Utama lain.
                        </div>

                    @endif

                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const confirmButton = document.getElementById('confirmSuccessorBtn');

        if (!confirmButton) {
            return;
        }

        confirmButton.addEventListener('click', function () {
            const selectedCandidate = document.querySelector('input[name="dependent_id"]:checked');

            if (!selectedCandidate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Calon belum dipilih',
                    text: 'Sila pilih seorang calon pengganti sebelum meneruskan.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d6efd'
                });

                return;
            }

            Swal.fire({
                icon: 'question',
                title: 'Sahkan Calon Pengganti?',
                html: `
                    <div style="text-align:left; font-size:14px; line-height:1.6;">
                        <p class="mb-2">
                            Calon yang dipilih akan disahkan sebagai pengganti Ahli Utama lama.
                        </p>

                        <ul style="padding-left:18px; margin-bottom:0;">
                            <li>Jika calon sudah mempunyai akaun tanggungan, akaun tersebut akan terus dinaik taraf sebagai Ahli Utama Baharu.</li>
                            <li>Jika calon belum mempunyai akaun, sistem akan menunggu calon mendaftar akaun menggunakan No. KP yang sama.</li>
                            <li>Rekod tanggungan keluarga dan yuran akan dipindahkan selepas proses penggantian berjaya.</li>
                        </ul>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, sahkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    confirmButton.closest('form').submit();
                }
            });
        });
    });
</script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berjaya',
        text: @json(session('success')),
        confirmButtonText: 'OK',
        confirmButtonColor: '#0d6efd'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Ralat',
        text: @json(session('error')),
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc3545'
    });
</script>
@endif
@endsection