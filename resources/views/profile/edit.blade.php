@extends('base')
@push('scripts')
    @vite(['resources/js/home/home.js'])
@endpush
@push('body')
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-bold text-secondary">
                {{ __('Profile') }}
            </h2>
            <!-- Authentication -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                               onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </x-slot>

        <div class="p-3 row">
            <div class="row mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="p-3 m-3  sm:p-8 bg-white shadow rounded-2">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="p-3 m-3  sm:p-8 bg-white shadow rounded-2">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="p-3 m-3  sm:p-8 bg-white shadow rounded-2">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@endpush
@push('scripts')
    {{--    @vite('resources/js/homepage.js')--}}
@endpush

