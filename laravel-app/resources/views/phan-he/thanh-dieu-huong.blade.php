<!-- Sidebar Navigation (Glassmorphic dark design) -->
<aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between shadow-2xl relative z-20">
    <div>
        <!-- Header Brand -->
        <div class="h-16 flex items-center px-6 bg-slate-950/40 border-b border-slate-800 gap-3">
            <img src="/logo.png" alt="Logo" class="w-8 h-8 rounded-lg object-cover">
            <div>
                <h1 class="text-white font-extrabold text-[11px] tracking-wide uppercase leading-tight">QUẢN LÝ PHÒNG TRỌ</h1>
                <span class="text-[9px] text-indigo-400 font-semibold uppercase tracking-wider block leading-none">Chuyên Nghiệp • Tin Cậy</span>
            </div>
        </div>

        <!-- Profile Info -->
        <div class="px-6 py-6 border-b border-slate-800 bg-slate-950/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-base shadow-lg">
                    QL
                </div>
                <div>
                    <div class="text-sm font-semibold text-white">Quản trị viên</div>
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Trực tuyến
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="px-4 py-4 space-y-1">
            <button @click="activeTab = 'dashboard'; selectedRoom = null; searchQuery = ''"
                    :class="activeTab === 'dashboard' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                <i data-lucide="layout-dashboard" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                Tổng quan
            </button>

            <button @click="activeTab = 'rooms'; selectedRoom = null; searchQuery = ''"
                    :class="activeTab === 'rooms' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                <i data-lucide="key-round" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                Phòng trọ
            </button>

            <button @click="activeTab = 'tenants'; selectedRoom = null; searchQuery = ''"
                    :class="activeTab === 'tenants' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                <i data-lucide="users" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                Khách thuê
            </button>

            <button @click="activeTab = 'contracts'; selectedRoom = null; searchQuery = ''"
                    :class="activeTab === 'contracts' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                <i data-lucide="file-text" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                Hợp đồng
            </button>

            <button @click="activeTab = 'invoices'; selectedRoom = null; searchQuery = ''"
                    :class="activeTab === 'invoices' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                <i data-lucide="receipt" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                Hóa đơn
            </button>

            <button @click="activeTab = 'assets'; selectedRoom = null; searchQuery = ''"
                    :class="activeTab === 'assets' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                <i data-lucide="package" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                Tài sản
            </button>
        </nav>
    </div>

    <!-- Footer / Logout Mock -->
    <div class="p-4 border-t border-slate-800">
        <button @click="alert('Đăng xuất thành công! (Mock)')" 
                class="w-full flex items-center justify-center gap-2 bg-slate-800 hover:bg-rose-950/20 hover:text-rose-400 text-slate-400 py-2.5 px-4 rounded-xl text-sm font-medium transition duration-200 border border-slate-800 hover:border-rose-900/30">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            Đăng xuất
        </button>
    </div>
</aside>
