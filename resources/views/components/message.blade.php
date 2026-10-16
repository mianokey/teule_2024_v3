    @if($errors->any())
        <div class="requisition-state-panel requisition-notice-danger mb-3">
            <div class="requisition-state-main">
                <div class="requisition-state-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <div class="requisition-state-text">
                    <strong>Please correct the following errors:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
