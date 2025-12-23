<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('sellers.users.store') }}" method="POST">
                        @csrf
                        
                        <!-- Name -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <!-- Branch -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Branch</label>
                            <input type="text" name="branch" value="{{ old('branch') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <!-- User Type (Hidden or Readonly for Sellers) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">User Type</label>
                             <input type="text" name="user_type_display" value="Seller" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100" readonly>
                             <input type="hidden" name="user_type" value="seller">
                        </div>

                        <!-- Parent Reseller (Not applicable for top-level Sellers usually, but leaving commented out if needed) -->
                        <!-- 
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Parent Reseller (if Sub User)</label>
                            <select name="parent_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">None</option>
                            </select>
                        </div>
                        -->

                        <!-- Phone -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                            <input type="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Re-enter Password</label>
                            <input type="password" name="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        
                        <!-- Return Fee -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Add Return Fee</label>
                            <input type="number" step="0.01" name="return_fee" value="{{ old('return_fee') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <!-- Courier -->
                         <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Courier</label>
                             <!-- Placeholder for Courier ID, or just ID input for now -->
                            <input type="text" name="courier_id" placeholder="Courier ID" value="{{ old('courier_id') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save User
                            </button>
                            <a href="{{ route('sellers.users.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
