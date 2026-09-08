@php
    $sponsorUrl = route(
        'sponsorship_card',
        $child->encoded_id
    );

    $current_grade = $child->details
        ->firstWhere('key', 'current_grade')
        ->value ?? '';

    $firstName = explode(
        ' ',
        trim($child->name)
    )[0];
@endphp

<style>
    /* =========================================================
       EDUCATION SCHOLARSHIP CARD
       ========================================================= */

    .education-card {
        position: relative;
        width: 5in;
        height: 7in;

        padding: 0.25in 0.3in;

        background:
            linear-gradient(
                145deg,
                #fffdf7 0%,
                #ffffff 45%,
                #fff8ee 100%
            );

        border-radius: 22px;

        border: 3px solid #000096;

        box-shadow:
            0 8px 25px rgba(0, 0, 0, 0.18),
            inset 0 0 0 3px #f5b942,
            inset 0 0 0 7px #ffffff;

        display: flex;
        flex-direction: column;

        overflow: hidden;
        box-sizing: border-box;
    }


    /* =========================================================
       DECORATIVE BORDER
       ========================================================= */

    .education-card::before {
        content: "";

        position: absolute;
        inset: 10px;

        border: 1px solid rgba(0, 0, 150, 0.25);

        border-radius: 16px;

        pointer-events: none;

        z-index: 0;
    }


    /* =========================================================
       TOP DECORATIVE BAND
       ========================================================= */

    .education-card::after {
        content: "";

        position: absolute;

        top: 0;
        left: 0;

        width: 100%;
        height: 9px;

        background:
            linear-gradient(
                90deg,
                #000096 0%,
                #000096 35%,
                #f5b942 35%,
                #f5b942 65%,
                #ef4700 65%,
                #ef4700 100%
            );

        z-index: 2;
    }


    /* =========================================================
       CONTENT LAYER
       ========================================================= */

    .education-card > * {
        position: relative;
        z-index: 3;
    }


    /* =========================================================
       TITLE
       ========================================================= */

    .education-title-wrapper {
        text-align: center;

        margin-top: 0.12in;
        margin-bottom: 0.08in;
    }

    .education-title {
        display: inline-block;

        color: #000096;

        font-size: 21px;
        font-weight: 800;

        letter-spacing: 0.5px;

        margin: 0;

        padding: 7px 20px;

        border: 2px solid #f5b942;

        border-radius: 30px;

        background:
            linear-gradient(
                135deg,
                #ffffff,
                #fff8e8
            );

        box-shadow:
            0 3px 8px rgba(0, 0, 0, 0.08);
    }


    /* =========================================================
       CHILD IMAGE
       ========================================================= */

    .education-image-wrapper {
        width: 1.55in;
        height: 1.55in;

        margin: 0.08in auto 0.07in;

        padding: 5px;

        border-radius: 50%;

        background:
            linear-gradient(
                135deg,
                #000096,
                #f5b942,
                #ef4700
            );

        box-shadow:
            0 5px 12px rgba(0, 0, 0, 0.18);

        flex-shrink: 0;
    }

    .education-image-wrapper-inner {
        width: 100%;
        height: 100%;

        border-radius: 50%;

        padding: 3px;

        background: #ffffff;

        box-sizing: border-box;
    }

    .education-image-wrapper img {
        width: 100%;
        height: 100%;

        object-fit: cover;

        border-radius: 50%;

        display: block;
    }


    /* =========================================================
       CHILD NAME
       ========================================================= */

    .education-child-name {
        text-align: center;

        color: #000096;

        font-size: 20px;
        font-weight: 800;

        margin-top: 3px;

        line-height: 1.15;
    }


    /* =========================================================
       GRADE
       ========================================================= */

    .education-grade {
        display: inline-block;

        margin: 7px auto 10px;

        padding: 5px 16px;

        color: #ffffff;

        background:
            linear-gradient(
                135deg,
                #000096,
                #2525bd
            );

        border-radius: 20px;

        font-size: 12px;
        font-weight: 500;

        box-shadow:
            0 3px 7px rgba(0, 0, 150, 0.2);
    }

    .education-grade strong {
        color: #f5b942;
    }


    /* =========================================================
       MAIN CONTENT
       ========================================================= */

    .education-content {
        display: flex;

        align-items: center;
        justify-content: center;

        gap: 0.22in;

        width: 92%;

        margin: 0 auto;

        padding: 0.13in;

        border-radius: 15px;

        background:
            linear-gradient(
                135deg,
                rgba(0, 0, 150, 0.035),
                rgba(245, 185, 66, 0.10)
            );

        border: 1px solid rgba(0, 0, 150, 0.12);

        box-sizing: border-box;
    }


    /* =========================================================
       QR CODE
       ========================================================= */

    .education-qr {
        flex-shrink: 0;

        text-align: center;

        padding: 7px;

        background: #ffffff;

        border: 2px solid #f5b942;

        border-radius: 12px;

        box-shadow:
            0 3px 9px rgba(0, 0, 0, 0.12);
    }

    .education-qr svg {
        width: 0.85in !important;
        height: 0.85in !important;

        display: block;

        margin: auto;
    }

    .qr-caption {
        color: #000096;

        font-size: 8px;
        font-weight: 700;

        margin-top: 3px;
    }


    /* =========================================================
       MESSAGE
       ========================================================= */

    .education-message {
        flex: 1;

        text-align: left;

        color: #333333;

        font-size: 12px;

        line-height: 1.4;
    }

    .education-message p {
        margin: 0 0 8px;
    }

    .education-message .highlight {
        color: #ef4700;

        font-weight: 800;
    }


    /* =========================================================
       FOOTER
       ========================================================= */

    .footer-wrapper {
        position: absolute;

        bottom: 30px;
        left: 0;

        width: 100%;

        display: flex;

        justify-content: center;
        align-items: center;

        padding: 0 0.25in;

        box-sizing: border-box;
    }

    .footer-content {
        width: 86%;

        display: flex;

        justify-content: center;
        align-items: center;

        gap: 0.18in;

        padding-top: 7px;

        border-top: 2px solid #f5b942;
    }

    .footer-logo-wrapper {
        display: flex;
        align-items: center;
    }

    .footer-logo {
        width: 0.55in;
        height: auto;
    }

    .footer-contact {
        text-align: left;

        color: #333333;

        font-size: 9px;

        font-weight: 600;

        line-height: 1.25;
    }

    .footer-contact p {
        margin: 0;
    }


    /* =========================================================
       INDIVIDUAL DOWNLOAD BUTTON
       ========================================================= */

    .download-btn-wrapper {
        position: absolute;

        bottom: 8px;
        right: 8px;

        z-index: 20;
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

        .education-card {
            box-shadow: none;

            page-break-inside: avoid;
        }

        .no-print {
            display: none !important;
        }
    }
