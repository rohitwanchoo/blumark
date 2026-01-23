<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('smtp-settings.index') }}"
                   class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">SMTP Setup Guide</h1>
            </div>
            <p class="text-gray-500">Easy instructions for setting up email delivery</p>
        </div>

        <!-- Gmail App Password Section -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Gmail with App Password (Recommended)</h2>
                    <p class="text-gray-600 mb-4">The easiest way to send emails through Gmail. Perfect for individuals and small businesses.</p>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800 font-semibold">✓ Why use this method?</p>
                <ul class="mt-2 text-sm text-blue-700 space-y-1 ml-4 list-disc">
                    <li>No technical setup required</li>
                    <li>Works in minutes</li>
                    <li>No Google Cloud account needed</li>
                    <li>Free for personal Gmail accounts</li>
                </ul>
            </div>

            <h3 class="font-semibold text-gray-900 mb-3">Step-by-Step Instructions:</h3>

            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 mb-2">Enable 2-Step Verification ⚠️ REQUIRED FIRST</h4>
                        <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded">
                            <p class="text-sm text-red-800 font-semibold">🚨 If you skip this step, you'll see "The setting you are looking for is not available" in Step 2.</p>
                        </div>
                        <p class="text-gray-600 mb-2">App Passwords ONLY work if 2-Step Verification is enabled. Here's how:</p>
                        <ol class="text-sm text-gray-600 space-y-2 ml-4 list-decimal">
                            <li>Go to <a href="https://myaccount.google.com/security" target="_blank" class="text-blue-600 hover:underline font-medium">myaccount.google.com/security</a></li>
                            <li>Scroll down to "How you sign in to Google"</li>
                            <li>Find "2-Step Verification" and click <strong>"Get Started"</strong> (or it may show as "Turn on")</li>
                            <li>Follow the setup wizard - you'll need to verify with your phone number</li>
                            <li>Complete the setup until you see a confirmation that 2-Step Verification is ON</li>
                        </ol>
                        <div class="mt-3 p-3 bg-green-50 rounded border border-green-200">
                            <p class="text-xs text-green-800">✅ <strong>How to know it's working:</strong> You'll see "2-Step Verification is on" with a blue shield icon.</p>
                        </div>
                        <div class="mt-2 p-3 bg-gray-50 rounded border border-gray-200">
                            <p class="text-xs text-gray-600">💡 <strong>Already enabled?</strong> If you see a blue checkmark or "On" status, skip to Step 2.</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">2</div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 mb-2">Create an App Password</h4>
                        <p class="text-gray-600 mb-2">This generates a special password just for this app.</p>
                        <ol class="text-sm text-gray-600 space-y-1 ml-4 list-disc">
                            <li>Visit <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-blue-600 hover:underline font-medium">myaccount.google.com/apppasswords</a></li>
                            <li>You might need to sign in again</li>
                            <li>Under "Select app", choose "Mail" (or "Other")</li>
                            <li>Under "Select device", choose "Other (Custom name)"</li>
                            <li>Type "Watermarking App" or any name you prefer</li>
                            <li>Click "Generate"</li>
                        </ol>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">3</div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 mb-2">Copy the Password</h4>
                        <p class="text-gray-600 mb-2">Google will display a 16-character password in a yellow box.</p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-2">
                            <p class="text-sm font-mono text-center text-gray-900">abcd efgh ijkl mnop</p>
                            <p class="text-xs text-gray-600 text-center mt-1">Example app password</p>
                        </div>
                        <p class="text-sm text-gray-600">Click the copy icon or select and copy this password. You won't be able to see it again!</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">4</div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 mb-2">Configure in the App</h4>
                        <p class="text-gray-600 mb-2">Return to the SMTP settings and fill in the form:</p>
                        <ul class="text-sm text-gray-600 space-y-1 ml-4 list-disc">
                            <li><strong>Gmail Address:</strong> Your full email (e.g., yourname@gmail.com)</li>
                            <li><strong>App Password:</strong> Paste the 16-character password from Step 3</li>
                            <li><strong>From Name:</strong> Your name or company name (what recipients will see)</li>
                        </ul>
                        <a href="{{ route('smtp-settings.providers') }}"
                           class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            Go to Setup Form
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Troubleshooting Section -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Common Issues & Solutions</h2>

            <div class="space-y-4">
                <div class="border-l-4 border-red-500 pl-4 py-2">
                    <h4 class="font-semibold text-gray-900 mb-1">"Username and Password not accepted"</h4>
                    <p class="text-sm text-gray-600">You're using your regular Gmail password. You must use an App Password instead (see steps above).</p>
                </div>

                <div class="border-l-4 border-red-500 pl-4 py-2">
                    <h4 class="font-semibold text-gray-900 mb-1">"The setting you are looking for is not available"</h4>
                    <div class="text-sm text-gray-600 space-y-2">
                        <p><strong>This means 2-Step Verification is NOT enabled yet.</strong> You must enable it first:</p>
                        <ol class="list-decimal ml-4 space-y-1">
                            <li>Go to <a href="https://myaccount.google.com/security" target="_blank" class="text-blue-600 hover:underline font-medium">myaccount.google.com/security</a></li>
                            <li>Find "2-Step Verification" and click "Get Started"</li>
                            <li>Follow the setup (you'll verify with your phone)</li>
                            <li>After enabling 2FA, THEN visit <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-blue-600 hover:underline font-medium">App Passwords</a></li>
                        </ol>
                        <p class="mt-2 italic">Note: Without 2-Step Verification, Google won't allow App Passwords for security reasons.</p>
                    </div>
                </div>

                <div class="border-l-4 border-yellow-500 pl-4 py-2">
                    <h4 class="font-semibold text-gray-900 mb-1">Can I use a Google Workspace email?</h4>
                    <p class="text-sm text-gray-600">Yes! The same steps work for Google Workspace (formerly G Suite) accounts. However, your admin must allow App Passwords in your organization's settings.</p>
                </div>

                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <h4 class="font-semibold text-gray-900 mb-1">How do I revoke access later?</h4>
                    <p class="text-sm text-gray-600">Go to <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-blue-600 hover:underline">myaccount.google.com/apppasswords</a> and click "Remove" next to the app password you created.</p>
                </div>
            </div>
        </div>

        <!-- Other Providers Section -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Other Email Providers</h2>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Microsoft 365 / Outlook</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><strong>Host:</strong> smtp.office365.com</li>
                        <li><strong>Port:</strong> 587</li>
                        <li><strong>Encryption:</strong> TLS</li>
                        <li><strong>Note:</strong> Use your full email and password</li>
                    </ul>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">SendGrid</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><strong>Host:</strong> smtp.sendgrid.net</li>
                        <li><strong>Port:</strong> 587</li>
                        <li><strong>Username:</strong> apikey</li>
                        <li><strong>Password:</strong> Your SendGrid API key</li>
                    </ul>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Mailgun</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><strong>Host:</strong> smtp.mailgun.org</li>
                        <li><strong>Port:</strong> 587</li>
                        <li><strong>Encryption:</strong> TLS</li>
                        <li><strong>Note:</strong> Get credentials from Mailgun dashboard</li>
                    </ul>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Custom SMTP</h3>
                    <p class="text-sm text-gray-600">Contact your email provider or IT administrator for SMTP settings.</p>
                </div>
            </div>
        </div>

        <!-- Need Help Section -->
        <div class="mt-6 p-6 bg-gray-50 rounded-lg border border-gray-200 text-center">
            <p class="text-gray-700">
                <strong>Still need help?</strong> Contact your IT administrator or
                <a href="mailto:support@example.com" class="text-blue-600 hover:underline">contact support</a>
            </p>
        </div>
    </div>
</x-app-layout>
