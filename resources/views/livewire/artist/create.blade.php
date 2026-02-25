<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Artist;

new
#[Layout('components.layouts.app.frontend')]
class extends Component {

    use WithFileUploads;

    public string $stage_name = '';
    public ?string $bio = null;
    public ?string $genre = null;
    public ?string $location = null;

    public $profile_image;
    public $cover_image;

    public bool $is_published = true;

    public function save(): void
    {
        $this->validate([
            'stage_name'    => 'required|min:2|max:255',
            'profile_image' => 'nullable|image|max:5000',
            'cover_image'   => 'nullable|image|max:5000',
        ]);

        $profilePath = $this->profile_image?->store('artists/profile', 'public');
        $coverPath   = $this->cover_image?->store('artists/covers', 'public');

        Artist::create([
            'user_id'       => Auth::id(),
            'stage_name'    => $this->stage_name,
            'slug'          => Str::slug($this->stage_name) . '-' . uniqid(),
            'bio'           => $this->bio,
            'genre'         => $this->genre,
            'location'      => $this->location,
            'profile_image' => $profilePath,
            'cover_image'   => $coverPath,
            'is_verified'   => false,
            'is_published'  => $this->is_published,
        ]);

    }

};
?>

<div class="max-w-3xl mx-auto py-16 text-white">

    <h1 class="text-3xl font-bold mb-10">
        Create Artist Profile
    </h1>

    <form wire:submit.prevent="save" class="space-y-6">

        {{-- Stage Name --}}
        <div>
            <label class="block text-sm mb-2 text-gray-400">
                Stage Name
            </label>
            <input type="text"
                   wire:model="stage_name"
                   class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500">
        </div>

        {{-- Genre --}}
        <div>
            <label class="block text-sm mb-2 text-gray-400">
                Genre
            </label>
            <input type="text"
                   wire:model="genre"
                   class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500">
        </div>

        {{-- Location --}}
        <div>
            <label class="block text-sm mb-2 text-gray-400">
                Location
            </label>
            <input type="text"
                   wire:model="location"
                   class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500">
        </div>

        {{-- Bio --}}
        <div>
            <label class="block text-sm mb-2 text-gray-400">
                Bio
            </label>
            <textarea wire:model="bio"
                      rows="4"
                      class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500"></textarea>
        </div>

        {{-- Profile Image --}}
        <div>
            <label class="block text-sm mb-2 text-gray-400">
                Profile Image
            </label>
            <input type="file"
                   wire:model="profile_image"
                   class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">
        </div>

        {{-- Cover Image --}}
        <div>
            <label class="block text-sm mb-2 text-gray-400">
                Cover Image
            </label>
            <input type="file"
                   wire:model="cover_image"
                   class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">
        </div>

        {{-- Publish Toggle --}}
        <div class="flex items-center gap-3">
            <input type="checkbox"
                   wire:model="is_published">
            <span class="text-sm text-gray-400">
                Publish profile immediately
            </span>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 transition rounded-xl py-3 font-semibold">
            Create Artist
        </button>

    </form>

</div>