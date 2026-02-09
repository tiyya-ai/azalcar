@extends('layouts/contentNavbarLayout')

@section('title', 'Admin Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.css">
@endsection

@section('page-style')
<style>
  .card-stats-item {
    transition: all 0.3s ease-in-out;
  }
  .card-stats-item:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  }
  .quick-action-card {
    border: 1px solid rgba(0,0,0,0.08);
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.2s;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-decoration: none !important;
  }
  .quick-action-card:hover {
    border-color: #7367F0;
    background: #f8f7ff;
  }
  .quick-action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    font-size: 1.5rem;
  }
  .quick-action-title {
    font-weight: 600;
    color: #5d596c;
    margin-bottom: 0.25rem;
  }
  .table-listing-img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 8px;
  }
</style>
@endsection

@section('content')

<!-- Quick Actions Grid -->
<div class="row g-4 mb-4">
   <div class="col-12">
      <h5 class="mb-0 text-muted small text-uppercase">Quick Management</h5>
   </div>
   <div class="col-6 col-md-4 col-lg-2">
      <a href="{{ route('admin.listings.index') }}" class="quick-action-card">
         <div class="quick-action-icon bg-label-primary text-primary">
            <i class="ti tabler-car"></i>
         </div>
         <span class="quick-action-title">Listings</span>
      </a>
   </div>
   <div class="col-6 col-md-4 col-lg-2">
      <a href="{{ route('admin.users.index') }}" class="quick-action-card">
         <div class="quick-action-icon bg-label-success text-success">
            <i class="ti tabler-users"></i>
         </div>
         <span class="quick-action-title">Users</span>
      </a>
   </div>
   <div class="col-6 col-md-4 col-lg-2">
      <a href="{{ route('admin.makes.index') }}" class="quick-action-card">
         <div class="quick-action-icon bg-label-info text-info">
            <i class="ti tabler-steering-wheel"></i>
         </div>
         <span class="quick-action-title">Car Makes</span>
      </a>
   </div>
   <div class="col-6 col-md-4 col-lg-2">
      <a href="{{ route('admin.vehicle_types.index') }}" class="quick-action-card">
         <div class="quick-action-icon bg-label-warning text-warning">
            <i class="ti tabler-category"></i>
         </div>
         <span class="quick-action-title">Types</span>
      </a>
   </div>
   <div class="col-6 col-md-4 col-lg-2">
      <a href="{{ route('admin.settings.index') }}" class="quick-action-card">
         <div class="quick-action-icon bg-label-secondary text-secondary">
            <i class="ti tabler-settings"></i>
         </div>
         <span class="quick-action-title">Settings</span>
      </a>
   </div>
   <div class="col-6 col-md-4 col-lg-2">
      <a href="{{ route('admin.monitoring.index') }}" class="quick-action-card">
         <div class="quick-action-icon bg-label-danger text-danger">
            <i class="ti tabler-activity"></i>
         </div>
         <span class="quick-action-title">Monitoring</span>
      </a>
   </div>
</div>

<!-- Key Metrics -->
<div class="row g-4 mb-4">
  <!-- Total Revenue -->
  <div class="col-sm-6 col-xl-3">
    <div class="card card-stats-item h-100">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Revenue</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">₽{{ number_format($stats['revenue']) }}</h4>
              @if($stats['today_revenue'] > 0)
                <span class="badge bg-label-success rounded-pill">+₽{{ number_format($stats['today_revenue']) }}</span>
              @endif
            </div>
            <small class="text-muted mb-0">Platform earnings</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="icon-base ti tabler-currency-rubel icon-28px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Listings Actions -->
  <div class="col-sm-6 col-xl-3">
    <div class="card card-stats-item h-100">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Active Listings</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ number_format($stats['active_listings']) }}</h4>
              @if($stats['pending_listings'] > 0)
                <span class="badge bg-label-warning rounded-pill">{{ $stats['pending_listings'] }} pending</span>
              @endif
            </div>
            <small class="text-muted mb-0">Total: {{ number_format($stats['listings']) }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-info">
              <i class="icon-base ti tabler-car icon-28px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Users Stats -->
  <div class="col-sm-6 col-xl-3">
    <div class="card card-stats-item h-100">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Users</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ number_format($stats['users']) }}</h4>
              @if($stats['today_users'] > 0)
                <span class="badge bg-label-success rounded-pill">+{{ $stats['today_users'] }} new</span>
              @endif
            </div>
            <small class="text-muted mb-0">{{ $stats['sellers'] }} Verified Sellers</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="icon-base ti tabler-users icon-28px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Pending Approvals -->
  <div class="col-sm-6 col-xl-3">
    <div class="card card-stats-item h-100">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Pending Sellers</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['pending_sellers'] }}</h4>
              @if($stats['pending_sellers'] > 0)
                <span class="badge bg-label-danger rounded-pill">Action Req.</span>
              @else
                <span class="badge bg-label-success rounded-pill">All Clear</span>
              @endif
            </div>
            <small class="text-muted mb-0">Awaiting verification</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="icon-base ti tabler-user-exclamation icon-28px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0">Revenue & Growth Overview</h5>
          <small class="text-muted">Last 7 days performance</small>
        </div>
        <div class="dropdown">
           <button class="btn p-0" type="button" id="revenueMenu" data-bs-toggle="dropdown"><i class="ti tabler-dots-vertical"></i></button>
           <div class="dropdown-menu dropdown-menu-end">
             <a class="dropdown-item" href="javascript:void(0);">Last 30 Days</a>
             <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
             <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
           </div>
        </div>
      </div>
      <div class="card-body">
        <div id="revenueChart" style="min-height: 300px;"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header">
         <h5 class="card-title mb-0">System Health</h5>
      </div>
      <div class="card-body">
         <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
               <div class="avatar rounded bg-label-primary me-3 d-flex align-items-center justify-content-center">
                  <i class="ti tabler-server icon-24px"></i>
               </div>
               <div>
                  <h6 class="mb-0">System Status</h6>
                  <small class="text-muted">Database & Core</small>
               </div>
            </div>
            <span class="badge bg-label-success">Operational</span>
         </div>
         <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
               <div class="avatar rounded bg-label-info me-3 d-flex align-items-center justify-content-center">
                  <i class="ti tabler-database icon-24px"></i>
               </div>
               <div>
                  <h6 class="mb-0">Data Points</h6>
                  <small class="text-muted">Makes, Models, Types</small>
               </div>
            </div>
            <span class="fw-bold">{{ number_format($stats['makes'] + $stats['models'] + $stats['types']) }}</span>
         </div>
         <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
               <div class="avatar rounded bg-label-warning me-3 d-flex align-items-center justify-content-center">
                  <i class="ti tabler-click icon-24px"></i>
               </div>
               <div>
                  <h6 class="mb-0">Today's Leads</h6>
                  <small class="text-muted">User interactions</small>
               </div>
            </div>
            <span class="fw-bold">{{ $stats['today_leads'] }}</span>
         </div>
         <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
               <div class="avatar rounded bg-label-secondary me-3 d-flex align-items-center justify-content-center">
                  <i class="ti tabler-eye icon-24px"></i>
               </div>
               <div>
                  <h6 class="mb-0">Total Views</h6>
                  <small class="text-muted">All time listings</small>
               </div>
            </div>
            <span class="fw-bold">{{ number_format($stats['total_views']) }}</span>
         </div>
         
         <hr class="my-4">
         
         <div class="bg-lighter p-3 rounded">
            <h6 class="mb-2">Storage Usage</h6>
            <div class="progress" style="height: 8px;">
               <div class="progress-bar" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
               <small class="text-muted">12GB Used</small>
               <small class="text-muted">100GB Total</small>
            </div>
         </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Listings & Top Performers -->
