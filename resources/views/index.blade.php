@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>


<div class="banner-area">
    <div class="home-slider homeslider-container owl-theme owl-carousel">
       
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="faq-img">
                    <img src="{{ asset('assets/img/banner/coverphoto2024_1.jpg') }}" alt="Testimonial">
                </div>
                <div style="z-index: 50" class="container-fluid">
                    <div class="row align-items-center">
                        <!-- "Partner with us" section - full width on smaller screens -->
                        <div class="col-12 col-lg-12 ">
                            <div class="banner-content" style="margin-bottom: -5px;">
                                <div class="top-text-div">
                                    <p style="padding:20px;" class="text-dark">Our objective is to have our children
                                        develop and become empowered through holistic nurturing.</p>
                                </div>
                                <div class="banner-btn-area">
                                    <a class="common-btn" href="{{route('aboutus_home')}}">Read more</a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="faq-img">
                    <img src="{{ asset('assets/img/banner/children.jpg') }}" style="filter: blur(2px); alt="Testimonial">
                </div>
                <div style="z-index: 50" class="container-fluid">
                    <div class="row align-items-center">
                        <!-- "Partner with us" section - full width on smaller screens -->
                        <div class="col-12 col-lg-6 d-none d-lg-block">
                            <div class="banner-content">
                                <span>Partner with us</span>
                                <h1>To see vulnerable children transformed, living in Christ-centered families.</h1>
                                <p class="text-dark">Our objective is to have our children develop and become empowered
                                    through holistic nurturing.</p>
                                <div class="banner-btn-area">
                                    <a class="common-btn" href="{{route('aboutus_home')}}">Read more</a>
                                </div>
                            </div>
                        </div>
                        <!-- "Impact" section - two side by side on smaller screens -->
                        <div class="col-12 col-lg-6">
                            <div class="container">
                                <div class="section-title">
                                    <h2>The Impact</h2>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-md-6">
                                        <div class="counter yellow">
                                            <div class="counter-icon">
                                                <span class="counter-value">
                                                    {{ $systemDetails->firstWhere('key',
                                                    'HomePage_children_under_direct_care')->value ?? '' }}
                                                </span>
                                            </div>
                                            <div class="counter-content">
                                                <h3>Children under direct care</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div class="counter green">
                                            <div class="counter-icon">
                                                <span class="counter-value">
                                                    {{ $systemDetails->firstWhere('key',
                                                    'HomePage_TLA_population')->value ?? '' }}
                                                </span>
                                            </div>
                                            <div class="counter-content">
                                                <h3>Students in Teule Leadership Academy</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div class="counter green">
                                            <div class="counter-icon">
                                                <span class="counter-value">
                                                    {{ $systemDetails->firstWhere('key', 'HomePage_CBC_Children')->value
                                                    ?? '' }}
                                                </span>
                                            </div>
                                            <div class="counter-content">
                                                <h3>Children under Community-based care</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div class="counter yellow">
                                            <div class="counter-icon">
                                                <span class="counter-value">
                                                    {{ $systemDetails->firstWhere('key', 'HomePage_Alumni')->value ?? ''
                                                    }}+
                                                </span>
                                            </div>
                                            <div class="counter-content">
                                                <h3>Alumni Impacted over the years</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="faq-img">
                    <img src="{{ asset('assets/img/faq-main-3.jpg') }}" alt="Testimonial">
                </div>
                <div style="z-index: 50" class="container-fluid">
                    <div class="row align-items-center">
                        <!-- "Partner with us" section - full width on smaller screens -->
                        <div class="col-12 col-lg-12 ">
                            <div class="banner-content" style="margin-bottom: -5px;">
                                <div class="top-text-div">
                                    <p style="padding:20px;" class="text-dark">
                                        Together we can raise awareness to drive meaningful change and create a better
                                        world for children in need. Follow us on social media and share our content.
                                        #Teulekenya #MiminiMteule #TwendeTeule
                                    </p>
                                </div>
                                <div class="banner-btn-area">
                                    <a class="common-btn" href="{{route('aboutus_home')}}">Read more</a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>






<section class="work-area pt-100 pb-70">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="work-content">
                    <div class="section-title">
                        <span class="sub-title">Our Mission</span>
                        <h2>To demonstrate the love of Jesus Christ through rescue, empowerment, and re-integration of
                            vulnerable children into communities and stable families.</h2>
                    </div>
                    <div class="section-title">
                        <span class="sub-title">Our Vision</span>
                        <h2>To see vulnerable children transformed, living in Christ-centered families.</h2>
                    </div>

                    <div style="font-size: 20px;font-weight:600;" class="section-title">
                        <span class="sub-title">How to support this vision.</span>
                        <hr>
                    </div>
                    <ul>
                        <li>
                            <h3><span>01</span>Spread the word</h3>
                            <p>Together we can raise awareness to drive meaningful change and create a better world for
                                children in need. Follow us on social media and share our content.

                                <br /><span style="color: #efdd13">#Teulekenya #MiminiMteule #TwendeTeule</span>
                            </p>
                        </li>
                        <li>
                            <h3><span>02</span>Become a financial partner.</h3>
                            <p>There are many ways to direct your gift. Monthly partners can send money to our general
                                fund for unrestricted giving, education fund or child sponsorship. Our education fund
                                supports scholarships
                                to Teule Leadership Academy and secondary and post secondary educational expenses for
                                our beneficiaries. Child sponsorship funds are pooled together to support direct care
                                for children in our programs
                                like basic needs, education, healthcare, and psychosocial support. You can also give a
                                one-time gift or support a campaign.
                                <a href="{{route('donate')}}">Click on the link to see all our giving platforms.</a>
                            </p>
                        </li>
                        <li>
                            <h3><span>03</span>Make an order or purchase from our sustainability projects</h3>
                            <p>We run a variety of sustainability projects including a dairy farm, a guest house, farm
                                (selling vegetables and fruits) and the water project.
                                <a class="cust-links" href="javascript:void(0)">Click to order</a>
                            </p>
                        </li>
                        <li>
                            <h3><span>04</span>Volunteer or become an intern.</h3>
                            <p>Teule has short-term and long-term opportunities for local and international volunteers
                                and interns. We are looking for individuals or groups who share our vision and are
                                passionate to serve others.<a href="{{route('apply_intern')}}">Apply here </a></p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="work-img">
                    <img src="assets/img/banner/banner-main3.png" alt="founders image">
                    <img src="assets/img/banner/banner-main1.jpg" alt="teule children and staff">
                </div>
            </div>
        </div>
    </div>
