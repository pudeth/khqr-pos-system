@extends('layouts.admin')

@section('title', 'Store Settings - Pospay Admin')
@section('header', 'STORE SETTINGS')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Store Branding Settings -->
    <div class="neo-card bg-white rounded-xl overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-purple-400 to-pink-400 p-6 border-b-4 border-black">
            <h3 class="text-2xl font-black text-white uppercase tracking-wide">
                <i class="fas fa-store mr-3"></i>Store Branding
            </h3>
            <p class="text-white opacity-90 mt-1">Customize your store name, logo, and branding</p>
        </div>
        
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Store Name -->
                <div>
                    <label class="block text-sm font-black text-gray-700 uppercase tracking-wide mb-2">
                        Store Name
                    </label>
                    <input type="text" 
                           name="store_name" 
                           value="{{ old('store_name', $settings['branding']['store_name'] ?? 'POSPAY') }}"
                           class="neo-input w-full px-4 py-3 rounded-lg font-bold"
                           required>
                    @error('store_name')
                        <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store Tagline -->
                <div>
                    <label class="block text-sm font-black text-gray-700 uppercase tracking-wide mb-2">
                        Store Tagline
                    </label>
                    <input type="text" 
                           name="store_tagline" 
                           value="{{ old('store_tagline', $settings['branding']['store_tagline'] ?? 'KHQR PAYMENT SYSTEM') }}"
                           class="neo-input w-full px-4 py-3 rounded-lg font-bold"
                           placeholder="e.g., KHQR PAYMENT SYSTEM">
                    @error('store_tagline')
                        <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Store Logo -->
            <div class="mt-6">
                <label class="block text-sm font-black text-gray-700 uppercase tracking-wide mb-2">
                    Store Logo
                </label>
                
                @if(isset($settings['branding']['store_logo']) && $settings['branding']['store_logo'])
                    <div class="mb-4">
                        <p class="text-sm font-bold text-gray-600 mb-2">Current Logo:</p>
                        <div class="neo-card bg-gray-50 p-4 rounded-lg inline-block">
                            <img src="{{ asset('storage/' . $settings['branding']['store_logo']) }}" 
                                 alt="Store Logo" 
                                 class="h-20 w-auto rounded-lg border-2 border-black">
                        </div>
                    </div>
                @endif
                
                <input type="file" 
                       name="store_logo" 
                       accept="image/*"
                       class="neo-input w-full px-4 py-3 rounded-lg font-bold">
                <p class="text-sm text-gray-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Upload JPG, PNG, or GIF. Max size: 2MB
                </p>
                @error('store_logo')
                    <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Store Contact Information -->
            <div class="mt-8 pt-6 border-t-4 border-black">
                <h4 class="text-xl font-black text-gray-700 uppercase tracking-wide mb-4">
                    Contact Information
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Store Phone -->
                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-wide mb-2">
                            Phone Number
                        </label>
                        <input type="text" 
                               name="store_phone" 
                               value="{{ old('store_phone', $settings['general']['store_phone'] ?? '') }}"
                               class="neo-input w-full px-4 py-3 rounded-lg font-bold"
                               placeholder="e.g., +855 12 345 678">
                        @error('store_phone')
                            <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Store Address -->
                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-wide mb-2">
                            Address
                        </label>
                        <textarea name="store_address" 
                                  rows="3"
                                  class="neo-input w-full px-4 py-3 rounded-lg font-bold"
                                  placeholder="Enter store address">{{ old('store_address', $settings['general']['store_address'] ?? '') }}</textarea>
                        @error('store_address')
                            <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="mt-8 flex justify-end">
                <button type="submit" class="neo-btn bg-neo-green text-black px-8 py-4 rounded-lg">
                    <i class="fas fa-save mr-2"></i>
                    SAVE SETTINGS
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Section -->
    <div class="neo-card bg-white rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-400 to-purple-400 p-6 border-b-4 border-black">
            <h3 class="text-2xl font-black text-white uppercase tracking-wide">
                <i class="fas fa-eye mr-3"></i>Preview
            </h3>
            <p class="text-white opacity-90 mt-1">How your branding will appear</p>
        </div>
        
        <div class="p-6">
            <div class="neo-card bg-gradient-to-r from-purple-400 to-pink-400 p-6 rounded-xl">
                <div class="flex items-center space-x-4">
                    @if(isset($settings['branding']['store_logo']) && $settings['branding']['store_logo'])
                        <img src="{{ asset('storage/' . $settings['branding']['store_logo']) }}" 
                             alt="Store Logo" 
                             class="h-16 w-16 rounded-lg border-3 border-black object-cover">
                    @else
                        <div class="h-16 w-16 bg-white rounded-lg border-3 border-black flex items-center justify-center">
                            <i class="fas fa-store text-2xl text-gray-400"></i>
                        </div>
                    @endif
                    
                    <div>
                        <h1 class="text-3xl font-black text-white tracking-tight">
                            {{ $settings['branding']['store_name'] ?? 'POSPAY' }}
                        </h1>
                        <p class="text-sm font-bold text-white opacity-90">
                            {{ $settings['branding']['store_tagline'] ?? 'KHQR PAYMENT SYSTEM' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection