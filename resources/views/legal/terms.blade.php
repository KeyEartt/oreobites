@extends('layouts.app')

@section('title', 'Terms & Conditions — Oreo Bites')
@section('description', 'Terms and conditions for using the Oreo Bites online ordering platform.')

@section('content')

{{-- Header --}}
<section class="bg-white border-b border-stone-200">
    <div class="container-app py-10 md:py-14">
        <div class="max-w-3xl">
            <nav class="flex items-center gap-2 text-xs text-stone-500 mb-3">
                <a href="/" class="hover:text-cookie-brown">Home</a>
                <span>/</span>
                <span class="text-cookie-brown font-medium">Terms &amp; Conditions</span>
            </nav>
            <h1 class="font-display font-extrabold text-3xl md:text-4xl text-oreo-noir">
                Terms &amp; Conditions
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
                        <x-icon name="cookie" class="w-4 h-4" />
                    </div>
                    <div class="text-sm">
                        <p class="font-display font-bold text-oreo-noir">Academic Demonstration Project</p>
                        <p class="text-stone-600 mt-1">
                            Oreo Bites is a student-built platform developed as part of the BS Information System program at the University of Caloocan City — Congressional Campus. By using this website, you acknowledge and agree to the terms outlined below.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Section 1 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">1. About This Platform</h2>
                <p>
                    Oreo Bites ("the Platform", "we", "us") is an academic e-commerce demonstration project created by students of the University of Caloocan City. The Platform allows users to browse, order, and pay for Oreo Cheesecake Bites for pickup at the UCC Congressional Campus.
                </p>
                <p class="mt-3">
                    While the Platform uses real payment infrastructure (PayMongo / GCash) and real product inventory, it primarily serves as a thesis and portfolio demonstration. All transactions are conducted in good faith, and any proceeds go directly to the operating costs of the student-run business.
                </p>
            </div>

            {{-- Section 2 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">2. Eligibility</h2>
                <p>To use this Platform, you must:</p>
                <ul class="mt-3 space-y-2">
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span>Be at least 13 years of age;</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span>Provide accurate, current, and complete information during checkout;</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span>Have access to a valid GCash account or an authorized payment method for completing transactions.</span>
                    </li>
                </ul>
            </div>

            {{-- Section 3 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">3. Orders &amp; Payment</h2>
                <ul class="space-y-2">
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span>All prices are listed in Philippine Peso (₱) and include applicable fees unless stated otherwise.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span>Payments are processed securely via <strong>PayMongo</strong> using GCash. We do not store your payment credentials.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span>An order is confirmed only after successful payment is verified. Unpaid orders expire automatically after 30 minutes.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span>Delivery fees are calculated based on the selected option at checkout and are non-negotiable.</span>
                    </li>
                </ul>
            </div>

            {{-- Section 4 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">4. Pickup &amp; Delivery</h2>
                <p>
                    The default fulfillment method is <strong>campus pickup</strong> at the UCC Congressional Campus Kiosk. Delivery within Caloocan is available for an additional fee. Pickup orders are held for 24 hours after the "Ready" notification. Orders not claimed within this period may be cancelled without refund to prevent food waste.
                </p>
            </div>

            {{-- Section 5 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">5. Refunds &amp; Cancellations</h2>
                <p>
                    Because our products are perishable food items, refunds are issued only in the following cases:
                </p>
                <ul class="mt-3 space-y-2">
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span>Product was not received (order marked "Ready" but item unavailable).</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span>Product was damaged, spoiled, or incorrect upon pickup.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-chocolate mt-2 shrink-0"></span>
                        <span>Duplicate payment was charged due to a system error.</span>
                    </li>
                </ul>
                <p class="mt-3">
                    Refund requests must be made within 24 hours of the "Ready" notification by contacting us via the details in the footer. Approved refunds are processed through the original payment method within 5–7 business days.
                </p>
            </div>

            {{-- Section 6 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">6. Food Allergens</h2>
                <p>
                    Our products contain <strong>milk, eggs, wheat, soy</strong>, and are processed in a facility that handles nuts and other allergens. If you have a food allergy, please contact us before ordering. We cannot guarantee zero cross-contamination.
                </p>
            </div>

            {{-- Section 7 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">7. Intellectual Property</h2>
                <p>
                    All branding, content, and code on this Platform are the property of the Oreo Bites team or used under fair use for academic purposes. The "Oreo" trademark is owned by Mondelez International and is used here for descriptive purposes only — this Platform is not affiliated with or endorsed by Mondelez.
                </p>
            </div>

            {{-- Section 8 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">8. Limitation of Liability</h2>
                <p>
                    As an academic demonstration project, this Platform is provided "as is" without warranty of any kind. To the fullest extent permitted by law, we are not liable for any indirect, incidental, or consequential damages arising from your use of the Platform. Our total liability for any claim is limited to the amount you paid for the affected order.
                </p>
            </div>

            {{-- Section 9 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">9. Changes to These Terms</h2>
                <p>
                    We may update these Terms from time to time. The "Last updated" date at the top of this page indicates the most recent revision. Continued use of the Platform after changes constitutes acceptance of the updated Terms.
                </p>
            </div>

            {{-- Section 10 --}}
            <div>
                <h2 class="font-display font-bold text-xl text-oreo-noir mb-3">10. Contact</h2>
                <p>
                    Questions about these Terms? Reach out to us:
                </p>
                <div class="mt-4 rounded-xl bg-milk-cream border border-stone-200 p-4 space-y-2 text-sm">
                    <p class="flex items-center gap-3">
                        <x-icon name="location" class="w-4 h-4 text-chocolate shrink-0" />
                        <span>University of Caloocan City — Congressional Campus</span>
                    </p>
                    <p class="flex items-center gap-3">
                        <x-icon name="login" class="w-4 h-4 text-chocolate shrink-0" />
                        <span>oreobites@ucc.edu.ph</span>
                    </p>
                </div>
            </div>

        </article>

        {{-- Related links --}}
        <div class="mt-6 flex flex-wrap gap-3 justify-center">
            <a href="/privacy" class="btn-secondary">
                <x-icon name="orders" class="w-4 h-4" />
                Read our Privacy Policy
            </a>
            <a href="/" class="btn-ghost">
                Back to Home
            </a>
        </div>
    </div>
</section>

@endsection