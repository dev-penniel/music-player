<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Artist;
use App\Models\Track;

new
#[Layout('components.layouts.app.frontend')]
class extends Component {

    use WithFileUploads;

    public ?Artist $artist = null;

    public string $title = '';
    public ?string $release_date = null;
    public bool $is_published = true;

    public $audio;
    public $cover;

    public array $featuredArtists = [];

    public function mount(): void
    {
        $this->artist = Artist::where('user_id', Auth::id())->first();

        if (!$this->artist) {
            redirect()->route('artist.create');
        }
    }

    public function getAvailableArtistsProperty()
    {
        return Artist::where('id', '!=', $this->artist->id)
            ->where('is_published', true)
            ->orderBy('stage_name')
            ->get();
    }

    public function upload(): void
    {
        $this->validate([
            'title' => 'required|min:2|max:255',
            'audio' => 'required|mimes:mp3,wav|max:20000',
            'cover' => 'nullable|image|max:5000',
        ]);

        $audioPath = $this->audio->store('tracks', 'public');
        $coverPath = $this->cover?->store('covers', 'public');

        $track = Track::create([
            'artist_id'    => $this->artist->id,
            'title'        => $this->title,
            'slug'         => Str::slug($this->title) . '-' . uniqid(),
            'file_path'    => $audioPath,
            'cover_path'   => $coverPath,
            'release_date' => $this->release_date,
            'is_published' => $this->is_published,
            'plays'        => 0,
        ]);

        // Attach featured artists safely
        if (!empty($this->featuredArtists)) {
            $attachData = collect($this->featuredArtists)
                ->unique()
                ->mapWithKeys(fn($id) => [
                    $id => ['role' => 'featured']
                ])
                ->toArray();

            $track->artists()->syncWithoutDetaching($attachData);
        }

        $this->reset([
            'title',
            'audio',
            'cover',
            'release_date',
            'featuredArtists'
        ]);

        session()->flash('success', 'Track uploaded successfully 🚀');
    }

};
?>

<div class="max-w-3xl mx-auto py-16 text-white">

    <h1 class="text-3xl font-bold mb-10">
        {{ $this->artist->stage_name }} — Upload Track
    </h1>

    @if(session()->has('success'))
        <div class="bg-green-600/20 text-green-400 p-4 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="upload" class="space-y-6">

        {{-- Title --}}
        <div>
            <label class="text-sm text-gray-400 block mb-2">
                Track Title
            </label>
            <input type="text"
                   wire:model="title"
                   class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500">
        </div>

        {{-- Release Date --}}
        <div>
            <label class="text-sm text-gray-400 block mb-2">
                Release Date
            </label>
            <input type="date"
                   wire:model="release_date"
                   class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">
        </div>

        {{-- Audio --}}
        <div>
            <label class="text-sm text-gray-400 block mb-2">
                Audio File
            </label>
            <input type="file"
                   wire:model="audio"
                   class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">
        </div>

        {{-- Cover --}}
        <div>
            <label class="text-sm text-gray-400 block mb-2">
                Cover Image
            </label>
            <input type="file"
                   wire:model="cover"
                   class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">
        </div>

        {{-- Featured Artists --}}
        @if($this->availableArtists->count())
        <div>
            <label class="text-sm text-gray-400 block mb-3">
                Featured Artists
            </label>

            <div class="grid grid-cols-2 gap-3">
                @foreach($this->availableArtists as $artist)
                    <label class="flex items-center gap-3 bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 hover:border-green-500 cursor-pointer transition">
                        <input type="checkbox"
                               value="{{ $artist->id }}"
                               wire:model="featuredArtists"
                               class="accent-green-500">

                        <span class="text-sm">
                            {{ $artist->stage_name }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Publish --}}
        <div class="flex items-center gap-3">
            <input type="checkbox"
                   wire:model="is_published"
                   class="accent-green-500">
            <span class="text-sm text-gray-400">
                Publish immediately
            </span>
        </div>

        <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 transition rounded-xl py-3 font-semibold">
            Upload Track
        </button>

    </form>

</div>