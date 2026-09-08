```blade
@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
        CARD CONTROLS
    ========================================================== --}}
    <div class="card shadow-sm mb-4 no-print">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-filter"></i> Child Card Filters & Settings
            </h5>
        </div>

        <div class="card-body">

            <div class="row g-3">

                {{-- STATUS FILTERS --}}
                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Show Children By Status
                    </label>

                    <div class="d-flex flex-wrap gap-3">

                        @php
                            /*
                             * Add/remove statuses here according to
                             * the values in your children.status column.
                             */
                            $statuses = [
                                'Active',
                                'Scholarship',
                                'Sponsored',
                                'Inactive',
                                'Graduated',
                                'Transferred'
                            ];
                        @endphp

                        @foreach($statuses as $status)

                            <div class="form-check">

                                <input
                                    class="form-check-input status-filter"
                                    type="checkbox"
                                    value="{{ $status }}"
                                    id="status-{{ Str::slug($status) }}"
                                    checked
                                >

                                <label
                                    class="form-check-label"
                                    for="status-{{ Str::slug($status) }}"
                                >
                                    {{ $status }}
                                </label>

                            </div>

                        @endforeach

                    </div>

                    <div class="mt-2">

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            onclick="selectAllStatuses()"
                        >
                            Select All
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            onclick="clearAllStatuses()"
                        >
                            Clear All
                        </button>

                    </div>

                </div>


                {{-- CARD TITLE --}}
                <div class="col-md-6">

                    <label for="cardTitle" class="form-label fw-bold">
                        Card Title
                    </label>

                    <input
                        type="text"
                        id="cardTitle"
                        class="form-control"
                        value="Be My Hero!"
                        oninput="updateCardTitle()"
                    >

                </div>


                {{-- STATUS SPECIFIC MESSAGE --}}
                <div class="col-md-6">

                    <label for="cardMessage" class="form-label fw-bold">
                        Card Message
                    </label>

                    <textarea
                        id="cardMessage"
                        class="form-control"
                        rows="3"
                        oninput="updateCardMessage()"
                    >Be part of this child's brighter future today!</textarea>

                </div>


                {{-- SELECTED STATUS DISPLAY --}}
                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Current Selection
                    </label>

                    <div
                        id="selectedStatusDisplay"
                        class="alert alert-info mb-0"
                    >
                        All statuses selected
                    </div>

                </div>

            </div>

        </div>
    </div>


    {{-- =========================================================
        CHILD CARDS
    ========================================================== --}}

    <div
        class="d-flex flex-wrap justify-content-center gap-3"
        id="cardsContainer"
    >

        @foreach($children as $child)

            @php

                $sponsorUrl = route(
                    'sponsorship_card',
                    $child->encoded_id
                );

                $hobbies = $child->details
                    ->firstWhere('key', 'hobbies')
                    ->value ?? '';

                $aspirations = $child->details
                    ->firstWhere('key', 'aspirations')
                    ->value ?? '';

                $case_history = $child->details
                    ->firstWhere('key', 'case_history')
                    ->value ?? '';

                $current_grade = $child->details
                    ->firstWhere('key', 'current_grade')
                    ->value ?? '';

            @endphp


            <div
                class="child-card-container"
                data-status="{{ $child->status }}"
                id="child-container-{{ $child->id }}"
            >

                <div class="child-card" id="child-card-{{ $child->id }}">

                    {{-- TITLE --}}
                    <h4
                        class="card-title text-success text-center mt-1 mb-1 fw-bold"
                    >
                        Be My Hero!
                    </h4>


                    {{-- CHILD IMAGE --}}
                    <div class="child-image-wrapper">

                        <img
                            src="{{ asset($child->img_url) }}"
                            alt="{{ $child->name }}"
                        >

                    </div>


                    {{-- CONTENT --}}
                    <div
                        class="content-wrapper flex-grow-1 d-flex flex-column justify-content-center"
                    >

                        {{-- QR + DETAILS --}}
                        <div class="qr-details-wrapper mb-1">

                            <div class="qr-section">

                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(90)->generate($sponsorUrl) !!}

                            </div>


                            <div class="details">

                                <h5>
                                    {{ $child->name }}
                                </h5>

                                <p>
                                    <strong>D.O.B:</strong>
                                    {{ $child->dob ?? 'N/A' }}
                                </p>

                                <p>
                                    <strong>Education:</strong>
                                    {{ $current_grade ?: 'N/A' }}
                                </p>

                                <p>
                                    <strong>Hobbies:</strong>
                                    {{ $hobbies ? ucwords(strtolower($hobbies)) : 'N/A' }}
                                </p>

                                <p>
                                    <strong>Aspiration:</strong>
                                    {{ $aspirations ? ucwords(strtolower($aspirations)) : 'N/A' }}
                                </p>

                            </div>

                        </div>


                        {{-- CASE HISTORY --}}
                        <div class="case-history text-center mx-2">

                            @if(trim($case_history) !== '') <p> {{ $case_history }} </p> @endif

                            <p class="card-message">

                                Be part of
                                <b>{{ $child->name }}</b>'s
                                brighter future today!

                            </p>

                        </div>

                        <hr class="my-1">

                    </div>


                    {{-- FOOTER --}}
                    <div class="footer-wrapper">

                        <div class="footer-content">

                            {{-- LOGO --}}
                            <div class="footer-logo-wrapper">

                                <img
                                    src="{{ asset('assets/img/logo.png') }}"
                                    alt="Teule Logo"
                                    class="footer-logo"
                                >

                            </div>


                            {{-- CONTACT --}}
                            <div class="footer-contact">

                                <p class="mb-0">
                                    www.teulekenya.org
                                </p>

                                <p class="mb-0">
                                    +254 721 582323
                                    <br>
                                    teuleusa@teulekenya.org
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- DOWNLOAD --}}
                    <div class="download-btn-wrapper no-print">

                        <button
                            class="btn btn-success btn-sm"
                            onclick="downloadCard({{ $child->id }})"
                        >
                            Download
                        </button>

                    </div>

                </div>

            </div>

        @endforeach

    </div>


    {{-- NO RESULTS MESSAGE --}}
    <div
        id="noChildrenMessage"
        class="alert alert-warning text-center mt-4 no-print"
        style="display:none;"
    >
        No children match the selected status.
    </div>

</div>



<style>

/* =========================================================
   FILTER PANEL
========================================================= */

.status-filter {
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
}


/* =========================================================
   CARD
========================================================= */

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


/* =========================================================
   OVERLAY
========================================================= */

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


/* =========================================================
   CHILD IMAGE
========================================================= */

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


/* =========================================================
   CONTENT
========================================================= */

.content-wrapper {

    display: flex;

    flex-direction: column;

    justify-content: center;

    flex-grow: 1;

}


/* =========================================================
   QR + DETAILS
========================================================= */

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


/* =========================================================
   DETAILS
========================================================= */

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


/* =========================================================
   CASE HISTORY
========================================================= */

.case-history {

    font-size: 12px;

    margin-bottom: 0.1in;

}


/* =========================================================
   FOOTER
========================================================= */

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


/* =========================================================
   DOWNLOAD BUTTON
========================================================= */

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


/* =========================================================
   PRINT
========================================================= */

@media print {

    body {
        margin: 0;
    }

    .child-card {

        box-shadow: none;

        page-break-inside: avoid;

    }

    .no-print {

        display: none !important;

    }

}

</style>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


<script>

/* =========================================================
   CARD TITLE
========================================================= */

function updateCardTitle() {

    const title = document.getElementById('cardTitle').value;

    document.querySelectorAll('.card-title').forEach(function(element) {

        element.textContent = title || 'Be My Hero!';

    });

}


/* =========================================================
   CARD MESSAGE
========================================================= */

function updateCardMessage() {

    const message =
        document.getElementById('cardMessage').value;

    document.querySelectorAll('.card-message').forEach(function(element) {

        const childName =
            element.querySelector('b')?.textContent;

        if (childName) {

            element.innerHTML =
                message.replace(
                    '{name}',
                    '<b>' + childName + '</b>'
                );

        } else {

            element.textContent = message;

        }

    });

}


/* =========================================================
   GET SELECTED STATUSES
========================================================= */

function getSelectedStatuses() {

    const checkboxes =
        document.querySelectorAll('.status-filter:checked');

    return Array.from(checkboxes).map(function(checkbox) {

        return checkbox.value;

    });

}


/* =========================================================
   FILTER CARDS
========================================================= */

function filterCards() {

    const selectedStatuses =
        getSelectedStatuses();

    const cards =
        document.querySelectorAll('.child-card-container');

    let visibleCount = 0;


    cards.forEach(function(card) {

        const status =
            card.dataset.status;

        if (selectedStatuses.includes(status)) {

            card.style.display = 'flex';

            visibleCount++;

        } else {

            card.style.display = 'none';

        }

    });


    /* Update status display */

    const display =
        document.getElementById('selectedStatusDisplay');


    if (selectedStatuses.length === 0) {

        display.innerHTML =
            '<strong>No status selected</strong>';

    }

    else if (
        selectedStatuses.length ===
        document.querySelectorAll('.status-filter').length
    ) {

        display.innerHTML =
            '<strong>All statuses selected</strong>';

    }

    else {

        display.innerHTML =
            '<strong>Showing:</strong> ' +
            selectedStatuses.join(', ');

    }


    /* No results */

    const noChildrenMessage =
        document.getElementById('noChildrenMessage');


    if (visibleCount === 0) {

        noChildrenMessage.style.display = 'block';

    } else {

        noChildrenMessage.style.display = 'none';

    }

}


/* =========================================================
   SELECT ALL
========================================================= */

function selectAllStatuses() {

    document
        .querySelectorAll('.status-filter')
        .forEach(function(checkbox) {

            checkbox.checked = true;

        });

    filterCards();

}


/* =========================================================
   CLEAR ALL
========================================================= */

function clearAllStatuses() {

    document
        .querySelectorAll('.status-filter')
        .forEach(function(checkbox) {

            checkbox.checked = false;

        });

    filterCards();

}


/* =========================================================
   STATUS CHECKBOX EVENTS
========================================================= */

document
    .querySelectorAll('.status-filter')
    .forEach(function(checkbox) {

        checkbox.addEventListener('change', function() {

            filterCards();

        });

    });


/* =========================================================
   DOWNLOAD CARD
========================================================= */

function downloadCard(childId) {

    const card =
        document.getElementById(
            `child-card-${childId}`
        );

    const downloadBtn =
        card.querySelector('.no-print');


    downloadBtn.style.display = 'none';


    html2canvas(card, {

        scale: 3,

        useCORS: true

    }).then(function(canvas) {

        const link =
            document.createElement('a');

        link.href =
            canvas.toDataURL('image/png');

        link.download =
            `child_card_${childId}.png`;

        link.click();


        downloadBtn.style.display = 'block';

    });

}


/* =========================================================
   INITIALIZE
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function() {

        filterCards();

    }
);

</script>

@endsection
```
