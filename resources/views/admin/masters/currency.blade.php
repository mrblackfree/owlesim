@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title">Currencies</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID#</th>
                                <th>Currency</th>
                                <th>Symbol</th>
                                <th>Referral Point <i class="fa fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="One currency equal points example:1 USD = 1 point"></i></th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currencies as $i => $currency)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $currency->name}}</td>
                                <td>{{ $currency->symbol }}</td>
                                <td>{{ $currency->referral_point }}</td>
                                <td>{{ date('d M Y h:i A', strtotime($currency->created_at)) }}</td>
                                <td>{{ date('d M Y h:i A', strtotime($currency->updated_at)) }}</td>
                                <td>
                                    <button
                                        data-points="{{ $currency->referral_point }}"
                                        data-action="{{ route('admin.currencies.update-points', $currency->id) }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#currencyModal"
                                        class="btn btn-sm btn-warning btn-update-points">
                                        Update Points
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="currencyModal" tabindex="-1" aria-labelledby="currencyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="currencyActionForm" method="POST" action="">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Currency</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col">
                        <label class="form-label">Referral Point</label>
                        <input type="text" placeholder="1 currency equal points" id="points" name="points" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.btn-update-points', function() {
        let id = $(this).data('id');
        let points = $(this).data('points');
        let actionUrl = $(this).data('action');

        $('#points').val(points);
        $('#currencyActionForm').attr('action', actionUrl);
    });
</script>
@endpush
