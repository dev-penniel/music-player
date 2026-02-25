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

    public ?Artist $artist = null;

    public string $title = '';
    public ?string $release_date = null;
    public bool $is_published = true;

    public $audio;
    public $cover;

    public array $featuredArtists = [];
    public array $allArtists = [];

    public function mount(): void
    {
        // Get logged in user's artist
        $this->artist = Artist::where('user_id', Auth::id())->first();

        if (!$this->artist) {
            redirect()->route('artist.create');
        }

        // Load all artists for featuring
        // $this->allArtists = Artist::where('id', '!=', $this->artist->id)
        //     ->get()
        //     ->toArray();
    }

    public function upload(): void
    {
        $this->validate([
            'title' => 'required|min:2',
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

        // Attach featured artists
        foreach ($this->featuredArtists as $artistId) {
            $track->artists()->attach($artistId, [
                'role' => 'featured'
            ]);
        }

        $this->reset(['title','audio','cover','release_date','featuredArtists']);

        session()->flash('success', 'Track uploaded successfully 🚀');
    }

};
?>

<div class="max-w-2xl mx-auto py-16 text-white">

    <h1 class="text-3xl font-bold mb-10">
        {{ $this->artist->stage_name }} Upload Track
    </h1>

    @if(session()->has('success'))
        <div class="bg-green-600/20 text-green-400 p-4 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="upload" class="space-y-6">

        <input type="text"
               wire:model="title"
               placeholder="Track Title"
               class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">

        <input type="date"
               wire:model="release_date"
               class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">

        <input type="file"
               wire:model="audio"
               class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">

        <input type="file"
               wire:model="cover"
               class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">

        {{-- Featured Artists --}}
        <div>
            <label class="text-sm text-gray-400 mb-2 block">
                Featured Artists
            </label>

            <select multiple
                    wire:model="featuredArtists"
                    class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">
                @foreach($allArtists as $artist)
                    <option value="{{ $artist['id'] }}">
                        {{ $artist['stage_name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox"
                   wire:model="is_published">
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
