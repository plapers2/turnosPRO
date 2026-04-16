<x-app-layout>
    <!-- Dashboard Content -->
    <div class="p-6 space-y-6">
        <!-- Stats Row: Bento Style -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-surface-container-lowest p-6 rounded-2xl shadow-[0px_4px_12px_rgba(0,0,0,0.02)] transition-transform hover:scale-[1.02]">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-outline">Today's Appts</span>
                    <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-sm"
                            style="font-variation-settings: 'FILL' 1;">event</span>
                    </div>
                </div>
                <div class="text-3xl font-bold text-on-surface mb-1">24</div>
                <div class="flex items-center gap-1 text-[10px] text-primary font-bold">
                    <span class="material-symbols-outlined text-xs">trending_up</span>
                    <span>+12% vs last week</span>
                </div>
            </div>
            <div
                class="bg-surface-container-lowest p-6 rounded-2xl shadow-[0px_4px_12px_rgba(0,0,0,0.02)] transition-transform hover:scale-[1.02]">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-outline">Attendance</span>
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-800 text-sm">person_check</span>
                    </div>
                </div>
                <div class="text-3xl font-bold text-on-surface mb-1">98.2%</div>
                <div class="flex items-center gap-1 text-[10px] text-emerald-600 font-bold">
                    <span class="material-symbols-outlined text-xs">verified</span>
                    <span>Near perfect score</span>
                </div>
            </div>
            <div
                class="bg-surface-container-lowest p-6 rounded-2xl shadow-[0px_4px_12px_rgba(0,0,0,0.02)] transition-transform hover:scale-[1.02]">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-outline">Pending</span>
                    <div class="w-8 h-8 bg-tertiary-fixed rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-tertiary text-sm">pending_actions</span>
                    </div>
                </div>
                <div class="text-3xl font-bold text-on-surface mb-1">7</div>
                <div class="flex items-center gap-1 text-[10px] text-tertiary font-bold">
                    <span class="material-symbols-outlined text-xs">priority_high</span>
                    <span>Action required</span>
                </div>
            </div>
            <div
                class="bg-surface-container-lowest p-6 rounded-2xl shadow-[0px_4px_12px_rgba(0,0,0,0.02)] transition-transform hover:scale-[1.02]">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-outline">Daily Revenue</span>
                    <div class="w-8 h-8 bg-secondary-container rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-secondary text-sm">payments</span>
                    </div>
                </div>
                <div class="text-3xl font-bold text-on-surface mb-1 tabular-nums">$1,450</div>
                <div class="flex items-center gap-1 text-[10px] text-stone-500 font-bold">
                    <span>Target: $2,000</span>
                </div>
            </div>
        </div>
        <!-- Main Dashboard Grid -->
        <div class="grid grid-cols-12 gap-6 items-start">
            <!-- Left: Timeline -->
            <div
                class="col-span-12 lg:col-span-8 bg-surface-container-lowest p-6 rounded-3xl shadow-[0px_12px_32px_rgba(15,110,86,0.04)]">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-xl font-bold text-on-surface">Today's Schedule</h2>
                    <div class="flex gap-2">
                        <button class="p-2 hover:bg-surface-container rounded-full"><span
                                class="material-symbols-outlined">filter_list</span></button>
                        <button class="p-2 hover:bg-surface-container rounded-full"><span
                                class="material-symbols-outlined">print</span></button>
                    </div>
                </div>
                <!-- Timeline Content -->
                <div
                    class="relative pl-12 space-y-8 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-surface-container-high">
                    <!-- Timeline Item 1 -->
                    <div class="relative">
                        <div class="absolute -left-[45px] top-1 w-10 text-right">
                            <span class="text-xs font-bold text-on-surface-variant tabular-nums">09:00</span>
                        </div>
                        <div class="absolute -left-12 top-1 w-6 h-6 rounded-full bg-white border-4 border-primary z-10">
                        </div>
                        <div
                            class="bg-primary/5 p-4 rounded-2xl border-l-4 border-primary flex justify-between items-center transition-all hover:translate-x-1">
                            <div class="flex gap-4 items-center">
                                <img alt="Client Portrait" class="w-10 h-10 rounded-full object-cover"
                                    data-alt="Portrait of a smiling professional woman with bright eyes against a neutral background"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuC0uyAlK-_TUG2hM-c5X5YJyHhh3eMZGyDzs2ych-SMI05juXeK5RCgNngNwiX02zMG9rzcOHq1gnDHjXuV-mx8srk-B72WAgnJG402xglCjdGyaXmrFdvJErmFCrHccOfACpRpGuBzCcsnD55ReGpsBChRYOZwMBt9YQk3lWpvPlMt__trV0JGsl9iu6A4YnCw3woxD87Pkppah_vUfO00dshESX3wUVvto-eNQA7XmkUuJqX18ILuhUA_WOYi5DH9yf3nMH8nr-Mk" />
                                <div>
                                    <p class="font-bold text-emerald-950">Sarah Jenkins</p>
                                    <p class="text-xs text-emerald-700">Initial Consultation • 45m</p>
                                </div>
                            </div>
                            <span
                                class="px-3 py-1 bg-primary text-white text-[10px] font-bold rounded-full">CONFIRMED</span>
                        </div>
                    </div>
                    <!-- Timeline Item 2 -->
                    <div class="relative">
                        <div class="absolute -left-[45px] top-1 w-10 text-right">
                            <span class="text-xs font-bold text-on-surface-variant tabular-nums">10:30</span>
                        </div>
                        <div
                            class="absolute -left-12 top-1 w-6 h-6 rounded-full bg-white border-4 border-secondary z-10">
                        </div>
                        <div
                            class="bg-secondary-container/20 p-4 rounded-2xl border-l-4 border-secondary flex justify-between items-center transition-all hover:translate-x-1">
                            <div class="flex gap-4 items-center">
                                <img alt="Client Portrait" class="w-10 h-10 rounded-full object-cover"
                                    data-alt="Headshot of a middle-aged man with glasses and a kind expression in natural lighting"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuB9NJL_LscHoKT8odO0cBGRdmCUuvdEtaPKJpEhONIOK8pDzuJiPF5EyVz0RW1_LDslpo59gq700wK9abdf3bhdksBtZC_JObhictuVWvvTTSuMj4AxDDfb3pG0ulnrS7rZevuC5hqECspefw89D3WU8VRKAgdUCDR4gUJvbcr6HXOfzq2bmYCNXgGPhyAn5v1t9sZnUJrE4f52orDlSxT7OBhXWTrre2RsVNce3cYh-kSTejA0ZuzurtcQlVoYUqZ0ozYEB8BbHQ_P" />
                                <div>
                                    <p class="font-bold text-stone-900">David Miller</p>
                                    <p class="text-xs text-stone-500">Service Follow-up • 30m</p>
                                </div>
                            </div>
                            <span
                                class="px-3 py-1 bg-secondary text-white text-[10px] font-bold rounded-full uppercase">Arrived</span>
                        </div>
                    </div>
                    <!-- Timeline Item 3 (Current Time Indicator) -->
                    <div class="relative py-2">
                        <div
                            class="absolute left-[-48px] top-1/2 w-screen border-t-2 border-dashed border-tertiary opacity-40 z-0">
                        </div>
                        <span
                            class="relative z-10 bg-tertiary text-white text-[10px] px-2 py-0.5 rounded-full ml-[-40px]">NOW</span>
                    </div>
                    <!-- Timeline Item 4 -->
                    <div class="relative">
                        <div class="absolute -left-[45px] top-1 w-10 text-right">
                            <span class="text-xs font-bold text-on-surface-variant tabular-nums">13:15</span>
                        </div>
                        <div
                            class="absolute -left-12 top-1 w-6 h-6 rounded-full bg-white border-4 border-surface-container-high z-10">
                        </div>
                        <div
                            class="bg-surface-container-low p-4 rounded-2xl border-l-4 border-outline flex justify-between items-center transition-all hover:translate-x-1 opacity-70">
                            <div class="flex gap-4 items-center">
                                <div
                                    class="w-10 h-10 rounded-full bg-stone-200 flex items-center justify-center text-stone-400 font-bold">
                                    RB</div>
                                <div>
                                    <p class="font-bold text-stone-900">Robert Brown</p>
                                    <p class="text-xs text-stone-500">Annual Review • 60m</p>
                                </div>
                            </div>
                            <span
                                class="px-3 py-1 bg-stone-100 text-stone-500 text-[10px] font-bold rounded-full uppercase">Pending</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right: Mini Calendar + Upcoming -->
            <div class="col-span-12 lg:col-span-4 space-y-6">
                <!-- Mini Calendar -->
                <div class="bg-surface-container-lowest p-6 rounded-3xl shadow-[0px_12px_32px_rgba(15,110,86,0.04)]">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-on-surface">September 2024</h3>
                        <div class="flex gap-1">
                            <span
                                class="material-symbols-outlined text-stone-400 cursor-pointer hover:text-primary">chevron_left</span>
                            <span
                                class="material-symbols-outlined text-stone-400 cursor-pointer hover:text-primary">chevron_right</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-outline mb-2">
                        <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                    </div>
                    <div class="grid grid-cols-7 gap-1 text-center">
                        <span class="py-2 text-stone-300">28</span>
                        <span class="py-2 text-stone-300">29</span>
                        <span class="py-2 text-stone-300">30</span>
                        <span class="py-2 text-stone-300">31</span>
                        <span
                            class="py-2 text-on-surface hover:bg-emerald-50 rounded-lg cursor-pointer transition-colors">1</span>
                        <span
                            class="py-2 text-on-surface hover:bg-emerald-50 rounded-lg cursor-pointer transition-colors">2</span>
                        <span
                            class="py-2 text-on-surface hover:bg-emerald-50 rounded-lg cursor-pointer transition-colors">3</span>
                        <!-- Today Indicator -->
                        <span class="py-2 bg-primary text-white font-bold rounded-lg cursor-pointer">4</span>
                        <span
                            class="py-2 text-on-surface relative after:content-[''] after:absolute after:bottom-1 after:left-1/2 after:-translate-x-1/2 after:w-1 after:h-1 after:bg-primary after:rounded-full">5</span>
                        <span
                            class="py-2 text-on-surface hover:bg-emerald-50 rounded-lg cursor-pointer transition-colors">6</span>
                        <span
                            class="py-2 text-on-surface hover:bg-emerald-50 rounded-lg cursor-pointer transition-colors">7</span>
                        <span
                            class="py-2 text-on-surface hover:bg-emerald-50 rounded-lg cursor-pointer transition-colors">8</span>
                        <span
                            class="py-2 text-on-surface hover:bg-emerald-50 rounded-lg cursor-pointer transition-colors">9</span>
                        <span
                            class="py-2 text-on-surface hover:bg-emerald-50 rounded-lg cursor-pointer transition-colors">10</span>
                    </div>
                </div>
                <!-- Upcoming List -->
                <div class="bg-surface-container-lowest p-6 rounded-3xl shadow-[0px_12px_32px_rgba(15,110,86,0.04)]">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-on-surface">Tomorrow</h3>
                        <a class="text-xs text-primary font-bold" href="#">View all</a>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between group cursor-pointer">
                            <div class="flex gap-3 items-center">
                                <div class="w-2 h-10 bg-emerald-100 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-bold group-hover:text-primary transition-colors">Legal
                                        Strategy Session</p>
                                    <p class="text-[10px] text-stone-500">09:00 AM • James K.</p>
                                </div>
                            </div>
                            <span
                                class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors">arrow_forward_ios</span>
                        </div>
                        <div class="flex items-center justify-between group cursor-pointer">
                            <div class="flex gap-3 items-center">
                                <div class="w-2 h-10 bg-emerald-100 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-bold group-hover:text-primary transition-colors">Asset Audit
                                    </p>
                                    <p class="text-[10px] text-stone-500">11:30 AM • Maria L.</p>
                                </div>
                            </div>
                            <span
                                class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors">arrow_forward_ios</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bottom: Activity Feed -->
            <div
                class="col-span-12 bg-surface-container-lowest p-6 rounded-3xl shadow-[0px_12px_32px_rgba(15,110,86,0.04)]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-on-surface">Recent Activity</h2>
                    <button class="text-xs font-bold text-primary flex items-center gap-1">
                        Clear all <span class="material-symbols-outlined text-xs">close</span>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="flex gap-4 items-start">
                        <div class="w-8 h-8 bg-emerald-50 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary text-sm">add_circle</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">New Booking Created</p>
                            <p class="text-xs text-stone-500 mb-1">Anna W. booked 'Design Review' for Oct 12.</p>
                            <p class="text-[10px] text-stone-400">2 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div
                            class="w-8 h-8 bg-tertiary-fixed rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-tertiary text-sm">payment</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Payment Received</p>
                            <p class="text-xs text-stone-500 mb-1">$250.00 from Invoice #99211.</p>
                            <p class="text-[10px] text-stone-400">45 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="w-8 h-8 bg-stone-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-stone-500 text-sm">edit</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Schedule Adjusted</p>
                            <p class="text-xs text-stone-500 mb-1">Dr. Aris changed lunch break to 1:30 PM.</p>
                            <p class="text-[10px] text-stone-400">1 hour ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
