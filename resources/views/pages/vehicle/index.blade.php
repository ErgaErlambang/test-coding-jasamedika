@extends('layouts.master')

@section('title', 'Vehicle Management')

@push('styles')
<link href="{{asset('assets/plugins/custom/datatables/datatables.bundle.css?v=7.0.5')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-6 pb-0">
            <div class="card-title">
                <h3 class="card-label">Vehicle Management</h3>
            </div>
            <div class="card-toolbar">
                <div class="dropdown dropdown-inline mr-2">
                    @if(Auth::user()->hasRole->slug == 'superadministrator')
                        <a href="{{ route('vehicle.create') }}" class="btn btn-primary font-weight-bolder"><i class="la la-plus"></i>New Record</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            @include('layouts.alert')
            <table class="table table-separate table-head-custom" id="kt_datatable_2">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Plate</th>
                        <th>Available</th>
                        @if(Auth::user()->hasRole->slug == 'superadministrator')
                            <th>Active</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@include('layouts.modal')
@endsection


@push('scripts')
<script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js?v=7.0.5')}}"></script>
<script>
    function showModal() {
        var $form = $('.form-delete');
        $('#confirm').modal({
            backdrop: 'static',
            keyboard: false
        }).on('click', '#delete-btn', function () {
            $form.submit();
        });
    }
</script>

@if(\Auth::user()->hasRole->slug == "superadministrator")
<script>
    $(document).ready(function() {
        $('#kt_datatable_2').DataTable({
            responsive:true,
            lengthMenu: [5, 10, 25, 50],
            processing: true,
            serverSide: true,
            ajax: "{{ route('vehicle.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'brand', name: 'brand'},
                {data: 'model', name: 'model'},
                {data: 'plate_number', name: 'plate_number'},
                {data: 'available', name: 'available'},
                {data: 'active', name: 'active'},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false
                },
            ]
        });
    } );
</script>
@else
<script>
    $(document).ready(function() {
        $('#kt_datatable_2').DataTable({
            responsive:true,
            lengthMenu: [5, 10, 25, 50],
            processing: true,
            serverSide: true,
            ajax: "{{ route('vehicle.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'brand', name: 'brand'},
                {data: 'model', name: 'model'},
                {data: 'plate_number', name: 'plate_number'},
                {data: 'available', name: 'available'},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false
                },
            ]
        });
    } );
</script>
@endif

@endpush