<div class="row g-4">
  <!-- Recent Listings -->
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Recent Listings</h5>
        <a href="{{ route('admin.listings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Vehicle</th>
              <th>Seller</th>
              <th>Price</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse($recentListings as $listing)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <img src="{{ $listing->main_image ?? asset('assets/img/placeholder_car.jpg') }}" alt="" class="table-listing-img me-3">
                  <div class="d-flex flex-column">
                    <span class="fw-semibold text-heading">{{ Str::limit($listing->title, 30) }}</span>
                    <small class="text-muted">{{ $listing->year }} • {{ $listing->mileage }}km</small>
                  </div>
                </div>
              </td>
              <td>
                <div class="d-flex flex-column">
                  <span class="fw-semibold">{{ $listing->user->name ?? 'Unknown' }}</span>
                  <small class="text-muted">{{ $listing->user->email ?? '' }}</small>
                </div>
              </td>
              <td><span class="fw-bold">{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</span></td>
              <td>
                <span class="badge bg-label-{{ $listing->status == 'active' ? 'success' : ($listing->status == 'pending' ? 'warning' : 'secondary') }}">
                  {{ ucfirst($listing->status) }}
                </span>
              </td>
              <td><small class="text-muted">{{ $listing->created_at->diffForHumans() }}</small></td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center py-4">
                <div class="d-flex flex-column align-items-center">
                  <i class="ti tabler-files text-muted fs-1 mb-2"></i>
                  <p class="text-muted mb-0">No listings found</p>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Top Performers -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Top Performing</h5>
        <small class="text-muted">Most viewed listings</small>
      </div>
      <div class="card-body">
        <ul class="list-unstyled mb-0">
          @forelse($topListings as $listing)
          <li class="d-flex align-items-center mb-4">
            <div class="d-flex align-items-start">
              <span class="badge bg-label-primary rounded p-2 me-3">
                 <i class="ti tabler-trophy"></i>
              </span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">{{ Str::limit($listing->title, 20) }}</h6>
                <small class="text-muted">{{ $listing->views_count }} views</small>
              </div>
              <div class="user-progress">
                <p class="mb-0 fw-semibold">{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</p>
              </div>
            </div>
          </li>
          @empty
          <li class="text-center text-muted">No data available</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // We are switching from Chart.js to ApexCharts for better visuals
    const revenueData = @json($revenueTrend);
    
    // Prepare data
    const dates = Object.keys(revenueData).map(date => {
       const d = new Date(date);
       return d.getDate() + ' ' + d.toLocaleString('default', { month: 'short' });
    });
    const values = Object.values(revenueData);

    const chartConfig = {
        series: [{
            name: 'Revenue',
            data: values
        }],
        chart: {
            height: 300,
            type: 'area',
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: dates,
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
           labels: {
              formatter: function (value) {
                 return "₽" + value;
              }
           }
        },
        colors: ['#7367F0'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        grid: {
           borderColor: '#f1f1f1',
           strokeDashArray: 4,
           padding: { top: 0, right: 0, bottom: 0, left: 10 }
        },
        tooltip: {
           y: {
              formatter: function(val) {
                 return "₽" + val
              }
           }
        }
    };

    const chart = new ApexCharts(document.querySelector("#revenueChart"), chartConfig);
    chart.render();
});
</script>
@endsection
