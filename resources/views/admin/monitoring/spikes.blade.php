@extends('layouts/contentNavbarLayout')

@section('title', 'Lead Spikes')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0">Lead Spike Detection</h5>
        <a href="{{ route('admin.monitoring.index') }}" class="btn btn-secondary btn-sm">
          <i class="icon-base ti tabler-arrow-left me-1"></i>Back to Dashboard
        </a>
      </div>
      <div class="card-body">
        <div class="alert alert-info mb-4">
          <i class="icon-base ti tabler-info-circle me-2"></i>
          Showing lead spikes with more than 20 leads per hour in the last 7 days.
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Hour</th>
                <th>Leads</th>
                <th>Severity</th>
                <th>Trend</th>
              </tr>
            </thead>
            <tbody>
              @forelse($spikes as $spike)
              <tr>
                <td>{{ \Carbon\Carbon::parse($spike->date)->format('M d, Y') }}</td>
                <td>{{ str_pad($spike->hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                <td>
                  <span class="badge bg-label-{{ $spike->count > 100 ? 'danger' : ($spike->count > 50 ? 'warning' : 'info') }}">
                    {{ $spike->count }}
                  </span>
                </td>
                <td>
                  @if($spike->count > 100)
                    <span class="badge bg-danger">Critical</span>
                  @elseif($spike->count > 50)
                    <span class="badge bg-warning">High</span>
                  @else
                    <span class="badge bg-info">Moderate</span>
                  @endif
                </td>
                <td>
                  @php
                    $prevHour = \Carbon\Carbon::parse($spike->date . ' ' . str_pad($spike->hour, 2, '0', STR_PAD_LEFT) . ':00:00')->subHour();
                    $prevCount = \App\Models\Lead::whereBetween('created_at', [$prevHour, $prevHour->copy()->addHour()])->count();
                    $change = $spike->count - $prevCount;
                    $changePercent = $prevCount > 0 ? round(($change / $prevCount) * 100) : 0;
                  @endphp
                  @if($change > 0)
                    <span class="text-danger">
                      <i class="icon-base ti tabler-trending-up"></i>
                      +{{ $change }} ({{ $changePercent }}%)
                    </span>
                  @elseif($change < 0)
                    <span class="text-success">
                      <i class="icon-base ti tabler-trending-down"></i>
                      {{ $change }} ({{ abs($changePercent) }}%)
                    </span>
                  @else
                    <span class="text-muted">
                      <i class="icon-base ti tabler-minus"></i>
                      No change
                    </span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center py-4">
                  <i class="icon-base ti tabler-trending-up icon-48px text-success mb-2"></i>
                  <p class="text-muted mb-0">No lead spikes detected in the last 7 days</p>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($spikes->hasPages())
        <div class="d-flex justify-content-center mt-4">
          {{ $spikes->links() }}
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Lead Details Modal -->
<div class="modal fade" id="leadDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Lead Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="leadDetailsContent">
          <!-- Content will be loaded here -->
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
function viewLeadDetails(date, hour) {
    // This could be implemented to show detailed leads for that hour
    console.log('Viewing leads for', date, hour);
}
</script>
@endsection