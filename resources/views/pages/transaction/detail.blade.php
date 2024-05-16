@extends('layouts.master')

@section('title', 'Detail Transaction')

@push('styles')

@endpush

@section('content')
<div class="container">
    <div class="card card-custom overflow-hidden">
        <div class="card-body p-0">
            @include('layouts.alert')
            <div class="row justify-content-center pt-20 pb-15 px-8 px-md-0">
                <div class="col-md-9">
                    <h3 class="text-muted mb-8">Profile Information</h3>
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-column flex-root">
                            <span class="font-weight-bolder mb-2">Name</span>
                            <span class="opacity-70">{{ $transaction->user->name }}</span>
                        </div>
                        <div class="d-flex flex-column flex-root">
                            <span class="font-weight-bolder mb-2">Email</span>
                            <span class="opacity-70">{{ $transaction->user->email }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-9 mt-5">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-column flex-root">
                            <span class="font-weight-bolder mb-2">Address</span>
                            <span class="opacity-70"><span class="opacity-70">{{ $transaction->user->profile->address }}</span></span>
                        </div>
                        <div class="d-flex flex-column flex-root">
                            <span class="font-weight-bolder mb-2">Phone Number</span>
                            <span class="opacity-70"><span class="opacity-70">{{ $transaction->user->profile->phone_number }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center py-5 px-8 px-md-0">
                <div class="col-md-9">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="pl-0 font-weight-bold text-muted text-uppercase">Description</th>
                                    <th class="font-weight-bold text-muted text-uppercase">Date</th>
                                    <th class="font-weight-bold text-muted text-uppercase">Rate</th>
                                    <th class="text-right pr-0 font-weight-bold text-muted text-uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $start_date = new DateTime($transaction->start_date);
                                    $end_date = new DateTime($transaction->end_date);
                                    $return_date = new DateTime($transaction->returned_date);

                                    $overtime = $end_date->diff($return_date)->format("%r%a");
                                    $final_date = $start_date->diff($end_date)->days + 1;
                                    $fines = $transaction->base_price * ($overtime <= 0 ? 0 : $overtime);
                                @endphp

                                <tr class="font-weight-boldest">
                                    <td class="pl-0 pt-7">{{ $transaction->vehicle->model }}</td>
                                    <td class="pt-7">{{ $start_date->format("d")." - ".$end_date->format("d F Y")." ($final_date days)" }}</td>
                                    <td class="pt-7">Rp. {{ fRupiah($transaction->base_price )}}</td>
                                    <td class="text-danger pr-0 pt-7 text-right">
                                        Rp. {{ fRupiah($transaction->total+$fines) }} {{ $overtime <= 0 ? '' : "(+ Rp. ".fRupiah($fines)." fines)" }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-9">
                    <h3 class="text-muted mb-8">Vehicle Information</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold text-muted text-uppercase">Brand</th>
                                    <th class="font-weight-bold text-muted text-uppercase">Model</th>
                                    <th class="font-weight-bold text-muted text-uppercase">Plate Number</th>
                                    <th class="font-weight-bold text-muted text-uppercase">Return Date</th>
                                    <th class="font-weight-bold text-muted text-uppercase">Overtime</th>
                                    <th class="font-weight-bold text-muted text-uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="font-weight-bolder">
                                    <td>{{ $transaction->vehicle->brand }}</td>
                                    <td>{{ $transaction->vehicle->model }}</td>
                                    <td>{{ $transaction->vehicle->plate_number }}</td>
                                    <td>{{ !empty($transaction->returned_date) ? date('d F Y', strtotime($transaction->returned_date)) : "-" }}</td>
                                    <td><span class="badge badge-{{ $overtime <= 0 ? 'success' : 'danger' }}"> {{ $overtime <= 0 ? 0 : $overtime }} days </span></td>
                                    @switch($transaction->status)
                                        @case("Booked")
                                            <td>
                                                <span class="badge badge-warning">Booked</span>
                                            </td>
                                            @break
                                        @case("Active")
                                            <td>
                                                <span class="badge badge-success">Active</span>
                                            </td>
                                            @break
                                        @case("Returned")
                                            <td>
                                                <span class="badge badge-warning">Returned</span>
                                            </td>
                                            @break
                                        @case("Done")
                                            <td>
                                                <span class="badge badge-success">Done</span>
                                            </td>
                                            @break
                                        @case("Canceled")
                                            <td>
                                                <span class="badge badge-warning">Canceled</span>
                                            </td>
                                            @break
                                        @default
                                            
                                    @endswitch
                                    
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-9">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transaction.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
                        @if(Auth::user()->hasRole->slug == "superadministrator")
                            <a href="javascript:;" class="btn btn-light-primary font-weight-bold" data-toggle="modal" data-target="#modalUpdate">Update</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('transaction.updateStatus', $transaction->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Status Transaction</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-md-12">Status Transaction</label>
                        <div class="col-md-12">
                            <select class="form-control select2" id="kt_select2_2" name="status">
                                <optgroup label="Current">
                                    <option value="{{ $transaction->status }}" selected>{{ $transaction->status }}</option>
                                </optgroup>
                                <optgroup label="Others">
                                    <option value="Booked">Booked</option>
                                    <option value="Active">Active</option>
                                    <option value="Returned">Returned</option>
                                    <option value="Done">Done</option>
                                    <option value="Canceled">Canceled</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#modalUpdate').on('shown.bs.modal', function () {
        $('#kt_select2_2').select2({
            placeholder: "Select an option"
        });
    });
</script>
@endpush