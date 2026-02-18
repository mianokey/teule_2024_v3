@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center" id="cardsContainer">
        @foreach($children as $child)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card child-card" id="child-card-{{ $child->id }}">
                <h4 style="font-weight: 600;" class="text-success text-center mt-3">Be My Hero!</h4>
                <!-- Child Image -->
                <div class="child-image-wrapper mt-3 mx-auto">
                    <img src="{{ asset($child->img_url) }}" alt="{{ $child->name }}">
                </div>

                <!-- Card Body -->
                <div class="card-body p-1 d-flex flex-column align-items-center">
                    <div class="row w-100 mb-2">
                        <div class="col-md-3 text-center p-2">
                            <!-- QR Code -->
                            @php
                            $sponsorUrl = route('sponsorship_card', $child->encoded_id);
                            $hobbies = $child->details->firstWhere('key', 'hobbies')->value ?? '';
                            $aspirations = $child->details->firstWhere('key', 'aspirations')->value ?? '';
                            $case_history = $child->details->firstWhere('key', 'case_history')->value ?? '';
                            @endphp
                            <div class="qr-section mb-2">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(90)->generate($sponsorUrl) !!}
                            </div>
                        </div>
                        <div class="col-md-9 text-center">
                            <div class="details text-center">
                                <h5>{{ $child->name }}</h5>
                                <p class="mb-1"><strong>D.O.B:</strong> {{ $child->dob ?? 'N/A' }}</p>
                                <p class="mb-1">  <strong>Hobbies:</strong> {{ $hobbies ? ucwords(strtolower($hobbies)) : 'N/A' }} , <strong>Aspirations:</strong> {{ $aspirations ? ucwords(strtolower($aspirations)) : 'N/A' }} </p>

                            </div>
                        </div>
                    </div>

                    <div class="description text-center">
                        {{ $aspirations ? ucwords(strtolower($case_history)) : 'N/A' }}
                         Be part of <b>{{ $child->name }} </b> brighter future today!
                    </div>
                </div>

                <div class="card-footer text-center mt-auto">
                    <img src="{{ asset('../assets/img/logo.png') }}" alt="Website Logo" class="footer-logo mb-1">
                    <p class="mb-0">www.teulekenya.org || +254 721 582323</p>
                </div>

                <!-- Download Button -->
                <div class="text-center mt-2 no-print">
                    <button class="btn btn-success btn-sm" onclick="downloadCard({{ $child->id }})">
                        <i class="bi bi-download"></i> Download
                    </button>
                </div>

            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
/* Card Styling */
.child-card {
    position: relative;
    background-image: url('{{ asset('assets/img/childcard_bg.jpg') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);

    display: flex;
    flex-direction: column;
    height: 740px;
    padding: 10px;
    overflow: hidden;
}

/* White overlay for reduced opacity */
.child-card::after {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.882); /* adjust opacity */
    border-radius: 20px;
    pointer-events: none;
}

/* Keep content above overlay */
.child-card > * {
    position: relative;
    z-index: 1;
}

/* Child image */
.child-image-wrapper {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 10px auto;
}
.child-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* QR code */
.qr-section svg {
    width: 100px;
    height: 100px;
}

/* Details */
.details h5 {
    color: #000096;
    font-weight: bold;
    font-size: 18px;
}
.details p {
    margin: 0;
    font-size: 13px;
}

/* Description */
.description {
    font-size: 14px;
    margin-top: 5px;
}

/* Footer */
.card-footer .footer-logo {
    width: 40px;
    height: auto;
}

/* Spacing between cards */
#cardsContainer > .col-lg-3,
#cardsContainer > .col-md-4,
#cardsContainer > .col-sm-6 {
    margin-bottom: 20px;
}

/* Hide download button on print */
@media print {
    .no-print {
        display: none !important;
    }
}
</style>

<!-- html2canvas for download -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function downloadCard(childId) {
    const card = document.getElementById(`child-card-${childId}`);
    const downloadBtn = card.querySelector('.no-print');

    // Hide the download button
    downloadBtn.style.display = 'none';

    html2canvas(card).then(canvas => {
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = `child_card_${childId}.png`;
        link.click();

        // Show the download button again
        downloadBtn.style.display = 'block';
    });
}

</script>
@endsection
