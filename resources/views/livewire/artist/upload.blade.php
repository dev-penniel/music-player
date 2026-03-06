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

    // IMPORTANT: do NOT type this as array
    public $allArtists;

    public function mount()
    {
        $this->artist = Artist::where('user_id', Auth::id())->first();

        if (!$this->artist) {
            return redirect()->route('artist.create');
        }

        // Keep as Collection (NOT toArray)
        $this->allArtists = Artist::where('id', '!=', $this->artist->id)
            ->orderBy('stage_name')
            ->get();
    }

    public function uploadTrack()
    {

        
        $this->validate([
            'title' => 'required|min:2|max:255',
            'audio' => 'required|mimes:mp3,wav|max:20480',
            'cover' => 'nullable|image|max:5120',
            'featuredArtists' => 'array',
            'featuredArtists.*' => 'exists:artists,id'
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
            $attachData = [];

            foreach ($this->featuredArtists as $artistId) {
                $attachData[$artistId] = ['role' => 'featured'];
            }

            $track->artists()->attach($attachData);
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

<form wire:submit.prevent="uploadTrack" enctype="multipart/form-data" class="space-y-8">

{{-- TRACK TITLE --}}
<div>
    <label class="text-sm text-gray-400 block mb-2">
        Track Title
    </label>

    <input type="text"
           wire:model="title"
           placeholder="Enter track title"
           class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 focus:border-green-500">
</div>


{{-- COVER IMAGE --}}
<div>

    <label class="text-sm text-gray-400 block mb-2">
        Cover Artwork
    </label>

    <div class="border-2 border-dashed border-gray-700 rounded-xl p-6 text-center">

        <input type="file"
               wire:model="cover"
               accept="image/*"
               class="hidden"
               id="coverUpload">

        <label for="coverUpload" class="cursor-pointer">

            @if($cover)

                <img src="{{ $cover->temporaryUrl() }}"
                     class="mx-auto w-48 h-48 object-cover rounded-xl">

            @else

                <div class="text-gray-400">
                    <div class="text-lg">Upload Cover</div>
                    <div class="text-sm">PNG / JPG</div>
                </div>

            @endif

        </label>

        <div wire:loading wire:target="cover" class="text-sm text-gray-400 mt-3">
            Uploading cover...
        </div>

    </div>

</div>


{{-- AUDIO FILE --}}
<div>

    <label class="text-sm text-gray-400 block mb-2">
        Audio File
    </label>

    <div class="border-2 border-dashed border-gray-700 rounded-xl p-6 text-center">

        <input type="file"
               wire:model="audio"
               accept="audio/*"
               class="hidden"
               id="audioUpload">

        <label for="audioUpload" class="cursor-pointer">

            <div class="text-gray-400">

                @if(!$audio)
                    <div class="text-lg">Upload Track</div>
                    <div class="text-sm">MP3 / WAV</div>
                @endif

            </div>

        </label>

        <div wire:loading wire:target="audio" class="text-sm text-gray-400 mt-3">
            Uploading audio...
        </div>

        {{-- AUDIO PLAYER PREVIEW --}}
        @if($audio)

            <div class="mt-6">
                <audio controls class="w-full">
                    <source src="{{ $audio->temporaryUrl() }}">
                </audio>

                <div class="text-xs text-gray-400 mt-2">
                    {{ $audio->getClientOriginalName() }}
                </div>
            </div>

        @endif

    </div>

</div>


{{-- RELEASE DATE --}}
<div>
    <label class="text-sm text-gray-400 block mb-2">
        Release Date
    </label>

    <input type="date"
           wire:model="release_date"
           class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3">
</div>


{{-- FEATURED ARTISTS --}}
@if($allArtists->count())

<div>

<label class="text-sm text-gray-400 block mb-3">
Featured Artists
</label>

<div class="grid grid-cols-2 gap-3">

@foreach($allArtists as $featArtist)

<label class="flex items-center gap-3 bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 hover:border-green-500 cursor-pointer">

<input type="checkbox"
       value="{{ $featArtist->id }}"
       wire:model="featuredArtists"
       class="accent-green-500">

<span class="text-sm">
{{ $featArtist->stage_name }}
</span>

</label>

@endforeach

</div>

</div>

@endif


{{-- PUBLISH --}}
<div class="flex items-center gap-3">

<input type="checkbox"
       wire:model="is_published"
       class="accent-green-500">

<span class="text-sm text-gray-400">
Publish Immediately
</span>

</div>


{{-- SUBMIT --}}
<button type="submit"
        class="w-full bg-green-500 hover:bg-green-600 transition rounded-xl py-3 font-semibold">

Upload Track

</button>

</form>