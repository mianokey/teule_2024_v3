
@extends('layouts.app')

@section('content')

<x-header></x-header>
<x-navbar></x-navbar>

<style>
    :root {
        --gt-green: #4bbf24;
        --gt-yellow: #efdd13;
        --gt-navy: #00096A;
        --gt-cream: #f7f8f3;
        --gt-dark: #151827;
    }

    .gt-page {
        overflow: hidden;
        color: var(--gt-dark);
    }

    .gt-page * {
        box-sizing: border-box;
    }

    .gt-page a {
        text-decoration: none;
    }

    .gt-eyebrow {
        display: block;
        color: var(--gt-green);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 18px;
    }

    .gt-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 15px 28px;
        border-radius: 5px;
        background: var(--gt-green);
        color: #fff !important;
        font-weight: 900;
        letter-spacing: .4px;
        border: 2px solid var(--gt-green);
        transition: .3s ease;
        cursor: pointer;
    }

    .gt-btn:hover {
        background: #399c19;
        border-color: #399c19;
        color: #fff !important;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(75,191,36,.25);
    }

    .gt-btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 14px 25px;
        border: 2px solid #fff;
        color: #fff !important;
        font-weight: 800;
        border-radius: 5px;
        transition: .3s ease;
    }

    .gt-btn-outline:hover {
        background: #fff;
        color: var(--gt-navy) !important;
    }

    .gt-share-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 14px 24px;
        border: 2px solid var(--gt-green);
        border-radius: 5px;
        background: transparent;
        color: var(--gt-navy);
        font-weight: 900;
        cursor: pointer;
        transition: .3s ease;
    }

    .gt-share-button:hover {
        background: var(--gt-green);
        color: #fff;
    }

    .gt-section {
        padding: 95px 0;
    }

    .gt-section-title {
        color: var(--gt-navy);
        font-size: clamp(32px, 4vw, 52px);
        line-height: 1.12;
        font-weight: 950;
        margin-bottom: 22px;
    }

    .gt-lead {
        color: #626575;
        font-size: 17px;
        line-height: 1.85;
    }

    /* HERO VIDEO */

    .gt-hero {
        position: relative;
        min-height: 760px;
        display: flex;
        align-items: center;
        overflow: hidden;
        background: var(--gt-navy);
        color: #fff;
    }

    .gt-hero-video {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .gt-hero-overlay {
        position: absolute;
        inset: 0;
        z-index: 1;
        background: linear-gradient(
            90deg,
            rgba(0, 9, 106, .94) 0%,
            rgba(0, 9, 106, .78) 48%,
            rgba(0, 9, 106, .38) 100%
        );
    }

    .gt-hero .container {
        position: relative;
        z-index: 2;
    }

    .gt-hero-content {
        max-width: 780px;
        padding: 75px 0;
    }

    .gt-hero .gt-eyebrow {
        color: var(--gt-yellow);
    }

    .gt-hero h1 {
        color: #fff;
        font-size: clamp(44px, 6.5vw, 78px);
        line-height: 1.02;
        font-weight: 950;
        margin: 0 0 25px;
    }

    .gt-hero h1 span {
        color: var(--gt-yellow);
    }

    .gt-hero-content > p {
        max-width: 650px;
        color: rgba(255,255,255,.9);
        font-size: 19px;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    .gt-goal {
        margin-bottom: 30px;
    }

    .gt-goal strong {
        display: block;
        color: var(--gt-yellow);
        font-size: 48px;
        font-weight: 950;
        line-height: 1.1;
    }

    .gt-goal span {
        display: block;
        color: rgba(255,255,255,.8);
        font-size: 12px;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-top: 8px;
    }

    .gt-hero-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 13px;
    }

    .gt-scroll-note {
        position: absolute;
        bottom: 25px;
        left: 50%;
        transform: translateX(-50%);
        color: rgba(255,255,255,.8);
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        z-index: 2;
        white-space: nowrap;
        animation: gtBounce 2s infinite;
    }

    @keyframes gtBounce {
        0%, 100% { transform: translate(-50%, 0); }
        50% { transform: translate(-50%, -8px); }
    }

    /* STORY */

    .gt-intro {
        background: var(--gt-cream);
    }

    .gt-intro-image {
        width: 100%;
        height: 490px;
        object-fit: cover;
        border-radius: 7px;
        box-shadow: 18px 18px 0 var(--gt-green);
    }

    .gt-intro-copy {
        padding: 20px 0 20px 35px;
    }

    .gt-quote {
        border-left: 5px solid var(--gt-green);
        padding: 12px 0 12px 22px;
        color: var(--gt-navy);
        font-size: 22px;
        line-height: 1.5;
        font-weight: 900;
        margin-top: 28px;
    }

    /* STORY STRIP */

    .gt-story-strip {
        position: relative;
        background: var(--gt-navy);
        color: #fff;
        padding: 75px 0;
    }

    .gt-story-strip h2 {
        color: #fff;
        font-size: clamp(32px, 4vw, 48px);
        line-height: 1.15;
        font-weight: 950;
        margin-bottom: 15px;
    }

    .gt-story-strip p {
        color: rgba(255,255,255,.82);
        font-size: 17px;
        line-height: 1.85;
        margin: 0;
    }

    .gt-story-strip .gt-eyebrow {
        color: var(--gt-yellow);
    }

    /* SUPPORT CARDS */

    .gt-support {
        background: #fff;
    }

    .gt-support-card {
        height: 100%;
        background: var(--gt-cream);
        border-radius: 7px;
        overflow: hidden;
        transition: .35s ease;
    }

    .gt-support-card:hover {
        transform: translateY(-9px);
        box-shadow: 0 18px 35px rgba(0,0,0,.1);
    }

    .gt-support-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
    }

    .gt-support-card-content {
        padding: 28px;
        border-bottom: 4px solid var(--gt-green);
    }

    .gt-support-card h3 {
        color: var(--gt-navy);
        font-size: 23px;
        font-weight: 950;
        margin-bottom: 13px;
    }

    .gt-support-card p {
        color: #666979;
        line-height: 1.8;
        margin: 0;
    }

    /* VIDEO FEATURE */

    .gt-film {
        background: var(--gt-dark);
        color: #fff;
        text-align: center;
    }

    .gt-film h2 {
        color: #fff;
    }

    .gt-film > .container > p {
        max-width: 720px;
        margin: 0 auto 30px;
        color: rgba(255,255,255,.76);
        font-size: 17px;
        line-height: 1.8;
    }

    .gt-film-image {
        position: relative;
        max-width: 950px;
        margin: 35px auto 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .gt-film-image img {
        width: 100%;
        max-height: 490px;
        object-fit: cover;
        display: block;
        filter: brightness(.65);
    }

    .gt-film-caption {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 25px;
    }

    .gt-play-icon {
        width: 78px;
        height: 78px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid var(--gt-yellow);
        color: var(--gt-yellow);
        font-size: 27px;
        margin-bottom: 17px;
    }

    .gt-film-caption strong {
        font-size: 20px;
        color: #fff;
    }

    /* 2027 FUNDRAISING */

    .gt-giving {
        background: var(--gt-cream);
    }

    .gt-giving-panel {
        background: #fff;
        padding: 42px;
        border-radius: 8px;
        border-top: 7px solid var(--gt-green);
        box-shadow: 0 12px 35px rgba(0,0,0,.06);
    }

    .gt-giving-panel h3 {
        color: var(--gt-navy);
        font-size: 28px;
        font-weight: 950;
        margin-bottom: 15px;
    }

    .gt-giving-panel p {
        color: #666979;
        line-height: 1.8;
    }

    .gt-giving-list {
        list-style: none;
        padding: 0;
        margin: 25px 0;
    }

    .gt-giving-list li {
        padding: 14px 0;
        border-bottom: 1px solid #ececf0;
        color: #343746;
        font-weight: 750;
    }

    .gt-giving-list li span {
        display: inline-block;
        color: #399c19;
        margin-right: 12px;
        font-weight: 950;
    }

    /* QR CODE */

    .gt-qr-section {
        padding: 85px 0;
        background: #fff;
        text-align: center;
    }

    .gt-qr-card {
        max-width: 780px;
        margin: auto;
        padding: 45px 35px;
        border-radius: 10px;
        background: var(--gt-cream);
        border: 1px solid #e9eddf;
        border-bottom: 7px solid var(--gt-green);
    }

    .gt-qr-image {
        display: block;
        width: 220px;
        max-width: 100%;
        padding: 12px;
        background: #fff;
        border-radius: 8px;
        margin: 25px auto;
        box-shadow: 0 8px 25px rgba(0,0,0,.07);
    }

    .gt-qr-actions {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 25px;
    }

    /* FINAL APPEAL */

    .gt-final {
        position: relative;
        padding: 110px 0;
        text-align: center;
        color: #fff;
        background:
            linear-gradient(rgba(0,9,106,.92), rgba(0,9,106,.92)),
            url("{{ asset('assets/img/banner/children.jpg') }}")
            center/cover no-repeat;
    }

    .gt-final h2 {
        color: #fff;
        max-width: 880px;
        margin: 0 auto 25px;
        font-size: clamp(36px, 5vw, 60px);
        line-height: 1.1;
        font-weight: 950;
    }

    .gt-final p {
        max-width: 700px;
        margin: 0 auto 30px;
        color: rgba(255,255,255,.85);
        font-size: 18px;
        line-height: 1.85;
    }

    .gt-final .gt-eyebrow {
        color: var(--gt-yellow);
    }

    .gt-hashtag {
        margin-top: 30px;
        color: var(--gt-yellow);
        font-weight: 900;
        letter-spacing: 1px;
    }

    /* MOBILE GIVE BUTTON */

    .gt-mobile-give {
        display: none;
    }

    /* ANIMATIONS */

    .gt-reveal {
        opacity: 1;
        transform: none;
    }

    html.gt-js .gt-reveal {
        opacity: 0;
        transform: translateY(35px);
        transition: opacity .8s ease, transform .8s ease;
    }

    html.gt-js .gt-reveal.gt-visible {
        opacity: 1;
        transform: translateY(0);
    }

    .gt-reveal-delay-1 {
        transition-delay: .15s !important;
    }

    .gt-reveal-delay-2 {
        transition-delay: .3s !important;
    }

    .gt-float {
        animation: gtFloat 5s ease-in-out infinite;
    }

    @keyframes gtFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    @media (max-width: 991px) {
        .gt-hero {
            min-height: 680px;
        }

        .gt-intro-copy {
            padding: 45px 0 0;
        }

        .gt-section {
            padding: 75px 0;
        }
    }

    @media (max-width: 767px) {
        .gt-hero {
            min-height: 700px;
        }

        .gt-hero-overlay {
            background: rgba(0,9,106,.78);
        }

        .gt-hero-content {
            padding: 65px 0 80px;
        }

        .gt-hero h1 {
            font-size: 46px;
        }

        .gt-hero-content > p {
            font-size: 16px;
        }

        .gt-goal strong {
            font-size: 39px;
        }

        .gt-hero-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .gt-hero-actions > * {
            width: 100%;
            text-align: center;
        }

        .gt-scroll-note {
            display: none;
        }

        .gt-intro-image {
            height: 330px;
            box-shadow: 10px 10px 0 var(--gt-green);
        }

        .gt-section {
            padding: 65px 0;
        }

        .gt-giving-panel {
            padding: 27px 21px;
        }

        .gt-qr-card {
            padding: 30px 18px;
        }

        .gt-qr-actions {
            flex-direction: column;
        }

        .gt-qr-actions > * {
            width: 100%;
        }

        .gt-mobile-give {
            display: flex;
            position: fixed;
            z-index: 9999;
            bottom: 12px;
            left: 12px;
            right: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,.25);
        }

        body {
            padding-bottom: 65px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .gt-reveal,
        .gt-float,
        .gt-scroll-note,
        .gt-btn {
            animation: none !important;
            transition: none !important;
        }
    }
</style>


<div class="gt-page">

    {{-- ==========================================
         HERO WITH VIDEO BACKGROUND
    =========================================== --}}

    <section class="gt-hero">

<video
    class="gt-hero-video"
    autoplay
    muted
    loop
    playsinline
    webkit-playsinline
    preload="auto"
    poster="{{ asset('assets/img/banner/coverphoto2024_1.jpg') }}"
>
    <source
        src="{{ asset('assets/videos/web_loop.mp4') }}"
        type="video/mp4">

    Your browser does not support HTML5 video.
</video>


        <div class="gt-hero-overlay"></div>

        <div class="container">

            <div class="gt-hero-content gt-reveal">

                <span class="gt-eyebrow">
                    Giving Tuesday 2026 · Teule Kenya
                </span>

                <h1>
                    Every child deserves a <span>future.</span>
                </h1>

                <p>
                    Behind every child is a story. A story that can be changed
                    through love, education, care and the opportunity to thrive.
                    This Giving Tuesday, be part of what comes next.
                </p>

                <div class="gt-goal">
                    <strong>$20,000</strong>
                    <span>Our Giving Tuesday fundraising goal</span>
                </div>

                <div class="gt-hero-actions">

                    <a href="{{ route('donate') }}" class="gt-btn">
                        GIVE NOW <span>→</span>
                    </a>

                    <a href="#our-story" class="gt-btn-outline">
                        Discover our story
                    </a>

                    <button type="button"
                        class="gt-share-button"
                        data-gt-share
                        style="border-color:#fff;color:#fff;">
                        SHARE CAMPAIGN ↗
                    </button>

                </div>

            </div>

        </div>

        <div class="gt-scroll-note">
            Scroll to discover the story ↓
        </div>

    </section>


    {{-- ==========================================
         OUR STORY
    =========================================== --}}

    <section class="gt-section gt-intro" id="our-story">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-6 gt-reveal">

                    <img
                        src="{{ asset('assets/img/banner/giving_tuesday_01.jpg') }}"
                        alt="Children at Teule Kenya"
                        class="gt-intro-image"
                        loading="lazy">

                </div>

                <div class="col-lg-6">

                    <div class="gt-intro-copy gt-reveal">

                        <span class="gt-eyebrow">
                            More than a donation
                        </span>

                        <h2 class="gt-section-title">
                            A chance to learn. A place to belong.
                            A future to believe in.
                        </h2>

                        <p class="gt-lead">
                            At Teule Kenya, we believe every child deserves
                            to experience love, discover their potential and
                            grow in a safe and nurturing environment.
                        </p>

                        <p class="gt-lead">
                            Through rescue, education, family empowerment
                            and reintegration, we walk alongside vulnerable
                            children as they build brighter futures.
                        </p>

                        <div class="gt-quote">
                            We are not just changing today. We are helping
                            shape the generations of tomorrow.
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ==========================================
         EMOTIONAL STORY STRIP
    =========================================== --}}

    <section class="gt-story-strip">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-7 mb-4 mb-lg-0 gt-reveal">

                    <span class="gt-eyebrow">
                        The heart of Teule
                    </span>

                    <h2>
                        Every child is more than their circumstances.
                    </h2>

                </div>

                <div class="col-lg-5 gt-reveal gt-reveal-delay-1">

                    <p>
                        A child needs more than a roof over their head.
                        They need someone who believes in them, an opportunity
                        to learn, and a community that gives them room to grow.
                        That is the work we invite you to be part of.
                    </p>

                </div>

            </div>

        </div>

    </section>


    {{-- ==========================================
         WHAT YOUR SUPPORT MAKES POSSIBLE
    =========================================== --}}

    <section class="gt-section gt-support" id="our-impact">

        <div class="container">

            <div class="text-center mb-5 gt-reveal">

                <span class="gt-eyebrow">
                    Your generosity in action
                </span>

                <h2 class="gt-section-title">
                    Help make the next chapter possible.
                </h2>

                <p class="gt-lead mx-auto" style="max-width:720px;">
                    Your Giving Tuesday contribution will support the
                    education and programmes planned for 2027.
                </p>

            </div>

            <div class="row">

                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="gt-support-card gt-reveal">

                        <img
                            src="{{ asset('assets/img/graduation.jpg') }}"
                            alt="Education and learning"
                            loading="lazy">

                        <div class="gt-support-card-content">

                            <h3>Education</h3>

                            <p>
                                Help children access quality learning,
                                educational opportunities and the support
                                they need to pursue their dreams.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="gt-support-card gt-reveal gt-reveal-delay-1">

                        <img
                            src="{{ asset('assets/img/care.jpg') }}"
                            alt="Care and support for children"
                            loading="lazy">

                        <div class="gt-support-card-content">

                            <h3>Care and empowerment</h3>

                            <p>
                                Support the programmes that nurture children,
                                strengthen families and help vulnerable
                                communities move forward.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="gt-support-card gt-reveal gt-reveal-delay-2">

                        <img
                            src="{{ asset('assets/img/future.jpg') }}"
                            alt="Children and staff at Teule Kenya"
                            loading="lazy">

                        <div class="gt-support-card-content">

                            <h3>Hope for the future</h3>

                            <p>
                                Be part of a long-term commitment to helping
                                children grow, discover their abilities and
                                build meaningful futures.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ==========================================
         CAMPAIGN VIDEO / STORY FEATURE
    =========================================== --}}

    <section class="gt-section gt-film" id="watch">

        <div class="container">

            <div class="gt-reveal">

                <span class="gt-eyebrow" style="color:var(--gt-yellow);">
                    See the work. Feel the purpose.
                </span>

                <h2 class="gt-section-title">
                    This is what hope looks like.
                </h2>

                <p>
                    The most powerful way to understand the work is to see
                    the people, the moments and the stories behind it.
                </p>

            </div>

            <div class="gt-film-image gt-reveal">

                <img
                    src="{{ asset('assets/img/banner/coverphoto2024_1.jpg') }}"
                    alt="Teule Kenya campaign story"
                    loading="lazy">

                <div class="gt-film-caption">

                    <div class="gt-play-icon">
                        ▶
                    </div>

                    <strong>
                        Our story. Our children. Our shared future.
                    </strong>

                </div>

            </div>

            {{-- Replace this image feature with a video embed
                 when the campaign film is ready. --}}

        </div>

    </section>


    {{-- ==========================================
         THE 2027 FUNDRAISING GOAL
    =========================================== --}}

    <section class="gt-section gt-giving" id="2027">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-5 mb-4 mb-lg-0 gt-reveal">

                    <span class="gt-eyebrow">
                        Looking ahead
                    </span>

                    <h2 class="gt-section-title">
                        Help us prepare for 2027.
                    </h2>

                    <p class="gt-lead">
                        We are raising $20,000 to strengthen the education
                        and programmes that will continue serving children
                        and families in the coming year.
                    </p>

                    <p class="gt-lead">
                        Your gift is an investment in opportunity, dignity
                        and lasting transformation.
                    </p>

                    <a href="{{ route('donate') }}" class="gt-btn mt-3">
                        SUPPORT THE CAMPAIGN →
                    </a>

                </div>


                <div class="col-lg-7">

                    <div class="gt-giving-panel gt-reveal gt-reveal-delay-1">

                        <h3>
                            What are we raising funds for?
                        </h3>

                        <p>
                            The campaign will contribute towards Teule Kenya's
                            2027 education and programme budget, helping us
                            continue the work entrusted to us.
                        </p>

                        <ul class="gt-giving-list">

                            <li>
                                <span>01</span>
                                Education and learning opportunities
                            </li>

                            <li>
                                <span>02</span>
                                Child care and development programmes
                            </li>

                            <li>
                                <span>03</span>
                                Family and community empowerment
                            </li>

                            <li>
                                <span>04</span>
                                Programmes that nurture long-term growth
                            </li>

                        </ul>

                        <div class="d-flex align-items-center justify-content-between flex-wrap">

                            <div>
                                <small class="text-muted d-block">
                                    Campaign target
                                </small>

                                <strong style="font-size:36px;color:var(--gt-navy);">
                                    $20,000
                                </strong>
                            </div>

                            <a href="{{ route('donate') }}" class="gt-btn mt-3 mt-sm-0">
                                GIVE TODAY →
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ==========================================
         QR CODE AND CAMPAIGN SHARING
    =========================================== --}}

    <section class="gt-qr-section" id="share-campaign">

        <div class="container">

            <div class="gt-qr-card gt-reveal">

                <span class="gt-eyebrow">
                    Spread the hope
                </span>

                <h2 class="gt-section-title">
                    One share can reach someone who can make a difference.
                </h2>

                <p class="gt-lead mx-auto" style="max-width:620px;">
                    Share the Giving Tuesday campaign with your friends,
                    family, church, colleagues and networks. Every share
                    helps the message travel further.
                </p>

                <div class="gt-float">

                    <img
                        src="{{ route('giving-tuesday.qr') }}"
                        alt="Teule Kenya Giving Tuesday QR code"
                        class="gt-qr-image"
                        loading="lazy">

                </div>

                <p style="font-weight:900;color:var(--gt-navy);">
                    Scan to visit the campaign page.
                </p>

                <div class="gt-qr-actions">

                    <a
                        href="{{ route('giving-tuesday.qr', ['download' => 1]) }}"
                        class="gt-btn">

                        DOWNLOAD QR CODE ↓

                    </a>

                    <button
                        type="button"
                        class="gt-share-button"
                        data-gt-share>

                        SHARE THIS PAGE ↗

                    </button>

                </div>

                <p class="mt-4 mb-0" style="font-size:13px;color:#777;">
                    #GivingTuesday #TeuleKenya #MiminiMteule
                </p>

            </div>

        </div>

    </section>


    {{-- ==========================================
         FINAL CAMPAIGN APPEAL
    =========================================== --}}

    <section class="gt-final">

        <div class="container gt-reveal">

            <span class="gt-eyebrow">
                Giving Tuesday 2026
            </span>

            <h2>
                The future of a child can change because you chose to care.
            </h2>

            <p>
                You may never know every child whose life your generosity
                touches. But your decision to give can help create
                opportunities that last long after today.
            </p>

            <a href="{{ route('donate') }}" class="gt-btn">
                MAKE YOUR GIFT NOW →
            </a>

            <div class="gt-hashtag">
                #GivingTuesday &nbsp; #TeuleKenya &nbsp; #MiminiMteule
            </div>

        </div>

    </section>

