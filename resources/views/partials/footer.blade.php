<footer class="bg-oreo-noir text-stone-300 mt-20">
    <div class="container-app py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-9 h-9 flex items-center justify-center rounded-full bg-chocolate text-milk-cream">
                        <x-icon name="cookie" class="w-5 h-5" />
                    </span>
                    <span class="font-display font-bold text-lg">
                        <span class="text-golden">Oreo</span><span class="text-white">Bites</span>
                    </span>
                </div>
                <p class="text-sm text-stone-400 leading-relaxed">
                    Bite-sized frozen Oreo cheesecake, glazed in dark and white chocolate. Made fresh, kept frozen, and ready for campus pickup.
                </p>
            </div>

            {{-- Info --}}
            <div>
                <h3 class="font-display font-semibold text-white mb-4 text-sm uppercase tracking-wider">
                    Pickup &amp; Payment
                </h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3">
                        <x-icon name="location" class="w-4 h-4 mt-0.5 text-golden shrink-0" />
                        <span>UCC Congressional Campus<br><span class="text-stone-500 text-xs">Onsite Kiosk</span></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="credit-card" class="w-4 h-4 mt-0.5 text-golden shrink-0" />
                        <span>GCash via PayMongo<br><span class="text-stone-500 text-xs">Secure online payment</span></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="clock" class="w-4 h-4 mt-0.5 text-golden shrink-0" />
                        <span>Pre-order batches only<br><span class="text-stone-500 text-xs">Keep frozen with ice packs</span></span>
                    </li>
                </ul>
            </div>

            {{-- Legal --}}
            <div>
                <h3 class="font-display font-semibold text-white mb-4 text-sm uppercase tracking-wider">
                    Information
                </h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/track" class="hover:text-golden transition-colors">Track Your Order</a></li>
                    <li><a href="/terms" class="hover:text-golden transition-colors">Terms &amp; Conditions</a></li>
                    <li><a href="/privacy" class="hover:text-golden transition-colors">Privacy Policy</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-stone-800 mt-10 pt-6 flex flex-col md:flex-row justify-between items-center gap-3 text-xs text-stone-500">
            <p>
                &copy; {{ date('Y') }} Oreo Bites — BS Information System 3A
            </p>
            <p>
                University of Caloocan City &middot; Computer Studies Department
            </p>
        </div>
    </div>
</footer>