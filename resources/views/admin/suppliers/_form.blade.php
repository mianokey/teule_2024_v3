@php
    $isEditing = isset($supplier) && $supplier->exists;
@endphp

<div class="requisition-section">

    <div class="requisition-section-header">
        <div class="requisition-section-heading">
            <div class="requisition-section-icon">
                <i class="fas fa-building"></i>
            </div>

            <div>
                <h5>Supplier Information</h5>
                <p>Enter the supplier's business and contact information.</p>
            </div>
        </div>
    </div>

    <div style="padding: 20px;">

        <div class="row g-3">

            {{-- SUPPLIER NAME --}}
            <div class="col-md-6">
                <label class="requisition-detail-label">
                    Supplier Name <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control requisition-input @error('name') is-invalid @enderror"
                    value="{{ old('name', $supplier->name ?? '') }}"
                    placeholder="Enter supplier or business name"
                    required>

                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- CATEGORY --}}
            <div class="col-md-6">
                <label class="requisition-detail-label">
                    Supplier Category <span class="text-danger">*</span>
                </label>

                <select
                    name="category"
                    class="form-select requisition-input @error('category') is-invalid @enderror"
                    required>

                    <option value="">Select supplier category</option>

                    @foreach($categories as $value => $label)
                        <option
                            value="{{ $value }}"
                            {{ old('category', $supplier->category ?? '') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach

                </select>

                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- CONTACT PERSON --}}
            <div class="col-md-6">
                <label class="requisition-detail-label">Contact Person</label>

                <input
                    type="text"
                    name="contact_person"
                    class="form-control requisition-input"
                    value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
                    placeholder="Contact person's name">
            </div>

            {{-- PHONE --}}
            <div class="col-md-6">
                <label class="requisition-detail-label">Phone Number</label>

                <input
                    type="text"
                    name="phone"
                    class="form-control requisition-input"
                    value="{{ old('phone', $supplier->phone ?? '') }}"
                    placeholder="Supplier phone number">
            </div>

            {{-- EMAIL --}}
            <div class="col-md-6">
                <label class="requisition-detail-label">Email Address</label>

                <input
                    type="email"
                    name="email"
                    class="form-control requisition-input"
                    value="{{ old('email', $supplier->email ?? '') }}"
                    placeholder="Supplier email address">
            </div>

            {{-- TAX PIN --}}
            <div class="col-md-6">
                <label class="requisition-detail-label">KRA PIN / Tax PIN</label>

                <input
                    type="text"
                    name="tax_pin"
                    class="form-control requisition-input"
                    value="{{ old('tax_pin', $supplier->tax_pin ?? '') }}"
                    placeholder="Supplier KRA PIN">
            </div>

            {{-- ADDRESS --}}
            <div class="col-12">
                <label class="requisition-detail-label">Business Address</label>

                <textarea
                    name="address"
                    rows="3"
                    class="form-control requisition-input"
                    placeholder="Enter supplier's physical or postal address">{{ old('address', $supplier->address ?? '') }}</textarea>
            </div>

        </div>

    </div>

</div>

{{-- PAYMENT AND ACCOUNT INFORMATION --}}
<div class="requisition-section">

    <div class="requisition-section-header">
        <div class="requisition-section-heading">
            <div class="requisition-section-icon">
                <i class="fas fa-wallet"></i>
            </div>

            <div>
                <h5>Payment & Account Information</h5>
                <p>Set the supplier's payment terms and account limits.</p>
            </div>
        </div>
    </div>

    <div style="padding: 20px;">

        <div class="row g-3">

            {{-- PAYMENT TERMS --}}
            <div class="col-md-4">
                <label class="requisition-detail-label">Payment Terms</label>

                <input
                    type="text"
                    name="payment_terms"
                    class="form-control requisition-input"
                    value="{{ old('payment_terms', $supplier->payment_terms ?? '') }}"
                    placeholder="e.g. Cash, 30 Days, 60 Days">
            </div>

            {{-- CREDIT LIMIT --}}
            <div class="col-md-4">
                <label class="requisition-detail-label">Credit Limit (KES)</label>

                <input
                    type="number"
                    name="credit_limit"
                    min="0"
                    step="0.01"
                    class="form-control requisition-input"
                    value="{{ old('credit_limit', $supplier->credit_limit ?? '0.00') }}"
                    placeholder="0.00">
            </div>

            {{-- OPENING BALANCE --}}
            <div class="col-md-4">
                <label class="requisition-detail-label">Opening Balance (KES)</label>

                <input
                    type="number"
                    name="opening_balance"
                    min="0"
                    step="0.01"
                    class="form-control requisition-input"
                    value="{{ old('opening_balance', $supplier->opening_balance ?? '0.00') }}"
                    placeholder="0.00">
            </div>

        </div>

        <div class="mt-3">
            <label class="requisition-detail-label">Notes</label>

            <textarea
                name="notes"
                rows="4"
                class="form-control requisition-input"
                placeholder="Additional supplier information">{{ old('notes', $supplier->notes ?? '') }}</textarea>
        </div>

    </div>

</div>

{{-- STATUS --}}
<div class="requisition-section">

    <div class="requisition-section-header">
        <div class="requisition-section-heading">
            <div class="requisition-section-icon">
                <i class="fas fa-toggle-on"></i>
            </div>

            <div>
                <h5>Supplier Status</h5>
                <p>Control whether this supplier is available for use.</p>
            </div>
        </div>
    </div>

    <div style="padding: 20px;">

        <input type="hidden" name="is_active" value="0">

        <label class="d-flex align-items-center gap-2">

            <input
                type="checkbox"
                name="is_active"
                value="1"
                {{ (bool) old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>

            <span>
                Active supplier
            </span>

        </label>

        <small class="text-muted">
            Inactive suppliers will not be available for normal purchasing workflows.
        </small>

    </div>

</div>

{{-- ACTIONS --}}
<div class="requisition-bottom-actions">

    <a href="{{ $isEditing
        ? route('admin.suppliers.show', $supplier)
        : route('admin.suppliers.index') }}"
       class="requisition-cancel-button">

        <i class="fas fa-times"></i>
        Cancel

    </a>

    <button type="submit" class="requisition-save-button">

        <i class="fas fa-save"></i>

        {{ $isEditing ? 'Update Supplier' : 'Save Supplier' }}

    </button>

</div>