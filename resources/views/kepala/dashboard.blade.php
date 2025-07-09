{{-- resources/views/kepala/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Kepala')

@section('content')
<!--  Row 1 -->
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center">
          <div>
            <h4 class="card-title">Ringkasan Panen</h4>
            <p class="card-subtitle">
              Grafik hasil panen per bulan dan produktivitas
            </p>
          </div>
          <div class="ms-auto">
            <ul class="list-unstyled mb-0">
              <li class="list-inline-item text-success">
                <span class="round-8 bg-success rounded-circle me-1 d-inline-block"></span>
                Produktivitas
              </li>
              <li class="list-inline-item text-info">
                <span class="round-8 bg-info rounded-circle me-1 d-inline-block"></span>
                Total Panen
              </li>
            </ul>
          </div>
        </div>
        <div id="harvest-overview" class="mt-4 mx-n6"></div>
      </div>
    </div>
  </div>
</div>
@endsection