</div>


{{-- MOBILE STICKY DONATION BUTTON --}}

<a href="{{ route('donate') }}" class="gt-btn gt-mobile-give">
    GIVE NOW — SUPPORT A CHILD
</a>


{{-- ==========================================
     ANIMATIONS AND SHARING
=========================================== --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const page = document.querySelector('.gt-page');

    if (!page) {
        return;
    }

    // Reveal elements as visitors scroll down.
    const revealElements = page.querySelectorAll(
        '.gt-reveal, .gt-support-card'
    );

    document.documentElement.classList.add('gt-js');

    if ('IntersectionObserver' in window) {

        const revealObserver = new IntersectionObserver(
            function (entries, observer) {

                entries.forEach(function (entry) {

                    if (entry.isIntersecting) {

                        entry.target.classList.add('gt-visible');

                        observer.unobserve(entry.target);
                    }

                });

            },
            {
                threshold: 0.12,
                rootMargin: '0px 0px -35px 0px'
            }
        );

        revealElements.forEach(function (element) {
            revealObserver.observe(element);
        });

    } else {

        revealElements.forEach(function (element) {
            element.classList.add('gt-visible');
        });

    }


    // Native page sharing with clipboard fallback.
    const shareButtons = page.querySelectorAll('[data-gt-share]');

    shareButtons.forEach(function (button) {

        button.addEventListener('click', async function () {

            const campaignUrl = @json(route('giving-tuesday'));

            const shareData = {
                title: 'Giving Tuesday 2026 | Teule Kenya',
                text: 'Be part of changing the future of vulnerable children. Support Teule Kenya’s Giving Tuesday campaign.',
                url: campaignUrl
            };

            try {

                if (navigator.share) {

                    await navigator.share(shareData);

                } else if (navigator.clipboard) {

                    await navigator.clipboard.writeText(campaignUrl);

                    alert('Campaign link copied. You can now share it with others.');

                } else {

                    window.prompt(
                        'Copy and share this campaign link:',
                        campaignUrl
                    );

                }

            } catch (error) {

                if (error.name !== 'AbortError') {

                    window.prompt(
                        'Copy and share this campaign link:',
                        campaignUrl
                    );

                }

            }

        });

    });


    // Ensure the hero video attempts to play on supported browsers.
    const heroVideo = page.querySelector('.gt-hero-video');

    if (heroVideo) {

        heroVideo.muted = true;

        const playPromise = heroVideo.play();

        if (playPromise !== undefined) {
            playPromise.catch(function () {
                // Poster image remains visible if autoplay is blocked.
            });
        }

    }

});
</script>


<x-footer></x-footer>

@endsection
