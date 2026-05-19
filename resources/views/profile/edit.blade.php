<x-user-layout>
    <x-slot name="header">Profile</x-slot>
    <x-slot name="subheader">Update your account settings.</x-slot>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card-panel">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card-panel mb-4">
                @include('profile.partials.update-password-form')
            </div>
            <div class="card-panel">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-user-layout>
