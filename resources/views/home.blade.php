<x-layout>

    <x-slot name='centred'>
        <div class="flex w-full flex-col justify-center items-center space-y-6">
            <a href="{{ route('home') }}">
                <x-logo class="w-auto h-16 mx-auto text-indigo-600" />
            </a>

            <h1 class="text-5xl font-extrabold tracking-wider text-center text-gray-600">
                <div>{{ config('app.name') }}</div>
                <div class='text-sm font-bold italic'>Version 0.1</div>
            </h1>
            @if ($currentProject)
            <h2 class="text-3xl font-bold text-center text-indigo-600">
                {{ $currentProject->project }}
            </h2>
            @endif

            @isset($currentSubstitute)
            <div class='mb-3 font-medium italic text-white tracking-wider bg-blue-900 rounded px-2 py-1'>
                <a href='/substitute'>You are currently being substituted by {{$currentSubstitute->fullname}}</a>
            </div>
            @endisset

            @if(isset($currentSubstitutees) and count($currentSubstitutees)>0)
            <div class='mb-3 font-medium italic text-white tracking-wider bg-blue-900 rounded px-2 py-1'>
                <div>
                    You are currently substituting for:
                </div>
                @foreach ($currentSubstitutees as $currentsubstitutee)
                <div class='ml-10'>{{$currentsubstitutee->fullname}}</div>
                @endforeach
            </div>
            @endif

            @include('layouts.message')

            {{-- <ul class="list-reset">
                    <li class="inline px-4">
                        <a href="https://tailwindcss.com"
                            class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">Tailwind
                            CSS</a>
                    </li>
                    <li class="inline px-4">
                        <a href="https://github.com/alpinejs/alpine"
                            class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">Alpine.js</a>
                    </li>
                    <li class="inline px-4">
                        <a href="https://laravel.com"
                            class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">Laravel</a>
                    </li>
                </ul> --}}
        </div>
    </x-slot>

</x-layout>