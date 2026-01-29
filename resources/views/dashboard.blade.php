<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php $user = Auth::user(); @endphp
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($user && $user->department_id && !$user->is_admin)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        <a href="{{ route('profile.show') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-edit me-2"></i>View Profile
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
