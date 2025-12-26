<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Add City') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700">
             <div class="flex items-center mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                <svg class="w-6 h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">City Details</h3>
            </div>
            
            <form action="{{ route('cities.store') }}" method="POST"
                  x-data="{
                      slData: {{ json_encode($slData) }},
                      provinces: [],
                      districts: [],
                      selectedProvince: '{{ old('province') }}',
                      selectedDistrict: '{{ old('district') }}',
                      
                      init() {
                          this.provinces = Object.keys(this.slData);
                          if (this.selectedProvince) {
                              this.updateDistricts();
                          }
                      },
                      
                      updateDistricts() {
                          this.districts = this.selectedProvince ? this.slData[this.selectedProvince] : [];
                          if (!this.districts.includes(this.selectedDistrict)) {
                              this.selectedDistrict = '';
                          }
                      }
                  }">
                @csrf
                <div class="grid grid-cols-1 gap-6">

                    <!-- Country (Fixed) -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Country</label>
                        <input type="text" value="Sri Lanka" disabled class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400 cursor-not-allowed">
                    </div>

                    <!-- Province -->
                    <div>
                        <label for="province" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Province <span class="text-red-500">*</span></label>
                        <select id="province" name="province" x-model="selectedProvince" @change="updateDistricts()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                            <option value="">Select Province</option>
                            <template x-for="province in provinces" :key="province">
                                <option :value="province" x-text="province" :selected="province === selectedProvince"></option>
                            </template>
                        </select>
                        @error('province') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- District -->
                    <div>
                        <label for="district" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">District <span class="text-red-500">*</span></label>
                        <select id="district" name="district" x-model="selectedDistrict" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" :disabled="!selectedProvince" required>
                            <option value="">Select District</option>
                            <template x-for="district in districts" :key="district">
                                <option :value="district" x-text="district" :selected="district === selectedDistrict"></option>
                            </template>
                        </select>
                        @error('district') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                     <!-- City Name -->
                    <div>
                        <label for="city_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">City Name <span class="text-red-500">*</span></label>
                        <input type="text" name="city_name" id="city_name" value="{{ old('city_name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="e.g. Colombo" required>
                         @error('city_name') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Postal Code -->
                     <div>
                        <label for="postal_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Postal Code <span class="text-red-500">*</span></label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="e.g. 00100" required>
                         @error('postal_code') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 space-x-3">
                     <a href="{{ route('cities.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                        Cancel
                    </a>
                    <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        Save City
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