</style>


<div
    class="card child-card education-card"
    id="child-card-{{ $child->id }}"
    data-child-id="{{ $child->id }}"
    data-name="{{ $child->name }}"
    data-first-name="{{ $firstName }}"
    data-current-grade="{{ $current_grade }}"
>


    {{-- TITLE --}}
    <div class="education-title-wrapper">

        <h4 class="education-title">
            INVEST IN MY EDUCATION
        </h4>

    </div>


    {{-- CHILD IMAGE --}}
    <div class="education-image-wrapper">

        <div class="education-image-wrapper-inner">

            <img
                src="{{ asset($child->img_url) }}"
                alt="{{ $child->name }}"
            >

        </div>

    </div>


    {{-- NAME --}}
    <div class="education-child-name">

        {{ $child->name }}

    </div>


    {{-- GRADE --}}
    <div class="text-center">

        <div class="education-grade">

            <strong>Education Level:</strong>
            {{ $current_grade ?: 'N/A' }}

        </div>

    </div>


    {{-- QR + MESSAGE --}}
    <div class="education-content">


        {{-- QR CODE --}}
        <div class="education-qr">

            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate($sponsorUrl) !!}

            <div class="qr-caption">
                Scan to learn more
            </div>

        </div>


        {{-- MESSAGE --}}
        <div class="education-message">

            <p>
                Your support will help me stay in school,
                continue learning, and build a brighter future.
            </p>

            <p class="highlight">
                Be part of my education journey today.
            </p>

        </div>


    </div>


    {{-- FOOTER --}}
    <div class="footer-wrapper">

        <div class="footer-content">

            <div class="footer-logo-wrapper">

                <img
                    src="{{ asset('assets/img/logo.png') }}"
                    alt="Teule Logo"
                    class="footer-logo"
                >

            </div>


            <div class="footer-contact">

                <p>
                    www.teulekenya.org
                </p>

                <p>
                    +254 721 582323
                    <br>
                    teuleusa@teulekenya.org
                </p>

            </div>

        </div>

    </div>


    {{-- INDIVIDUAL DOWNLOAD --}}
    <div class="download-btn-wrapper no-print">

        <button
            type="button"
            class="btn btn-primary btn-sm"
            onclick="downloadEducationCard({{ $child->id }})"
        >

            <i class="fas fa-download me-1"></i>
            Download

        </button>

    </div>

</div>


<script>
    async function downloadEducationCard(childId)
    {
        const card =
            document.getElementById(
                'child-card-' + childId
            );

        if (!card) {
            alert('Card not found.');
            return;
        }

        const canvas =
            await html2canvas(card, {
                scale: 3,
                useCORS: true,
                allowTaint: false,
                backgroundColor: null,
                logging: false,
                imageTimeout: 15000
            });

        const image =
            canvas.toDataURL('image/png');

        const link =
            document.createElement('a');

        link.href = image;

        link.download =
            'education_card_' +
            childId +
            '.png';

        document.body.appendChild(link);

        link.click();

        link.remove();
    }
</script>