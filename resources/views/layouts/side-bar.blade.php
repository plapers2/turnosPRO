<!-- SideNavBar -->
<aside
    class="fixed left-0 top-0 h-screen w-[240px] z-40 bg-stone-50 flex flex-col py-6 gap-2 shadow-[12px_0px_32px_rgba(15,110,86,0.04)] font-inter text-sm font-medium">
    <div class="px-6 mb-8 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary-container flex items-center justify-center">
            <span class="material-symbols-outlined text-white"
                style="font-variation-settings: 'FILL' 1;">calendar_month</span>
        </div>
        <div>
            <h1 class="text-lg font-bold text-emerald-900  leading-tight">TurnosPro</h1>
            <p class="text-[10px] uppercase tracking-widest text-stone-500">Admin Dashboard</p>
        </div>
    </div>
    <nav class="flex-grow space-y-1">
        <!-- Active Tab: Overview -->
        <a class="flex items-center gap-3 py-3 px-4 bg-emerald-100/50  text-emerald-900 rounded-lg mx-2 transition-all duration-200 ease-in-out"
            href="#">
            <span class="material-symbols-outlined" data-icon="dashboard"
                style="font-variation-settings: 'FILL' 1;">dashboard</span>
            <span>Overview</span>
        </a>
        <a class="flex items-center gap-3 py-3 px-4 text-stone-600  hover:text-emerald-700 hover:bg-stone-100  rounded-lg mx-2 transition-all duration-200 ease-in-out"
            href="#">
            <span class="material-symbols-outlined" data-icon="calendar_today">calendar_today</span>
            <span>Operations</span>
        </a>
        <a class="flex items-center gap-3 py-3 px-4 text-stone-600  hover:text-emerald-700 hover:bg-stone-100  rounded-lg mx-2 transition-all duration-200 ease-in-out"
            href="#">
            <span class="material-symbols-outlined" data-icon="store">store</span>
            <span>Business</span>
        </a>
        <a class="flex items-center gap-3 py-3 px-4 text-stone-600  hover:text-emerald-700 hover:bg-stone-100  rounded-lg mx-2 transition-all duration-200 ease-in-out"
            href="#">
            <span class="material-symbols-outlined" data-icon="payments">payments</span>
            <span>Finance</span>
        </a>
        <a class="flex items-center gap-3 py-3 px-4 text-stone-600  hover:text-emerald-700 hover:bg-stone-100  rounded-lg mx-2 transition-all duration-200 ease-in-out"
            href="#">
            <span class="material-symbols-outlined" data-icon="settings">settings</span>
            <span>System</span>
        </a>
    </nav>
    <div class="px-4 mb-6">
        <button
            class="w-full bg-primary hover:bg-primary-container text-white py-3 px-4 rounded-xl font-bold flex items-center justify-center gap-2 transition-all active:scale-95 shadow-lg shadow-primary/20">
            <span class="material-symbols-outlined">add</span>
            <span>New Appointment</span>
        </button>
    </div>
    <div class="border-t border-stone-200/50 mx-4 pt-4 mt-auto">
        <a class="flex items-center gap-3 py-2 px-4 text-stone-600 dark:text-stone-400 hover:text-emerald-700 hover:bg-stone-100 rounded-lg transition-all"
            href="#">
            <span class="material-symbols-outlined" data-icon="help">help</span>
            <span>Help Center</span>
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="flex items-center gap-3 py-2 px-4 text-stone-600 dark:text-stone-400 hover:text-emerald-700 hover:bg-stone-100 rounded-lg transition-all">
                <span class="material-symbols-outlined" data-icon="logout">logout</span>
                <span>Logout</span>
            </button>
        </form>

    </div>
</aside>