</section>
<x-needs :needItems="$needItems"></x-needs>
<section class="work-area">
<div class="banner-area">
    <div class="partners_slider homeslider-container owl-theme owl-carousel">

        <!-- Partner 1 -->
        <div class="row align-items-center">
            <div class="col-lg-12 text-center">
                <div style="height:150px !important; display:flex; align-items:center; justify-content:center;" class="faq-img">
                    <img src="{{ asset('assets/img/partners/knl.jpg') }}" 
                         alt="Partner 1 Logo" 
                         style="max-height: 80%; max-width: 80%; object-fit: contain;">
                </div>
            </div>
        </div>

        <!-- Partner 2 -->
        <div class="row align-items-center">
            <div class="col-lg-12 text-center">
                <div style="height:150px !important; display:flex; align-items:center; justify-content:center;" class="faq-img">
                    <img src="{{ asset('assets/img/partners/equity.png') }}" 
                         alt="Partner 2 Logo" 
                         style="max-height: 80%; max-width: 80%; object-fit: contain;">
                </div>
            </div>
        </div>
        <!-- Partner 3 -->
        <div class="row align-items-center">
            <div class="col-lg-12 text-center">
                <div style="height:150px !important; display:flex; align-items:center; justify-content:center;" class="faq-img">
                    <img src="{{ asset('assets/img/partners/kcb.png') }}" 
                         alt="Partner 2 Logo" 
                         style="max-height: 80%; max-width: 80%; object-fit: contain;">
                </div>
            </div>
        </div>

        <!-- Partner 4 -->
        <div class="row align-items-center">
            <div class="col-lg-12 text-center">
                <div style="height:150px !important; display:flex; align-items:center; justify-content:center;" class="faq-img">
                    <img src="{{ asset('assets/img/partners/bikers.jpeg') }}" 
                         alt="Partner 2 Logo" 
                         style="max-height: 80%; max-width: 80%; object-fit: contain;">
                </div>
            </div>
        </div>
         <!-- Partner 5 -->
        <div class="row align-items-center">
            <div class="col-lg-12 text-center">
                <div style="height:150px !important; display:flex; align-items:center; justify-content:center;" class="faq-img">
                    <img src="{{ asset('assets/img/partners/linme.png') }}" 
                         alt="Partner 2 Logo" 
                         style="max-height: 80%; max-width: 80%; object-fit: contain;">
                </div>
            </div>
        </div>

        <!-- Add more logos similarly -->
    </div>
</div>

</section>

<section class="event-area pt-100 pb-70">
    <div class="container">
        <div class="section-title">
            <span class="sub-title">Our events</span>
            <h2>Upcoming Events</h2>
        </div>
        <div class="row align-items-center">

            <div class="col-lg-6">
                @foreach($events->take(1) as $event)
                <div class="event-item">
                    <img src="{{asset($event->url)}}" alt="Event">
                    <div class="inner">
                        <div class="events-date">
                            <h4>{{ \Carbon\Carbon::parse($event->date_from)->format('d M-y') }}</h4>
                            <span>to</span>
                            <h4>{{ \Carbon\Carbon::parse($event->date_to)->format('d M-y') }}</h4>
                        </div>
                        <h3>
                            <a href="event-details.html">{{$event->title}}</a>
                            <i data-toggle="popover" data-content="This is a popover content!" title="Popover Title"
                                class="auto-popover fa fa-info-circle text-white more-info"></i>
                        </h3>
                        <ul>
                            <li>
                                <i class="icofont-stopwatch"></i>
                                <span>
                                    {{ \Carbon\Carbon::parse($event->time_from)->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($event->time_to)->format('H:i') }}</span>
                            </li>
                            <li>
                                <i class="icofont-location-pin"></i>
                                <span>{{$event->location}}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="col-lg-6">
                @foreach($events->slice(1) as $event)
                <div class="event-item-right">
                    <div class="events-date">
                        <h4>{{ \Carbon\Carbon::parse($event->date_from)->format('d M') }}</h4>
                        <span>to</span>
                        <h4>{{ \Carbon\Carbon::parse($event->date_to)->format('d M') }}</h4>
                    </div>

                    <h3>
                        <a href="event-details.html">
                            {{$event->title}}

                        </a>
                    </h3>
                    <ul>
                        <li>
                            <i class="icofont-stopwatch"></i>
                            <span>{{ \Carbon\Carbon::parse($event->time_from)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($event->time_to)->format('H:i') }}</span>
                        </li>
                        <li>
                            <i class="icofont-location-pin"></i>
                            <span>{{$event->location}}</span>
                        </li>
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<x-footer></x-footer>
@endsection