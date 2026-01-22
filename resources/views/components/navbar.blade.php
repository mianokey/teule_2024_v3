<div class="scrolling-container">
    <div class="scrolling-text-wrapper">
        <p>THANK YOU SO MUCH FOR BEING PART OF US!</p>
        <p>EVERY CONTRIBUTION SHAPES TOMORROW’S LEADERS!</p>

        <p>FOR ANY INQUIRIES PLEASE EMAIL US AT teuleusa@teulekenya.org OR info@teulekenya.org </p>
    </div>
</div>
<div class="navbar-area sticky-top mb-4 mb-md-0">
    <div class="mobile-nav">
        <a href="https://teulekenya.org/" class="logo">
            <img width="40" src="{{asset('assets/img/logo512.png')}}" alt="Logo">
        </a>
    </div>
    <div class="main-nav">
        <div class="container">
            <nav class="navbar navbar-expand-md navbar-light">
                <a class="navbar-brand" href="https://teulekenya.org/">
                    <img width="50" src="{{asset('assets/img/logo192.png')}}" class="logo-onee" alt="Logo">
                    <img width="50" src="{{asset('assets/img/logo512.png')}}" class="logo-two" alt="Logo">
                </a>
                <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{route('home')}}" class="nav-link active">Home</a>

                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle">About Us <i
                                    class="icofont-simple-down"></i></a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a href="{{route('program')}}" class="nav-link active">Our Program</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('history')}}" class="nav-link">Our History</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('founders')}}" class="nav-link">Our Founders</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('board')}}" class="nav-link">The board</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('team')}}" class="nav-link">The team</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('gallery')}}" class="nav-link">Gallery</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle">Get Involved <i
                                    class="icofont-simple-down"></i></a>
                            <ul class="dropdown-menu">

                                <li class="nav-item">
                                    <a href="{{route('donate')}}" class="nav-link">Donate</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('edu_fund')}}" class="nav-link">Education Fund</a>
                                </li>
                                <!--<li class="nav-item">-->
                                <!--    <a  href="{{route('volunteer')}}" class="nav-link">volunteer</a>-->
                                <!--</li>-->
                                <li class="nav-item">
                                    <a href="{{route('apply_intern')}}" class="nav-link">Internship/Attachment</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle">Sustainability Projects <i
                                    class="icofont-simple-down"></i></a>
                            <ul class="dropdown-menu">

                                <li class="nav-item">
                                    <a href="{{route('sustainability', ['id' => 1, 'title' => 'Guest House']) }}"
                                        class="nav-link">Guest House</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('sustainability', ['id' => 2, 'title' => 'Teule Farm']) }}"
                                        class="nav-link">Farm</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('sustainability', ['id' => 3, 'title' => 'Teule Dairy']) }}"
                                        class="nav-link">Teule Dairy</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('sustainability', ['id' => 4, 'title' => 'Water']) }}"
                                        class="nav-link">Water Project</a>
                                </li>

                            </ul>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="{{route('tla')}}" class="nav-link">School (TLA)</a>
                        </li> -->
                        <li class="nav-item">
                            <a href="{{route('tla')}}" class="nav-link">Teule Leadership Academy</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('latest')}}" class="nav-link">Newsletters</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('sponsorship')}}" class="nav-link">Sponsor a child</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('careers')}}" class="nav-link">Careers</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('contact')}}" class="nav-link">Contact</a>
                        </li>
                    </ul>
                    <div class="side-nav">
                        <a class="donate-btn" href={{route('donate')}}>
                            DONATE
                            <i class="icofont-heart-alt"></i>
                        </a>
                    </div>
                </div>
            </nav>

        </div>
    </div>
</div>