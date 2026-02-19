@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-center gap-3" id="cardsContainer">
        @foreach($children as $child)
        <div class="d-flex justify-content-center">
            <div class="card child-card" id="child-card-{{ $child->id }}">

                <!-- Title -->
                <h4 class="text-success text-center mt-1 mb-1 fw-bold">Be My Hero!</h4>

                <!-- Child Image -->
                <div class="child-image-wrapper">
                    <img src="{{ asset($child->img_url) }}" alt="{{ $child->name }}">
                </div>

                @php
                    $sponsorUrl = route('sponsorship_card', $child->encoded_id);
                    $hobbies = $child->details->firstWhere('key', 'hobbies')->value ?? '';
                    $aspirations = $child->details->firstWhere('key', 'aspirations')->value ?? '';
                    $case_history = $child->details->firstWhere('key', 'case_history')->value ?? '';
                    $current_grade = $child->details->firstWhere('key', 'current_grade')->value ?? '';
                @endphp

                <!-- QR + Details + Case History container -->
                <div class="content-wrapper flex-grow-1 d-flex flex-column justify-content-center">

                    <!-- QR + Details Block -->
                    <div class="qr-details-wrapper mb-1">
                        <div class="qr-section">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(90)->generate($sponsorUrl) !!}
                        </div>

                        <div class="details">
                            <h5>{{ $child->name }}</h5>
                            <p><strong>D.O.B:</strong> {{ $child->dob ?? 'N/A' }}</p>
                            <p><strong>Education:</strong> {{ $current_grade ?? 'N/A' }}</p>
                            <p><strong>Hobbies:</strong> {{ $hobbies ? ucwords(strtolower($hobbies)) : 'N/A' }}</p>
                            <p><strong>Aspiration:</strong> {{ $aspirations ? ucwords(strtolower($aspirations)) : 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Case History Block (centered when text is small) -->
                    <div class="case-history text-center mx-2">
                        <p>{{ $case_history ?? 'N/A' }}</p>
                        <p>Be part of <b>{{ $child->name }}</b>'s brighter future today!</p>
                    </div>

                    <hr class="my-1">
                </div>

                

                <!-- Footer: static at bottom -->
                <div class="footer-wrapper">
                    <div class="footer-content">
                        <!-- Logo on the left -->
                        <div class="footer-logo-wrapper">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="Teule Logo" class="footer-logo">
                        </div>

                        <!-- Website + Phone on the right -->
                        <div class="footer-contact">
                            <p class="mb-0">www.teulekenya.org</p>
                            <p class="mb-0">+254 721 582323 <br> teuleusa@teulekenya.org</p>
                        </div>
                    </div>
                </div>

                <!-- Floating Download Button -->
                <div class="download-btn-wrapper no-print">
                    <button class="btn btn-success btn-sm" onclick="downloadCard({{ $child->id }})">
                        Download
                    </button>
                </div>

            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
/* Card Size */
.child-card {
    position: relative;
    width: 5in;
    height: 7in;
    padding: 0.2in 0.25in;
    background-image: url('{{ asset('assets/img/childcard_bg.jpg') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 18px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Overlay */
.child-card::after {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.88);
    border-radius: 18px;
    pointer-events: none;
}

.child-card > * {
    position: relative;
    z-index: 1;
}

/* Child Image */
.child-image-wrapper {
    width: 1.4in;
    height: 1.4in;
    border-radius: 50%;
    overflow: hidden;
    margin: 0.1in auto 0.1in auto;
    flex-shrink: 0;
}

.child-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Content Wrapper for QR/details + Case History */
.content-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center; /* centers case history when small */
    flex-grow: 1;
}

/* QR + Details */
.qr-details-wrapper {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 0.2in;
    margin-bottom: 0.15in;
}

.qr-section svg {
    width: 0.9in !important;
    height: 0.9in !important;
}

/* Details */
.details h5 {
    color: #000096;
    font-weight: bold;
    font-size: 15px;
    margin-bottom: 0.05in;
}

.details p {
    font-size: 11px;
    margin-bottom: 2px;
}

/* Case History */
.case-history {
    font-size: 12px;
    margin-bottom: 0.1in;
}

/* Footer: static at bottom */
.footer-wrapper {
    position: absolute;
    bottom: 8px;
    left: 0;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 0.2in;
    box-sizing: border-box;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 80%;
}

.footer-logo {
    width: 0.55in;
}

.footer-contact {
    text-align: left;
    font-size: 11px;
    font-weight: 500;
}

/* Floating download button */
.download-btn-wrapper {
    position: absolute;
    bottom: 8px;
    right: 8px;
    z-index: 10;
}

.download-btn-wrapper .btn {
    padding: 0.2rem 0.5rem;
    font-size: 11px;
}

/* Print */
@media print {
    body { margin: 0; }
    .child-card { box-shadow: none; page-break-inside: avoid; }
    .no-print { display: none !important; }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function downloadCard(childId) {
    const card = document.getElementById(`child-card-${childId}`);
    const downloadBtn = card.querySelector('.no-print');

    downloadBtn.style.display = 'none';

    html2canvas(card, { scale: 3 }).then(canvas => {
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = `child_card_${childId}.png`;
        link.click();
        downloadBtn.style.display = 'block';
    });
}
</script>
@endsection