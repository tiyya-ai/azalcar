@extends('layouts/contentNavbarLayout')

@section('title', 'Transactions - Admin')

@section('content')
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Revenue</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">₽ {{ number_format($totalRevenue, 2) }}</h4>
            </div>
            <small class="mb-0">All time revenue</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="ti tabler-currency-rubel icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Transactions</h5>
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
      <div class="col-md-4">
        <form action="{{ route('admin.transactions.index') }}" method="GET">
            <select name="type" class="form-select text-capitalize" onchange="this.form.submit()">
              <option value=""> All Transactions </option>
              <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Top-ups (Credit)</option>
              <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Purchases (Debit)</option>
            </select>
        </form>
      </div>
      <div class="col-md-4 user_role"></div>
      <div class="col-md-4">
          <form action="{{ route('admin.transactions.index') }}" method="GET">
             <div class="input-group">
                 <input type="text" name="search" class="form-control" placeholder="Search Transaction ID or User..." value="{{ request('search') }}">
                 <button class="btn btn-outline-primary" type="submit">Search</button>
             </div>
          </form>
      </div>
    </div>
  </div>
  <div class="table-responsive text-nowrap">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Amount</th>
          <th>Type</th>
          <th>Method</th>
          <th>Reference</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $trx)
        <tr>
          <td><a href="#">#{{ $trx->id }}</a></td>
          <td>
             <div class="d-flex flex-column">
                  <a href="{{ route('admin.users.show', $trx->user_id) }}" class="text-body text-truncate"><span class="fw-medium">{{ $trx->user->name }}</span></a>
                  <small class="text-muted">{{ $trx->user->email }}</small>
              </div>
          </td>
          <td class="{{ $trx->type == 'credit' ? 'text-success' : 'text-danger' }}">
              {{ $trx->type == 'credit' ? '+' : '-' }} ₽{{ number_format($trx->amount, 2) }}
          </td>
          <td>
              @if($trx->type == 'credit')
                  <span class="badge bg-label-success">Credit</span>
              @else
                  <span class="badge bg-label-primary">Debit</span>
              @endif
          </td>
          <td>{{ ucfirst($trx->payment_method ?? 'System') }}</td>
          <td>{{ $trx->reference_id ?? '-' }}</td>
          <td>{{ $trx->created_at->format('M d, Y H:i') }}</td>
          <td><span class="badge bg-label-secondary">Completed</span></td>
        </tr>
        @empty
        <tr>
             <td colspan="8" class="text-center">No transactions found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-4">
      {{ $transactions->links() }}
  </div>
</div>
@endsection
