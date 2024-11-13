@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="banner-area">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="banner-content">
                            <span>Partner with us</span>
                            <h1>To see vulnerable children transformed, living in Christ-centered families.</h1>
                            <p class="text-dark">our objective is to have our children develop and become empowered through holistic nurturing.</p>
                            <div class="banner-btn-area">
                                <a class="common-btn" href="{{route('aboutus_home')}}">Read more</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="container">
                            <div class="section-title">
                                <h2>The Impact</h2>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="counter yellow">
                                        <div class="counter-icon">
                                            <span class="counter-value">
                                                {{ $systemDetails->firstWhere('key', 'HomePage_children_under_direct_care')->value ?? '' }}
                                            </span>
                                        </div>
                                        <div class="counter-content">
                                            <h3>Children under direct care</h3>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="counter green">
                                        <div class="counter-icon">
                                            <span class="counter-value">
                                                  {{ $systemDetails->firstWhere('key', 'HomePage_TLA_population')->value ?? '' }}
                                            </span>
                                        </div>
                                        <div class="counter-content">
                                            <h3>Students in Teule Leadership Academy</h3>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="counter green">
                                        <div class="counter-icon">
                                            <span class="counter-value">
                                                {{ $systemDetails->firstWhere('key', 'HomePage_CBC_Children')->value ?? '' }}
                                            </span>
                                        </div>
                                        <div class="counter-content">
                                            <h3>Children under Community based care</h3>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="counter yellow">
                                        <div class="counter-icon">
                                            <span class="counter-value">
                                                 {{ $systemDetails->firstWhere('key', 'HomePage_Alumni')->value ?? '' }}+
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
    <section>

    </section>
</div>

<div class="about-area pt-100 pb-70">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-img">
                    <img src="{{asset('assets/img/about/anniversary.jpg')}}" alt="About">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content">
                    <div class="section-title">
                        <span class="sub-title">About us</span>
                        <h2>What we do</h2>
                    </div>
                    <span class="about-span">Teule Kenya has four core activities in which to meet its mandate.</span>
                    <p>These activities are providing holistic child care, quality education, family empowerment, and spiritual discipleship to raise a generation of leaders who will serve their communities and the world. Our core activities are closely anchored on the spirit of Sustainable Development Goals, in addressing the root causes of poverty and hunger. Additionally, these activities increase access to clean water, quality education, gender equality for orphaned and vulnerable children, and their families.
                    </p>
                    
                    <div class="about-btn-area">
                        <a class="common-btn about-btn" href="{{route('donate')}}">Support the Course</a>
                        <!-- resources/views/your_view.blade.php -->
                        <a class="common-btn" href="{{ route('readmore', ['documentName' => 'strategic_plan.pdf']) }}">Read Our Strategic Plan</a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="feature-area pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-lg-4">
                <div class="feature-item">
                    <i class="flaticon-solidarity"></i>
                    <h3>
                        <a href="{{route('volunteer')}}">Become a volunteer</a>
                    </h3>
                    <p>We are open to volunteers to come and support the existing work force at Teule Kenya.</p>
                    <a class="feature-btn" href="{{route('volunteer')}}">Apply Now</a>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="feature-item two">
                    <i class="flaticon-donation"></i>
                    <h3>
                        <a href="{{route('donate')}}">Donate/Sponsor a Child now</a>
                    </h3>
                    <p>Join the pool of other donors/sponsors who have over the years supported the Teule Children</p>
                    <a class="feature-btn" href="{{route('donate')}}">Donate</a>
                </div>
            </div>
            <div class="col-sm-6 offset-sm-3 offset-lg-0 col-lg-4">
                <div class="feature-item three">
                    <i class="flaticon-love"></i>
                    <h3>
                        <a href="{{route('apply_intern')}}">Become an Intern</a>
                    </h3>
                    <p>Apply today for internship opportunities here at Teule Kenya</p>
                    <a class="feature-btn" href="{{route('apply_intern')}}">Apply Now</a>
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
                        <h2>To demonstrate the love of Jesus Christ through rescue, empowerment, and re-integration of vulnerable children into communities and stable families.</h2>
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
                            <p>Together we can raise awareness to drive meaningful change and create a better world for children in need. Follow us on social media and share our content. 
                                 
                                 <br /><span style="color: #efdd13">#Teulekenya #MiminiMteule #TwendeTeule</span>
                            </p>
                        </li>
                        <li>
                            <h3><span>02</span>Become a financial partner.</h3>
                            <p>There are many ways to direct your gift. Monthly partners can send money to our general fund for unrestricted giving, education fund or child sponsorship. Our education fund supports scholarships
                            to Teule Leadership Academy and secondary and post secondary educational expenses for our beneficiaries. Child sponsorship funds are pooled together to support direct care for children in our programs
                            like basic needs, education, healthcare, and psychosocial support.  You can also give a one-time gift or support a campaign.
                                <a href="{{route('donate')}}">Click on the link to see all our giving platforms.</a>
                            </p>
                        </li>
                        <li>
                            <h3><span>03</span>Make an order or purchase from our sustainability projects</h3>
                            <p>We run a variety of sustainability projects including a dairy farm, a guest house, farm (selling vegetables and fruits) and the water project.
                                <a class="cust-links" href="javascript:void(0)">Click to order</a>
                            </p>
                        </li>
                        <li>
                            <h3><span>04</span>Volunteer or become an intern.</h3>
                            <p>Teule has short-term and long-term opportunities for local and international volunteers and interns. We are looking for individuals or groups who share our vision and are passionate to serve others.<a href="{{route('apply_intern')}}">Apply here </a></p>
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
                            <i data-toggle="popover" data-content="This is a popover content!" title="Popover Title" class="auto-popover fa fa-info-circle text-white more-info"></i>
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