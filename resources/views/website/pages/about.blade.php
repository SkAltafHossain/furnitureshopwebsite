@extends('website.layouts.app')

@section('title', 'About Us')

@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary">About Local Shop</h1>
                <p class="lead text-muted">Your trusted local e-commerce store since 2024</p>
            </div>
            
            <!-- Our Story -->
            <section class="mb-5">
                <h2 class="h3 mb-4">Our Story</h2>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-3">
                            Welcome to Local Shop, your premier destination for quality products at affordable prices. 
                            Founded in 2024, we started with a simple mission: to bring the best local products directly 
                            to your doorstep.
                        </p>
                        <p class="mb-3">
                            What began as a small local store has grown into a comprehensive e-commerce platform, 
                            serving thousands of satisfied customers across the region. We believe in the power of 
                            local businesses and strive to showcase the best products our community has to offer.
                        </p>
                        <p>
                            Our commitment to quality, customer satisfaction, and community support remains at the 
                            heart of everything we do. Every product in our catalog is carefully selected to meet 
                            our high standards for quality and value.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded p-4 text-center">
                            <i class="fas fa-store fa-4x text-primary mb-3"></i>
                            <h4>From Local Store to Digital Platform</h4>
                            <p class="text-muted">Bringing local products to the digital age</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Our Mission -->
            <section class="mb-5">
                <h2 class="h3 mb-4">Our Mission</h2>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <i class="fas fa-bullseye fa-3x text-primary mb-3"></i>
                                <h5>Quality First</h5>
                                <p class="text-muted">We never compromise on quality. Every product is carefully selected and tested to ensure it meets our high standards.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                                <h5>Customer Satisfaction</h5>
                                <p class="text-muted">Your satisfaction is our top priority. We go above and beyond to ensure you have the best shopping experience.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <i class="fas fa-handshake fa-3x text-success mb-3"></i>
                                <h5>Community Support</h5>
                                <p class="text-muted">We support local producers and businesses, helping our community thrive while bringing you the best local products.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Our Values -->
            <section class="mb-5">
                <h2 class="h3 mb-4">Our Values</h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                            <div>
                                <h5>Integrity</h5>
                                <p class="text-muted">We conduct business with honesty and transparency. What you see is what you get.</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                            <div>
                                <h5>Innovation</h5>
                                <p class="text-muted">We continuously improve our services and embrace new technologies to serve you better.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                            <div>
                                <h5>Sustainability</h5>
                                <p class="text-muted">We're committed to environmentally responsible practices and sustainable growth.</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                            <div>
                                <h5>Excellence</h5>
                                <p class="text-muted">We strive for excellence in everything we do, from product selection to customer service.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Team -->
            <section class="mb-5">
                <h2 class="h3 mb-4">Meet Our Team</h2>
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <div class="team-member">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                            <h5>John Doe</h5>
                            <p class="text-muted">Founder & CEO</p>
                            <p class="small">Visionary leader with 10+ years in e-commerce</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="team-member">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                            <h5>Jane Smith</h5>
                            <p class="text-muted">Operations Manager</p>
                            <p class="small">Ensuring smooth operations and customer satisfaction</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="team-member">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                            <h5>Mike Johnson</h5>
                            <p class="text-muted">Customer Support Lead</p>
                            <p class="small">Dedicated to providing exceptional customer service</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Stats -->
            <section class="mb-5">
                <div class="bg-light rounded p-4">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <h3 class="text-primary">10,000+</h3>
                            <p class="text-muted">Happy Customers</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h3 class="text-primary">500+</h3>
                            <p class="text-muted">Products Available</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h3 class="text-primary">50+</h3>
                            <p class="text-muted">Local Partners</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h3 class="text-primary">4.8★</h3>
                            <p class="text-muted">Average Rating</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Contact -->
            <section>
                <h2 class="h3 mb-4">Get in Touch</h2>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Visit Our Store</h5>
                        <p class="text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>123 Main Street, City Center<br>
                            <i class="fas fa-phone me-2"></i>+1234567890<br>
                            <i class="fas fa-envelope me-2"></i>info@localshop.com
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>Business Hours</h5>
                        <p class="text-muted">
                            Monday - Friday: 9:00 AM - 8:00 PM<br>
                            Saturday: 10:00 AM - 6:00 PM<br>
                            Sunday: 11:00 AM - 4:00 PM
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
