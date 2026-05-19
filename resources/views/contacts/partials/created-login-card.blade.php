@if(session('created_login'))
    @php($login = session('created_login'))
    <div
        class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-900/20"
        x-data="{
            copied: false,
            loginText: @js("Login URL: {$login['login_url']}\nEmail: {$login['email']}\nPassword: {$login['password']}\nRole: {$login['role']}")
        }"
    >
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h3 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200">Login account created</h3>
                <p class="mt-1 text-sm text-emerald-800 dark:text-emerald-300">Share these details with the reseller. The password is shown only after account creation.</p>
                <dl class="mt-3 grid gap-2 text-sm md:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase text-emerald-700 dark:text-emerald-300">Email</dt>
                        <dd class="font-semibold text-emerald-950 dark:text-white">{{ $login['email'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-emerald-700 dark:text-emerald-300">Role</dt>
                        <dd class="font-semibold text-emerald-950 dark:text-white">{{ $login['role'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-emerald-700 dark:text-emerald-300">Password</dt>
                        <dd class="font-mono font-semibold text-emerald-950 dark:text-white">{{ $login['password'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-emerald-700 dark:text-emerald-300">Login URL</dt>
                        <dd class="break-all font-semibold text-emerald-950 dark:text-white">{{ $login['login_url'] }}</dd>
                    </div>
                </dl>
            </div>

            <button
                type="button"
                class="inline-flex items-center justify-center rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-300 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
                @click="navigator.clipboard.writeText(loginText).then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
            >
                <span x-show="!copied">Copy login details</span>
                <span x-show="copied" x-cloak>Copied</span>
            </button>
        </div>
    </div>
@endif
