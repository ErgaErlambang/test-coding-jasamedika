@extends('layouts.master')

@section('title', $vehicle->model)

@push('styles')
<style>
    .symbol.symbol-xl-90 .symbol-label {
        width: 300px;
        height: 200px;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <div class="card card-custom gutter-b example example-compact">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">{{ $vehicle->model }}</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        @include('layouts.alert')
                    </div>
                    <div class="col-md-4">
                        <div class="flex-row-auto" id="kt_profile_aside">
                            <div class="text-center mb-10">
                                <div class="symbol symbol-xl-90">
                                    <div class="symbol-label"
                                        style="background-image:url('{{ asset('uploads/vehicle/'.$vehicle->image) }}')">
                                    </div>
                                </div>
                                <h4 id="name_user" class="font-weight-bold my-2"></h4>
                                <div id="email_user" class="text-muted mb-2"></div>
                                <span class="label label-light-{{ $vehicle->is_available ? "success" : "warning" }} label-inline font-weight-bold label-lg">
                                    {{ $vehicle->is_available ? "Available" : "Not Available" }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <span>Brand</span>
                            </div>
                            <div class="col-md-8">
                                <span>: {{ $vehicle->brand }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <span>Model</span>
                            </div>
                            <div class="col-md-8">
                                <span>: {{ $vehicle->model }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <span>Plate Number</span>
                            </div>
                            <div class="col-md-8">
                                <span>: {{ $vehicle->plate_number }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <span>Price per day</span>
                            </div>
                            <div class="col-md-8">
                                <span>: Rp {{ fRupiah($vehicle->price) }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <span>Unavailable until</span>
                            </div>
                            <div class="col-md-8">
                                @if ($vehicle->is_available)
                                    <span>: 
                                        <span class="label label-light-success label-inline font-weight-bold label-lg">Vehicle is Available!</span>
                                    </span>
                                @else
                                    <span>:
                                        <span class="label label-light-warning label-inline font-weight-bold label-lg"> {{ date('d F Y', strtotime($vehicle->available_until)); }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="javascript:;" class="btn btn-primary ml-auto btn-book" data-toggle="modal" data-target="#modalBook">Book now</a>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBook" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('vehicle.booking', $vehicle->slug) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Booking {{ $vehicle->model }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-12">Starting book date</label>
                            <div class="col-md-12">
                                <div class="input-daterange input-group" id="datepicker">
                                    <input type="text" class="form-control rangedate" name="start" readonly/>
                                <div class="input-group-append">
                                    <span class="input-group-text">to</span>
                                </div>
                                    <input type="text" class="form-control rangedate" name="end" readonly/>
                                </div>
                                <span class="form-text">Estimated price : <span id="estimate-price" class="font-weight-bold">-</span></span>
                                <span class="form-text">Available status : <span id="estimate-status" class="font-weight-bold">-</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary font-weight-bold">Book Car</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
<script>
    $('#datepicker').datepicker({
        todayHighlight: true,
        format: 'yyyy-mm-dd',
        startDate: new Date(),
        templates: {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
    });
    
</script>

<script>
    $(function() {
        $('.rangedate').on("change", function() {
            var start = $("input[name='start']").val();
                end = $("input[name='end']").val();
                textPrice = $('#estimate-price');
                textStatus = $('#estimate-status');
            
            textPrice.text('...');
            textStatus.html('...'); 
            
            $.ajax({
                method: "POST",
                url: "{{ route('vehicle.checkstatus') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: { model: "{{ $vehicle->slug }}", start_date: start, end_date: end },
                success: function(e) {
                    if(e.status == false) {
                        textPrice.text(e.price);
                        textStatus.html("<span class='badge badge-pill badge-danger'>"+e.message+"</span>"); 
                    }else {
                        textPrice.text(e.price);
                        textStatus.html("<span class='badge badge-success'>"+e.message+"</span>"); 
                    }
                },
                error: function(data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                    Swal.fire("Opps", "Something went wrong", "error");
                }
            })
        });
    });
</script>
@endpush
