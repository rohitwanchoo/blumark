<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BluMark Privacy Policy - How we collect, use, and protect your data.">
    <title>Privacy Policy - {{ config('app.name', 'BluMark') }}</title>

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
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Privacy Policy</h1>
                <p class="text-gray-600">Last updated: {{ date('F j, Y') }}</p>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 md:p-12 prose prose-gray max-w-none">
                <h2 class="text-2xl font-bold text-gray-900 mt-0">1. Introduction</h2>
                <p>
                    Welcome to BluMark ("we," "our," or "us"). We are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our PDF watermarking service.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">2. Information We Collect</h2>

                <h3 class="text-xl font-semibold text-gray-900">2.1 Account Information</h3>
                <p>When you create an account, we collect:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Name</li>
                    <li>Email address</li>
                    <li>Password (encrypted)</li>
                    <li>OAuth authentication data (if using social login)</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-900">2.2 Document Data</h3>
                <p>When you use our watermarking service:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>PDF files you upload for watermarking</li>
                    <li>Watermark text (ISO and Lender names)</li>
                    <li>Watermark settings (font size, color, opacity)</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-900">2.3 Usage Information</h3>
                <p>We automatically collect:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>IP address</li>
                    <li>Browser type and version</li>
                    <li>Device information</li>
                    <li>Pages visited and features used</li>
                    <li>Timestamps of activity</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">3. How We Use Your Information</h2>
                <p>We use the collected information to:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Provide and maintain our watermarking service</li>
                    <li>Process your PDF files and apply watermarks</li>
                    <li>Manage your account and provide customer support</li>
                    <li>Send service-related communications</li>
                    <li>Improve our service and develop new features</li>
                    <li>Detect and prevent fraud or abuse</li>
                    <li>Comply with legal obligations</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">4. Data Retention</h2>
                <p>
                    <strong>Uploaded Files:</strong> Your uploaded PDF files and watermarked output files are automatically deleted after <strong>{{ config('watermark.retention_days', 7) }} days</strong>. We do not retain copies of your documents beyond this period.
                </p>
                <p>
                    <strong>Account Data:</strong> We retain your account information for as long as your account is active. You may request deletion of your account at any time.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">5. Data Security</h2>
                <p>We implement industry-standard security measures to protect your data:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Encryption:</strong> All data transfers are encrypted using 256-bit SSL/TLS encryption</li>
                    <li><strong>Secure Storage:</strong> Files are stored on secure servers with restricted access</li>
                    <li><strong>Password Protection:</strong> Passwords are hashed using industry-standard algorithms</li>
                    <li><strong>Regular Audits:</strong> We conduct regular security assessments</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">6. Data Sharing</h2>
                <p>We do not sell, trade, or rent your personal information. We may share data only in these circumstances:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Service Providers:</strong> With trusted third parties who assist in operating our service (e.g., payment processors)</li>
                    <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
                    <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">7. Your Rights</h2>
                <p>You have the right to:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Access your personal information</li>
                    <li>Correct inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Export your data in a portable format</li>
                    <li>Opt-out of marketing communications</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900">8. Cookies</h2>
                <p>
                    We use essential cookies to maintain your session and provide core functionality. These cookies are necessary for the service to function properly. We do not use tracking or advertising cookies.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">9. Third-Party Services</h2>
                <p>Our service may integrate with third-party services:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Google OAuth:</strong> For social login authentication</li>
                    <li><strong>Stripe:</strong> For payment processing (if applicable)</li>
                </ul>
                <p>These services have their own privacy policies, and we encourage you to review them.</p>

                <h2 class="text-2xl font-bold text-gray-900">10. Children's Privacy</h2>
                <p>
                    Our service is not intended for individuals under 18 years of age. We do not knowingly collect personal information from children.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">11. Changes to This Policy</h2>
                <p>
                    We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last updated" date.
                </p>

                <h2 class="text-2xl font-bold text-gray-900">12. Contact Us</h2>
                <p>
                    If you have questions about this Privacy Policy or our data practices, please contact us at:
                </p>
                <p class="bg-gray-50 p-4 rounded-lg">
                    <strong>BluMark</strong><br>
                    Email: privacy@blumark.pro
                </p>
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
                    <a href="{{ route('privacy') }}" class="text-primary-400 hover:text-primary-300 transition-colors">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
