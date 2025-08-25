@extends('layouts.main')

@section('title', 'Welcome | PiSystem')

@section('content')
    <div class="welcome">
        <h2>Welcome to PiSystem</h2>
        <p>
            PiSystem is your centralized platform for managing modules, reports, and system settings.  
            Use the navigation above to explore available features.
        </p>
        
        <div class="quick-links mb-3">
            <a href="/modules">Go to Modules</a> |
            <a href="/reports">View Reports</a> |
            <a href="/settings">System Settings</a>
        </div>

        <a href="{{ route('certificate.create') }}" class="btn btn-success">
            <i class="fa fa-certificate me-1"></i>
            Create Certificate
        </a>
        <a href="{{ route('certificate.bulk') }}" class="btn btn-success">
            <i class="fa fa-certificate me-1"></i>
            Create Certificate
        </a>
    </div>
@endsection

@push('scripts')
<script>
    console.log("Welcome page loaded in PiSystem");
</script>
@endpush
