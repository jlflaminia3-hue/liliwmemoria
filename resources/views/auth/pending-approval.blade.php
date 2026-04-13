<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Your email has been verified successfully.') }}
    </div>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('Your account still needs validation by a Master Admin before your role can be changed to Admin and you can access the dashboard.') }}
    </div>

    <div class="mt-4 flex items-center justify-end">
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-primary-button>
                {{ __('Log Out') }}
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>

