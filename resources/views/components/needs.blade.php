<div class="need-area pt-100 pb-70">
    <div class="container">
        <div class="section-title">
            <span class="sub-title">needs</span>
            <h2>Current Needs</h2>
        </div>
        <div class="row">
            @foreach($needItems as $item)
            <div class="col-sm-6 col-lg-3">
                <div class="need-item three">
                    <i class="{{ $item->icon }}"></i>
                    <h3>{{ $item->title }}</h3>
                    <p>{{ $item->description }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>