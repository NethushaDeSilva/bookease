<x-action-section>
    <x-slot name="title">{{ __('Account type') }}</x-slot>
    <x-slot name="description">{{ __('Manage how you use BookEase.') }}</x-slot>

    <x-slot name="content">
        <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-sm font-semibold text-indigo-700">
            {{ Auth::user()->isProvider() ? 'Service provider' : 'Customer' }}
        </span>

        @if (! Auth::user()->isActive())
            <p class="mt-4 text-sm text-slate-600">Your account is suspended. Contact the administrator before changing your account type.</p>
        @elseif (Auth::user()->isCustomer())
            <h3 class="mt-4 text-lg font-bold text-slate-900">Ready to offer your services?</h3>
            <p class="mt-2 max-w-xl text-sm leading-6 text-slate-600">
                Become a service provider to create your business, publish services, and manage customer bookings.
                Your earlier bookings and reviews stay with your account and remain accessible under My appointments.
                Your business will need administrator approval before customers can book it.
            </p>
            @if (Auth::user()->hasVerifiedEmail())
                <form method="POST" action="{{ route('profile.become-provider') }}" class="mt-5">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Become a provider
                    </button>
                </form>
            @else
                <p class="mt-4 text-sm text-slate-600">Verify your email before becoming a provider.</p>
                <a href="{{ route('verification.notice') }}" class="mt-2 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">Verify email address</a>
            @endif
        @else
            <p class="mt-4 text-sm leading-6 text-slate-600">Manage your business profile, services, and availability from your provider workspace.</p>
            <div class="mt-4 flex flex-wrap gap-4">
                <a href="{{ route('provider.business.profile') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">My business</a>
                <a href="{{ route('customer.bookings.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">My appointments</a>
            </div>
        @endif
    </x-slot>
</x-action-section>
