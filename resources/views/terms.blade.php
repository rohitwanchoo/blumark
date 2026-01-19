<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BluMark Terms of Service - Terms and conditions for using our PDF watermarking service.">
    <title>Terms of Service - {{ config('app.name', 'BluMark') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                        dark: {
                            900: '#0f1419',
                            800: '#1a1f26',
                            700: '#2d333b',
                        }
                    },
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-dark-900 border-b border-dark-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">Blu<span class="text-primary-400">Mark</span></span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/#features" class="text-gray-300 hover:text-white text-sm font-medium transition-colors">Features</a>
                    <a href="/#how-it-works" class="text-gray-300 hover:text-white text-sm font-medium transition-colors">How It Works</a>
                    <a href="/#use-cases" class="text-gray-300 hover:text-white text-sm font-medium transition-colors">Use Cases</a>
                    <a href="/#security" class="text-gray-300 hover:text-white text-sm font-medium transition-colors">Security</a>
                    <a href="/docs/api" class="text-gray-300 hover:text-white text-sm font-medium transition-colors">Developers</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-all duration-200">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-sm font-medium transition-colors hidden sm:block">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-all duration-200">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Terms of Service</h1>
                <p class="text-gray-600">Last updated: {{ date('F j, Y') }}</p>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 md:p-12 prose prose-gray max-w-none">
                <h2 class="text-2xl font-bold text-gray-900 mt-0">1. Acceptance of Terms</h2>
                <p>
                    By accessing or using BluMark ("Service"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree to these Terms, please do not use our Service.
                </p>
                <p>
                    These Terms apply to all users, including ISOs, brokers, lenders, and any other parties using our PDF watermarking service.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">2. Description of Service</h2>
                <p>
                    BluMark provides a PDF watermarking service designed for the merchant cash advance (MCA) industry. Our Service allows users to:
                </p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Upload PDF documents</li>
                    <li>Apply dual watermarks (ISO name and Lender name) to documents</li>
                    <li>Download watermarked documents</li>
                    <li>Manage watermarking jobs and history</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">3. Account Registration</h2>
                <p>To use our Service, you must:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Be at least 18 years of age</li>
                    <li>Register for an account with accurate information</li>
                    <li>Maintain the security of your account credentials</li>
                    <li>Notify us immediately of any unauthorized access</li>
                </ul>
                <p>
                    You are responsible for all activities that occur under your account.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">4. Acceptable Use</h2>
                <p>You agree to use our Service only for lawful purposes. You must NOT:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Upload documents containing illegal content</li>
                    <li>Use the Service for fraudulent purposes</li>
                    <li>Upload documents you do not have the right to use</li>
                    <li>Attempt to circumvent security measures</li>
                    <li>Interfere with the operation of the Service</li>
                    <li>Use automated systems to access the Service without permission</li>
                    <li>Share your account credentials with others</li>
                    <li>Misrepresent your identity or affiliation</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">5. Document Ownership and Responsibility</h2>
                <p>
                    <strong>Your Documents:</strong> You retain all ownership rights to the documents you upload. By using our Service, you grant us a limited license to process your documents solely for the purpose of applying watermarks.
                </p>
                <p>
                    <strong>Your Responsibility:</strong> You are solely responsible for:
                </p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Ensuring you have the right to upload and watermark the documents</li>
                    <li>The accuracy of the watermark text (ISO and Lender names)</li>
                    <li>Compliance with applicable laws and regulations</li>
                    <li>Proper handling and distribution of watermarked documents</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">6. Data Retention and Deletion</h2>
                <p>
                    Uploaded and processed documents are automatically deleted after <strong>{{ config('watermark.retention_days', 7) }} days</strong>. You should download your watermarked documents promptly, as we do not maintain backups after the retention period.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">7. Service Availability</h2>
                <p>
                    We strive to maintain high availability but do not guarantee uninterrupted access. The Service may be temporarily unavailable due to:
                </p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Scheduled maintenance</li>
                    <li>System updates or upgrades</li>
                    <li>Technical issues beyond our control</li>
                    <li>Force majeure events</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">8. Fees and Payment</h2>
                <p>
                    Certain features of the Service may require payment. By subscribing to a paid plan:
                </p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>You agree to pay all applicable fees</li>
                    <li>Fees are billed in advance on a recurring basis</li>
                    <li>Refunds are provided at our discretion</li>
                    <li>We reserve the right to change pricing with notice</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">9. Intellectual Property</h2>
                <p>
                    The Service, including its design, features, and content (excluding user-uploaded documents), is owned by BluMark and protected by intellectual property laws. You may not copy, modify, or distribute any part of our Service without permission.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">10. Disclaimer of Warranties</h2>
                <p>
                    THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, WHETHER EXPRESS OR IMPLIED. WE DO NOT WARRANT THAT:
                </p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>The Service will meet your specific requirements</li>
                    <li>The Service will be uninterrupted or error-free</li>
                    <li>Watermarks will prevent all unauthorized use of documents</li>
                    <li>The Service will detect or prevent fraud</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">11. Limitation of Liability</h2>
                <p>
                    TO THE MAXIMUM EXTENT PERMITTED BY LAW, BLUMARK SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, INCLUDING BUT NOT LIMITED TO:
                </p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Loss of profits or revenue</li>
                    <li>Loss of data or documents</li>
                    <li>Business interruption</li>
                    <li>Unauthorized access to your documents</li>
                    <li>Any claims arising from your use of the Service</li>
                </ul>
                <p>
                    Our total liability shall not exceed the amount you paid for the Service in the twelve (12) months preceding the claim.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">12. Indemnification</h2>
                <p>
                    You agree to indemnify and hold harmless BluMark and its officers, directors, employees, and agents from any claims, damages, or expenses arising from:
                </p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Your use of the Service</li>
                    <li>Your violation of these Terms</li>
                    <li>Your violation of any third-party rights</li>
                    <li>Documents you upload or distribute</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">13. Termination</h2>
                <p>
                    We may suspend or terminate your account at any time for:
                </p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Violation of these Terms</li>
                    <li>Suspected fraudulent or illegal activity</li>
                    <li>Non-payment of fees</li>
                    <li>Extended periods of inactivity</li>
                </ul>
                <p>
                    You may terminate your account at any time by contacting us. Upon termination, your right to use the Service ceases immediately.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">14. Changes to Terms</h2>
                <p>
                    We reserve the right to modify these Terms at any time. We will notify users of material changes via email or through the Service. Continued use of the Service after changes constitutes acceptance of the modified Terms.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">15. Governing Law</h2>
                <p>
                    These Terms shall be governed by and construed in accordance with the laws of the United States, without regard to conflict of law principles.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">16. Dispute Resolution</h2>
                <p>
                    Any disputes arising from these Terms or your use of the Service shall be resolved through binding arbitration, except where prohibited by law. You waive any right to participate in class action lawsuits.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">17. Severability</h2>
                <p>
                    If any provision of these Terms is found to be unenforceable, the remaining provisions shall continue in full force and effect.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">18. Contact Information</h2>
                <p>
                    For questions about these Terms, please contact us at:
                </p>
                <p class="bg-gray-50 p-4 rounded-lg">
                    <strong>BluMark</strong><br>
                    Email: legal@blumark.pro
                </p>

                <div class="mt-8 p-6 bg-primary-50 rounded-xl border border-primary-200">
                    <p class="text-primary-800 font-medium">
                        By using BluMark, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <a href="/" class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">Blu<span class="text-primary-400">Mark</span></span>
                    </a>
                    <p class="text-sm max-w-md">
                        PDF watermarking built for the MCA industry. Dual watermarks help ISOs get proper credit and lenders get organized submissions. Better for everyone.
                    </p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="/#how-it-works" class="hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="/#use-cases" class="hover:text-white transition-colors">Use Cases</a></li>
                        <li><a href="/#security" class="hover:text-white transition-colors">Security</a></li>
                        <li><a href="/docs/api" class="hover:text-white transition-colors">Developers</a></li>
                    </ul>
                </div>

                <!-- Account -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-sm">
                        @guest
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign In</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Create Account</a></li>
                        @else
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition-colors">My Jobs</a></li>
                        @endguest
                    </ul>
                </div>
            </div>

            <div class="border-t border-dark-700 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm">&copy; {{ date('Y') }} BluMark. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0 text-sm">
                    <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="text-primary-400 hover:text-primary-300 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
