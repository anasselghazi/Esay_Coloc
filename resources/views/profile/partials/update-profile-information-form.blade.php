<form method="post" action="{{ route('profile.update') }}"
      class="mt-6 space-y-6"
      enctype="multipart/form-data">

    @csrf
    @method('patch')

    <!-- Name -->
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text"
            class="mt-1 block w-full"
            :value="old('name', $user->name)"
            autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <!-- Pseudo -->
    <div>
        <x-input-label for="pseudo" :value="__('Pseudo')" />
        <x-text-input id="pseudo" name="pseudo" type="text"
            class="mt-1 block w-full"
            :value="old('pseudo', $user->pseudo)"
            required />
        <x-input-error class="mt-2" :messages="$errors->get('pseudo')" />
    </div>

    <!-- Email -->
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email"
            class="mt-1 block w-full"
            :value="old('email', $user->email)"
            required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <!-- Photo -->
    <div>
        @if($user->photo)
    <div class="mt-3">
        <img src="{{ asset('storage/' . $user->photo) }}"
             class="w-24 h-24 rounded-full object-cover">

        <div class="mt-2">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="remove_photo" value="1">
                <span>Remove photo</span>
            </label>
        </div>
    </div>
@endif
        <x-input-label for="photo" :value="__('Profile Photo')" />
        <input id="photo" name="photo" type="file"
            class="mt-1 block w-full"
            accept="image/*">
        <x-input-error class="mt-2" :messages="$errors->get('photo')" />

        @if($user->photo)
            <div class="mt-3">
                <img src="{{ asset('storage/' . $user->photo) }}"
                     class="w-24 h-24 rounded-full object-cover">
            </div>
        @endif
    </div>

    <!-- Save Button -->
    <div class="flex items-center gap-4">
        <x-primary-button>Save</x-primary-button>

        @if (session('status') === 'profile-updated')
            <p class="text-sm text-green-600">
                Saved successfully.
            </p>
        @endif
    </div>
</form>