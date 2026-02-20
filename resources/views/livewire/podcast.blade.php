<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Track;

new
#[Layout('components.layouts.app.frontend')]
class extends Component {

    public string $search = '';
    public string $genre = 'all';

    public int|null $currentTrackId = null;
    public bool $isPlaying = false;

    public array $tracksForJs = [];
    public int|null $currentIdForJs = null;

    public function mount(): void
    {
        $this->loadTracks();
    }

    public function updatedSearch()
    {
        $this->loadTracks();
    }

    public function setGenre(string $genre)
    {
        $this->genre = $genre;
        $this->loadTracks();
    }

    public function loadTracks(): void
    {
        $query = Track::query()->latest();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('artist', 'like', "%{$this->search}%");
            });
        }

        if ($this->genre !== 'all') {
            $query->where('genre', $this->genre);
        }

        $tracks = $query->get();

        $this->tracksForJs = $tracks->map(fn($t) => [
            'id' => $t->id,
            'title' => $t->title,
            'artist' => $t->artist,
            'cover_path' => $t->cover_path,
            'file_path' => $t->file_path,
            'genre' => $t->genre,
        ])->toArray();

        $this->currentTrackId = $tracks->first()?->id;
        $this->currentIdForJs = $this->currentTrackId;
    }

    public function setTrack(int $id): void
    {
        $this->currentTrackId = $id;
        $this->isPlaying = true;
        $this->currentIdForJs = $id;
        $this->dispatch('trackChanged', id: $id);
    }

    public function getGenresProperty()
    {
        return Track::pluck('genre')
            ->filter()
            ->unique()
            ->values();
    }

    public function getTrendingProperty()
    {
        return Track::inRandomOrder()->take(5)->get();
    }

};
?>

@php
$data = get_object_vars($this);
$tracks = $data['tracksForJs'] ?? [];
$currentId = $data['currentTrackId'] ?? null;
$isPlaying = $data['isPlaying'] ?? false;
@endphp

<div class="my-8">

    {{-- Filters + Search --}}
    <div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
        <div class="flex items-center gap-2">
            <select wire:model="genre" class="px-4 py-2 rounded-xl bg-gray-800 text-white text-sm focus:outline-none">
                <option value="all">All Genres</option>
                @foreach($this->genres as $g)
                    <option value="{{ $g }}">{{ ucfirst($g) }}</option>
                @endforeach
            </select>
            <input wire:model.debounce.500ms="search" 
                   type="text" 
                   placeholder="Search pods..." 
                   class="px-4 py-2 rounded-xl bg-gray-800 text-white text-sm focus:outline-none w-60">
        </div>
    </div>

    {{-- Trending Section --}}
    <section class="mb-12">
        <h2 class="text-xl font-semibold mb-6">🔥 Podcasts</h2>

        <div class="grid md:grid-cols-5 gap-6">
            @foreach($this->trending as $track)
                <div class="relative rounded-2xl overflow-hidden group cursor-pointer">
                    <img src="{{ $track->cover_path
                                ? asset('storage/'.$track->cover_path)
                                : asset('images/default-cover.jpg') }}"
                         class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">

                    <div class="absolute inset-0 bg-black/40 opacity-0
                                group-hover:opacity-100 transition"></div>

                    <div class="absolute bottom-4 left-4">
                        <div class="text-sm font-semibold">{{ $track->title }}</div>
                        <div class="text-xs text-gray-300">{{ $track->artist }}</div>
                    </div>

                </div>
            @endforeach
        </div>
    </section>

</div>