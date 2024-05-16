@extends('layouts.master')

@section('title', 'Vehicle Management')

@push('styles')
<style>
    .image-input .image-input-wrapper {
        width: 240px;
        height: 212px;
        border-radius: 0.42rem;
        background-repeat: no-repeat;
        background-size: cover;
    }
    </style>
@endpush

@section('content')
<div class="container">
    <form action="{{ route('vehicle.store') }}" method="POST" enctype="multipart/form-data" accept-charset="utf-8">
        @csrf
        <div class="card card-custom">
            <div class="card-header flex-wrap border-0 pt-6 pb-0">
                <div class="card-title">
                    <h3 class="card-label">Create Vehicle</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group mb-8">
                    @include('layouts.alert')
                </div>
                
                <div class="form-group">
                    <label>Brand <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="select_brand" multiple="multiple" name="brand">
                        <option label="Label"></option>
                        @foreach ($data['brand'] as $brand)
                            <option value="{{ $brand }}"> {{ $brand }} </option>
                        @endforeach
                    </select>
                    <span class="form-text text-muted">You can input or choose from the brands you have created.</span>
                </div>

                <div class="form-group">
                    <label>Model <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="select_model" multiple="multiple" name="model">
                        <option label="Label"></option>
                        @foreach ($data['model'] as $model)
                            <option value="{{ $model }}"> {{ $model }} </option>
                        @endforeach
                    </select>
                    <span class="form-text text-muted">You can input or choose from the brands you have created.</span>
                </div>

                <div class="form-group">
                    <label> Price <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" placeholder="Price per day" name="price" value="{{ old('price') }}">
                </div>

                <label> Plate Number <span class="text-danger">*</span></label>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Value" name="plate_number[]">
                            <span class="form-text text-muted">You can create multiple vehicles with the same data and different Plate </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="addValue" class="btn btn-primary btn-sm btn-info btn-just-icon">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div id="formArrayBuilder"></div>

                <div class="form-group row">
                    <label class="col-md-12"> Image </label>
                    <div class="image-input image-input-empty image-input-outline ml-3" id="kt_image_5" style="background-image: url('{{ asset('assets/media/misc/placeholder.png') }}')">
                        <div class="image-input-wrapper"></div>
                        <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change image">
                            <i class="fa fa-pen icon-sm text-muted"></i>
                            <input type="file" name="photo" accept=".png, .jpg, .jpeg" />
                            <input type="hidden" name="image_remove" />
                        </label>
                        <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel image">
                            <i class="ki ki-bold-close icon-xs text-muted"></i>
                        </span>
                        <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove image">
                            <i class="ki ki-bold-close icon-xs text-muted"></i>
                        </span>
                    </div>
                    <span class="form-text text-muted col-md-12 mt-4">Allowed file types: png, jpg, jpeg.</span>
                </div>

                <div class="form-group">
                    <label class="col-form-label">Enable this Vehicle ?</label>
                    <label class="checkbox checkbox-outline checkbox-outline-2x checkbox-primary">
                        <input type="checkbox" name="is_enable" {{ old('is_enable') ? 'checked' : '' }}>
                        <span></span>
                    </label>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                <a href="{{ route('vehicle.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/pages/crud/file-upload/image-input.js?v=7.0.5') }}"></script>
<script>
    var avatar5 = new KTImageInput('kt_image_5');
    $('#select_model').select2({
        placeholder: "Select or input option",
        maximumSelectionLength: 1,
        tags: true
    });
    $('#select_brand').select2({
        placeholder: "Select or input option",
        maximumSelectionLength: 1,
        tags: true
    });
</script>

<script>
    $(document).ready(function() {
        var container = $('#formArrayBuilder');
        var btnIncrement = $('#addValue');
        
        btnIncrement.click(function() {
            container.append(
                `<div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Value" name="plate_number[]">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="removeValue" class="btn btn-danger btn-sm btn-info btn-just-icon">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>`
            )
            $("body").on("click","#removeValue",function(){
                $(this).closest('#formArrayBuilder .row').remove();
            }); 
        })
    });
</script>
@endpush