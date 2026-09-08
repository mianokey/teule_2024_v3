<section class="blog-area pt-100 pb-70 bg-light">
    <div class="container">

        <!-- Section Title -->
        <div class="section-title text-center mb-5">
            <span class="sub-title">Stories That Inspire</span>
            <h2>Impact Stories & Updates</h2>
            <p>See how lives are being transformed through love, care, and opportunity.</p>
        </div>

        <!-- Featured Post Slider -->
        <div class="featured-blog-slider owl-carousel owl-theme mb-5">
            @if($featuredPost)
            <div class="featured-blog-card">
                <div class="featured-img" style="background-image:url('{{ asset($featuredPost->image) }}');">
                    <div class="overlay">
                        <span class="category">{{ $featuredPost->category ?? 'Impact Story' }}</span>
                        <h3><a href="{{ route('blogshow',$featuredPost->slug) }}">{{ $featuredPost->title }}</a></h3>
                        <div class="meta">
                            <span><i class="icofont-calendar"></i> {{ \Carbon\Carbon::parse($featuredPost->created_at)->format('d M Y') }}</span>
                            <span><i class="icofont-user"></i> {{ $featuredPost->author ?? 'Teule Team' }}</span>
                        </div>
                        <a class="read-btn" href="{{ route('blogshow',$featuredPost->slug) }}">Read Story</a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Blog Grid -->
        <div class="row mt-4">
            @foreach($otherPosts as $post)
            <div class="col-lg-3 col-md-6">
                <div class="blog-card">
                    <div class="blog-image">
                        <img src="{{ asset($post->image) }}" alt="{{ $post->title }}">
                        <div class="blog-overlay">
                            <a href="{{ route('blogshow',$post->slug) }}" class="read-btn">Read Story</a>
                        </div>
                        <span class="blog-category">{{ $post->category ?? 'Impact Story' }}</span>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="icofont-calendar"></i> {{ \Carbon\Carbon::parse($post->created_at)->format('d M Y') }}</span>
                            <span><i class="icofont-user"></i> {{ $post->author ?? 'Teule Team' }}</span>
                        </div>
                        <h3><a href="{{ route('blogshow',$post->slug) }}">{{ $post->title }}</a></h3>
                        <p>{{ Str::limit($post->excerpt, 120) }}</p>
                        <div class="blog-footer">
                            <a class="read-more" href="{{ route('blogshow',$post->slug) }}">Continue Reading →</a>
                            <div class="share-icons">
                                <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ route('blogshow',$post->slug) }}"><i class="icofont-facebook"></i></a>
                                <a target="_blank" href="https://twitter.com/intent/tweet?url={{ route('blogshow',$post->slug) }}"><i class="icofont-twitter"></i></a>
                                <a target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url={{ route('blogshow',$post->slug) }}"><i class="icofont-linkedin"></i></a>
                                <a target="_blank" href="https://wa.me/?text={{ route('blogshow',$post->slug) }}"><i class="icofont-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('blog') }}" class="common-btn">Explore All Stories</a>
        </div>

    </div>

    <style>
        /* Featured Slider */
        .featured-blog-slider .featured-blog-card{
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            height: 400px;
        }
        .featured-blog-slider .featured-img{
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            position: relative;
            transition: transform 0.4s ease;
        }
        .featured-blog-slider .featured-img:hover{
            transform: scale(1.05);
        }
        .featured-blog-slider .overlay{
            position: absolute;
            bottom: 0;
            left:0;
            right:0;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            padding: 20px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .featured-blog-slider .overlay .category{
            background:#ff5a3c;
            padding:5px 12px;
            border-radius:20px;
            display:inline-block;
            margin-bottom:10px;
        }
        .featured-blog-slider .read-btn{
            background:#ffcc00;
            color:#000;
            padding:10px 20px;
            border-radius:30px;
            font-weight:600;
            width:fit-content;
            margin-top:10px;
        }

        /* Blog Cards */
        .blog-card{
            background:#fff;
            border-radius:10px;
            overflow:hidden;
            transition:0.4s;
            box-shadow:0 10px 25px rgba(0,0,0,0.08);
            margin-bottom:30px;
        }
        .blog-card:hover{
            transform:translateY(-10px);
            box-shadow:0 20px 40px rgba(0,0,0,0.15);
        }
        .blog-image{
            position:relative;
            overflow:hidden;
        }
        .blog-image img{
            width:100%;
            height:200px;
            object-fit:cover;
            transition:0.4s;
        }
        .blog-card:hover img{
            transform:scale(1.08);
        }
        .blog-overlay{
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.45);
            display:flex;
            align-items:center;
            justify-content:center;
            opacity:0;
            transition:0.4s;
        }
        .blog-card:hover .blog-overlay{
            opacity:1;
        }
        .blog-category{
            position:absolute;
            top:15px;
            left:15px;
            background:#ff5a3c;
            color:#fff;
            padding:5px 12px;
            border-radius:20px;
            font-size:12px;
        }
        .blog-content{
            padding:20px;
        }
        .blog-meta{
            font-size:13px;
            color:#777;
            margin-bottom:10px;
            display:flex;
            justify-content:space-between;
        }
        .blog-footer{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-top:15px;
        }
        .share-icons a{
            margin-left:8px;
            font-size:18px;
            color:#555;
            transition:0.3s;
        }
        .share-icons a:hover{
            color:#ff5a3c;
        }
    </style>

</section>