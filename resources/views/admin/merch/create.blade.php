@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">Create Product</h6>
    </div>

    <x-message></x-message>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="product-form">
            @csrf

            <!-- Product Basic Info -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Product Name" required>
                        <label for="name">Product Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" class="form-control" id="base_price" name="base_price" value="{{ old('base_price') }}" placeholder="Base Price" required>
                        <label for="base_price">Base Price</label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="description" name="description" placeholder="Product Description" rows="3">{{ old('description') }}</textarea>
                        <label for="description">Description</label>
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="allow_preorder" name="allow_preorder" value="1" checked>
                        <label class="form-check-label" for="allow_preorder">Allow Preorder</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="allow_design_upload" name="allow_design_upload" value="1">
                        <label class="form-check-label" for="allow_design_upload">Allow Design Upload</label>
                    </div>
                </div>
            </div>

            <!-- Product Image -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="file" class="form-control" id="image" name="image" required>
                        <label for="image">Product Image</label>
                    </div>
                </div>
            </div>

            <!-- Variants -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <h6>Variants</h6>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered" id="variants-table">
                            <thead>
                                <tr>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Price (Optional)</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="variants[0][color]" class="form-control" required></td>
                                    <td><input type="text" name="variants[0][size]" class="form-control" required></td>
                                    <td><input type="number" step="0.01" name="variants[0][price]" class="form-control"></td>
                                    <td><input type="number" name="variants[0][stock]" class="form-control" required></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="add-variant" class="btn btn-success btn-sm mt-2">Add Variant</button>
                </div>
            </div>

            <!-- Submit -->
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block">Create Product</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    #variants-table tbody {
        display: block;
        max-height: 300px;
        overflow-y: auto;
    }

    #variants-table thead,
    #variants-table tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;

    document.getElementById('add-variant').addEventListener('click', function () {
        const tableBody = document.querySelector('#variants-table tbody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" name="variants[${rowIndex}][color]" class="form-control" required></td>
            <td><input type="text" name="variants[${rowIndex}][size]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="variants[${rowIndex}][price]" class="form-control"></td>
            <td><input type="number" name="variants[${rowIndex}][stock]" class="form-control" required></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
        `;
        tableBody.appendChild(newRow);
        rowIndex++;
    });

    document.querySelector('#variants-table').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endsection
