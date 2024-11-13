@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="about-area pt-100 pb-70">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-7">
                <div class="about-content">
                    <div class="section-title">
                        <span class="sub-title">Our Founders</span>
                        <h2>MESSAGE FROM OUR FOUNDERS</h2>
                    </div>

                    <p style="text-align: justify;">
                    For over 25 years, I have had the privilege to see Jesus transform the lives of the vulnerable children.  When I started in 1994,
                     it was a step of faith to make a difference in the plight of street children. Over the years there have been ups and downs. We
                      have faced many challenges. While working with vulnerable children and families can be difficult, it’s been a joy watching many
                       of these children grow up and have their own families. I am amazed at how far God has brought us and all that He has done. My
                        wife and I  are so honored to serve these children and be a part of the Teule family. Our heart's desire is to see each child
                         reach their full potential and change their community and the world. We look forward to many more years of impacting lives
                          and uplifting communities.

                    <blockquote class="cust-blockquote">
                        What a great opportunity for all of us to partner together and serve this generation.
                    </blockquote>

                    </br>With gratitude,

                    </br>John and Aneita High

                    </p>

                    <div class="about-btn-area">
                        <a class="common-btn" href="{{ route('readmore', ['documentName' => 'strategic plan.pdf']) }}">Learn more from the strategic plan</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="about-img">
                    <img src="{{asset('assets/img/banner/banner-main3.png')}}" alt="About">

                </div>
            </div>
        </div>
    </div>
</div>
<x-footer></x-footer>
@endsection