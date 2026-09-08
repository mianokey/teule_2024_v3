@extends('layouts.app')

@section('content')

<section class="blog-area pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <span class="sub-title">Our Blog</span>
            <h2>Latest Stories & Updates</h2>
        </div>

        <div class="row">
            @forelse($posts as $post)
            <div class="col-lg-4 col-md-6">
                <div class="single-blog-post">
                    <div class="blog-img">
                        <a href="{{ route('blog.show', $post->slug) }}">
                            <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" style="height:250px; object-fit:cover;">
                        </a>
                    </div>
                    <div class="blog-content">
                        <h3>
                            <a href="{{ route('blog.show', $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </h3>
                        <p>{{ Str::limit($post->excerpt, 120) }}</p>
                        <div class="blog-meta">
                            <span>By {{ $post->author ?? 'Teule Team' }}</span> |
                            <span>{{ $post->created_at->format('d M Y') }}</span>
                        </div>

                        <!-- Social Share Buttons -->
                        <div class="social-share mt-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" class="me-2">
                                <i class="fab fa-facebook-f"></i> Share
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank">
                                <i class="fab fa-twitter"></i> Tweet
                            </a>
                        </div>

                        <a href="{{ route('blog.show', $post->slug) }}" class="read-more-btn mt-2 d-block">Read More</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No posts found.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

@endsection