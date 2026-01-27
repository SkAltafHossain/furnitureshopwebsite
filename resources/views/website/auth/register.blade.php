@extends('website.layouts.app')

@section('title', 'Register')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Create Your Account</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1234567890">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Password must be at least 8 characters long</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="{{ route('page.terms') }}" target="_blank">Terms & Conditions</a> 
                                and <a href="{{ route('page.privacy') }}" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Create Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
                    </div>
                </div>
            </div>
            
            <!-- Benefits -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">Benefits of Your Account</h6>
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-check text-success me-2"></i> Track your orders in real-time</li>
                        <li><i class="fas fa-check text-success me-2"></i> Save multiple shipping addresses</li>
                        <li><i class="fas fa-check text-success me-2"></i> Quick checkout process</li>
                        <li><i class="fas fa-check text-success me-2"></i> View complete order history</li>
                        <li><i class="fas fa-check text-success me-2"></i> Receive exclusive offers</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Password confirmation validation
    $('form').submit(function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#password_confirmation').val();
        
        if (password !== confirmPassword) {
            e.preventDefault();
            showToast('Passwords do not match', 'error');
            return false;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            showToast('Password must be at least 8 characters long', 'error');
            return false;
        }
        
        if (!$('#terms').is(':checked')) {
            e.preventDefault();
            showToast('Please accept the terms and conditions', 'error');
            return false;
        }
    });
    
    // Phone number formatting
    $('#phone').on('input', function() {
        var value = $(this).val();
        // Remove non-numeric characters except + and spaces
        var formatted = value.replace(/[^0-9+\s]/g, '');
        $(this).val(formatted);
    });
});
</script>
@endpush
