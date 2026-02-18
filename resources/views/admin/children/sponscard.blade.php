@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center" id="cardsContainer">
        @foreach($children as $child)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card child-card" id="child-card-{{ $child->id }}">
                <h4 style="font-weight: 600;" class="text-success text-center mt-3">Be My Hero!</h4>

                <div class="child-image-wrapper mt-3 mx-auto">
                    <img src="{{ asset($child->img_url) }}" alt="{{ $child->name }}">
                </div>

                <div class="card-body p-1 d-flex flex-column align-items-center">
                    <div class="row w-100 mb-1">
                        <div class="col-md-3 text-center p-2">
                            @php
                            $sponsorUrl = route('sponsorship_card', $child->encoded_id);
                            $hobbies = $child->details->firstWhere('key', 'hobbies')->value ?? '';
                            $aspirations = $child->details->firstWhere('key', 'aspirations')->value ?? '';
                            $case_history = $child->details->firstWhere('key', 'case_history')->value ?? '';
                            $current_grade = $child->details->firstWhere('key', 'current_grade')->value ?? '';
                            @endphp
                            <div class="qr-section mb-2">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(180)->generate($sponsorUrl) !!}
                            </div>
                        </div>
                        <div class="col-md-9 text-center">
                            <div class="details text-center">
                                <h5>{{ $child->name }}</h5>
                                <p class="mb-1"><strong>D.O.B:</strong> {{ $child->dob ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>EDUCATION:</strong> {{ $current_grade ?? 'N/A' }}</p>
                                <p class="mb-1">
                                    <strong>Hobbies:</strong> {{ $hobbies ? ucwords(strtolower($hobbies)) : 'N/A' }},
                                    <strong>Aspirations:</strong> {{ $aspirations ? ucwords(strtolower($aspirations)) : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="description text-center">
                        {{ $aspirations ? $case_history : 'N/A' }}
                        Be part of <b>{{ $child->name }}</b> brighter future today!
                    </div>
                </div>

                <div class="card-footer text-center mt-auto">
                    <img src="{{ asset('../assets/img/logo.png') }}" alt="Website Logo" class="footer-logo mb-1">
                    <p class="mb-0">www.teulekenya.org || +254 721 582323</p>
                </div>

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
/* ... your existing styles ... */
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function downloadCard(childId) {
    const card = document.getElementById(`child-card-${childId}`);
    const downloadBtn = card.querySelector('.no-print');
    downloadBtn.style.display = 'none';

    html2canvas(card, {
        scale: 3,             // increase resolution
        useCORS: true,        // load external images
        allowTaint: false, 
        logging: true, 
        windowWidth: card.scrollWidth,
        windowHeight: card.scrollHeight
    }).then(canvas => {
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png', 1.0); // max quality
        link.download = `child_card_${childId}.png`;
        link.click();
        downloadBtn.style.display = 'block';
    }).catch(err => {
        console.error('Error generating card image:', err);
        downloadBtn.style.display = 'block';
        alert('Failed to generate image. Try again.');
    });
}
</script>
@endsection