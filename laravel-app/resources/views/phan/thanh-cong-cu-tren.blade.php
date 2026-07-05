    <main class="flex-1 flex flex-col overflow-hidden relative z-10">
        
        <!-- Top Toolbar Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between shadow-sm flex-shrink-0">
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold text-slate-800" 
                    x-text="tabTitles[activeTab]"></h2>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Search bar if not in dashboard -->
                <div x-show="activeTab !== 'dashboard'" class="relative w-64">
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Tìm kiếm nhanh..." 
                           class="w-full pl-9 pr-4 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition duration-150">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                </div>

                <!-- Profile summary (Interactive role toggler - NFR-03) -->
                <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                    <button @click="userRole = (userRole === 'admin' ? 'viewer' : 'admin'); changeRole()" 
                            class="flex items-center gap-2 focus:outline-none hover:opacity-80 transition group"
                            title="Click để chuyển đổi vai trò (NFR-03)">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition"
                             :class="userRole === 'admin' ? 'bg-indigo-50 text-indigo-600' : 'bg-amber-50 text-amber-600'"
                             x-text="userRole === 'admin' ? 'AD' : 'KS'">
                            AD
                        </div>
                        <div class="text-left">
                            <span class="text-xs font-bold block text-slate-800">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <span class="text-[9px] text-slate-400 font-medium block" x-text="userRole === 'admin' ? 'Quyền: Quản trị viên' : 'Quyền: Chỉ xem'">Quyền: Quản trị viên</span>
                        </div>
                    </button>

                    <!-- Logout button -->
                    <form method="POST" action="/logout" style="display:inline;">
                        @csrf
                        <button type="submit" 
                                title="Đăng xuất"
                                style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;color:#e11d48;font-size:12px;font-weight:700;cursor:pointer;transition:all 0.2s;font-family:inherit;"
                                onmouseover="this.style.background='#ffe4e6'"
                                onmouseout="this.style.background='#fff1f2'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </header>
