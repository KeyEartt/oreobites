@extends('layouts.app')

@section('title', 'Privacy Policy — Oreo Bites')
@section('description', 'How Oreo Bites collects, uses, and protects your personal information.')

@section('content')

{{-- Header --}}
<section class="bg-white border-b border-stone-200">
    <div class="container-app py-10 md:py-14">
        <div class="max-w-3xl">
            <nav class="flex items-center gap-2 text-xs text-stone-500 mb-3">
                <a href="/" class="hover:text-cookie-brown">Home</a>
                <span>/</span>
                <span class="text-cookie-brown font-medium">Privacy Policy</span>
            </nav>
            <h1 class="font-display font-extrabold text-3xl md:text-4xl text-oreo-noir">
                Privacy Policy
            </h1>
            <p class="text-stone-500 mt-2 text-sm">
                Last updated: <span class="font-medium text-cookie-brown">{{ $lastUpdated }}</span>
            </p>
        </div>
    </div>
</section>

{{-- Body --}}
<section class="container-app py-10 md:py-14">
    <div class="max-w-3xl mx-auto">
        <article class="card p-6 md:p-10 space-y-8 text-cookie-brown leading-relaxed">

            {{-- Intro --}}
            <div class="rounded-xl bg-milk-cream border border-stone-200 p-5">
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-full bg-white border border-stone-200 flex items-center justify-center text-cookie-brown shrink-0">
                        <x-icon name="login" class="w-4 h-4" />
                    </div>
                    <div class="text-sm">
                        <p class="font-display font-bold text-oreo-noir">Your Privacy Matters</p>
                        <p class="text-stone-600 mt-1">
                            We collect only the information needed to process your order, and we never sell your data. This policy explains what we collect, why, and how we protect it.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Section 1 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">1. Information We Collect</h2>
                <p class="mb-4">When you place an order or create an account, we collect:</p>

                <div class="rounded-xl border border-stone-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-milk-cream">
                            <tr class="text-left">
                                <th class="px-4 py-3 font-semibold text-oreo-noir">Data</th>
                                <th class="px-4 py-3 font-semibold text-oreo-noir">Why we need it</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <tr>
                                <td class="px-4 py-3 font-medium text-oreo-noir">Full name</td>
                                <td class="px-4 py-3">To identify your order at pickup</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-oreo-noir">Email address</td>
                                <td class="px-4 py-3">To send order confirmations and account notifications</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-oreo-noir">Phone number</td>
                                <td class="px-4 py-3">To contact you about your order if needed</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-oreo-noir">Delivery address</td>
                                <td class="px-4 py-3">Only if you select a delivery option (not pickup)</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-oreo-noir">Order history</td>
                                <td class="px-4 py-3">To show you past orders and manage stock</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-oreo-noir">Password (hashed)</td>
                                <td class="px-4 py-3">For account login — stored as a one-way bcrypt hash</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-sm">
                    <strong class="text-oreo-noir">We do NOT collect</strong> your GCash account number, credit card details, or any payment credentials. All payment processing is handled directly by PayMongo.
                </p>
            </div>

            {{-- Section 2 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">2. How We Use Your Information</h2>
                <ul class="space-y-2">
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span><strong>Process orders</strong> — to prepare your item and coordinate pickup or delivery.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span><strong>Communicate with you</strong> — order confirmations, status updates, and (if you opt in) occasional updates.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span><strong>Maintain your account</strong> — so you can view past orders and check out faster.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span><strong>Improve the service</strong> — understand which products are popular and how the site is used in aggregate.</span>
                    </li>
                </ul>
            </div>

            {{-- Section 3 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">3. Third-Party Services</h2>
                <p class="mb-3">We share the minimum necessary data with these trusted providers:</p>

                <div class="space-y-3">
                    <div class="rounded-xl border border-stone-200 p-4">
                        <p class="font-semibold text-oreo-noir text-sm">PayMongo</p>
                        <p class="text-sm text-stone-600 mt-1">
                            Payment processor. Receives your order amount, name, and email to complete the GCash transaction.
                            <a href="https://www.paymongo.com/privacy" target="_blank" class="text-chocolate hover:underline font-medium ml-1">Privacy policy →</a>
                        </p>
                    </div>

                    <div class="rounded-xl border border-stone-200 p-4">
                        <p class="font-semibold text-oreo-noir text-sm">Supabase</p>
                        <p class="text-sm text-stone-600 mt-1">
                            Database and authentication provider. Stores your order details and account credentials (hashed) in encrypted form.
                            <a href="https://supabase.com/privacy" target="_blank" class="text-chocolate hover:underline font-medium ml-1">Privacy policy →</a>
                        </p>
                    </div>
                </div>

                <p class="mt-4 text-sm">
                    We do <strong>not</strong> sell, rent, or share your personal information with any advertiser, data broker, or third party outside the providers listed above.
                </p>
            </div>

            {{-- Section 4 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">4. Cookies &amp; Local Storage</h2>
                <p>
                    We use your browser's <strong>localStorage</strong> to remember your shopping cart between visits. We do not use tracking cookies, ad pixels, or third-party analytics. Clearing your browser data will reset your cart.
                </p>
            </div>

            {{-- Section 5 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">5. Data Retention</h2>
                <p>
                    Order records are retained for up to <strong>1 year</strong> for accounting and inventory purposes. Account data is retained until you delete your account. Abandoned carts are cleared after 30 days.
                </p>
            </div>

            {{-- Section 6 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">6. Your Rights</h2>
                <p class="mb-3">Under the <strong>Data Privacy Act of 2012 (RA 10173)</strong>, you have the right to:</p>
                <ul class="space-y-2">
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span><strong>Access</strong> — request a copy of the personal data we hold about you.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span><strong>Correct</strong> — update inaccurate or incomplete information.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span><strong>Delete</strong> — request erasure of your account and personal data.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span><strong>Object</strong> — opt out of non-essential communications.</span>
                    </li>
                </ul>
                <p class="mt-3 text-sm">
                    To exercise any of these rights, email us at the address in the Contact section below. We will respond within 15 business days.
                </p>
            </div>

            {{-- Section 7 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">7. Security</h2>
                <p>
                    All traffic to this site is encrypted via HTTPS. Account passwords are hashed using bcrypt and are never stored or transmitted in plain text. Payment data never touches our servers — it goes directly to PayMongo. While no system is 100% secure, we follow industry best practices to protect your information.
                </p>
            </div>

            {{-- Section 8 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">8. Children's Privacy</h2>
                <p>
                    This Platform is not directed to children under 13. We do not knowingly collect personal information from children under 13. If you believe a child has provided us with personal data, please contact us so we can delete it.
                </p>
            </div>

            {{-- Section 9 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">9. Changes to This Policy</h2>
                <p>
                    We may update this Privacy Policy from time to time. The "Last updated" date at the top of this page indicates the most recent revision. We will notify registered users of material changes via email.
                </p>
            </div>

            {{-- Section 10 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">10. Contact</h2>
                <p class="mb-4">
                    For privacy-related questions, data access requests, or complaints:
                </p>
                <div class="rounded-xl bg-milk-cream border border-stone-200 p-4 space-y-2 text-sm">
                    <p class="flex items-center gap-3">
                        <x-icon name="location" class="w-4 h-4 text-chocolate shrink-0" />
                        <span>University of Caloocan City — Congressional Campus</span>
                    </p>
                    <p class="flex items-center gap-3">
                        <x-icon name="login" class="w-4 h-4 text-chocolate shrink-0" />
                        <span>oreobites@ucc.edu.ph</span>
                    </p>
                </div>
                <p class="mt-3 text-sm">
                    If you are unsatisfied with our response, you may file a complaint with the
                    <a href="https://www.privacy.gov.ph" target="_blank" class="text-chocolate hover:underline font-medium">National Privacy Commission</a>.
                </p>
            </div>

        </article>

        {{-- Related links --}}
        <div class="mt-6 flex flex-wrap gap-3 justify-center">
            <a href="/terms" class="btn-secondary">
                <x-icon name="orders" class="w-4 h-4" />
                Read our Terms
            </a>
            <a href="/" class="btn-ghost">
                Back to Home
            </a>
        </div>
    </div>
</section>

@endsection