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
                   placeholder="Search songs..." 
                   class="px-4 py-2 rounded-xl bg-gray-800 text-white text-sm focus:outline-none w-60">
        </div>
    </div>

    {{-- Trending Section --}}
    <section class="mb-12">
        <h2 class="text-xl font-semibold mb-6">🔥 Trending Now</h2>

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

                    {{-- Floating Button --}}
                    <div class="absolute bottom-3 right-3 opacity-0 
                                group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-300">

                        <div wire:click.stop="setTrack({{ $track->id }})"
                             class="w-11 h-11 rounded-full flex items-center justify-center shadow-xl transition-all duration-300 cursor-pointer
                                    {{ $currentTrackId == $track->id && $isPlaying 
                                        ? 'bg-green-400 scale-110 shadow-green-500/40 shadow-2xl' 
                                        : 'bg-green-500 hover:bg-green-400 hover:scale-110' }}">

                            @if($currentTrackId != $track->id || !$isPlaying)
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 text-black">
                                    <path d="M3 3.732a1.5 1.5 0 0 1 2.305-1.265l6.706 4.267a1.5 1.5 0 0 1 0 2.531l-6.706 4.268A1.5 1.5 0 0 1 3 12.267V3.732Z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 text-black">
                                    <path d="M5.5 3.5A1.5 1.5 0 0 1 7 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5Zm5 0A1.5 1.5 0 0 1 12 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5Z" />
                                </svg>
                            @endif

                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </section>

    {{-- Tracks Grid --}}
    <section>
        <h3 class="text-lg font-semibold mb-6">Explore New Music</h3>

        <div class="flex flex-wrap gap-6 overflow-x-auto pb-4">
            @foreach($tracks as $track)
                <div class="bg-gray-900 rounded-xl p-4 hover:bg-gray-800 transition-all duration-300 group cursor-pointer relative">

                    <div class="relative w-40 h-40">
                        <img x-on:load="extractColor($el)"
                             src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}"
                             class="w-40 h-40 object-cover rounded-xl shadow-lg group-hover:scale-105 transition duration-500">

                        {{-- Floating Play Button --}}
                        <div class="absolute bottom-3 right-3 opacity-0 
                                    group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-300">

                            <div wire:click.stop="setTrack({{ $track['id'] }})"
                                 class="w-11 h-11 rounded-full flex items-center justify-center shadow-xl transition-all duration-300 cursor-pointer
                                        {{ $currentTrackId == $track['id'] && $isPlaying 
                                            ? 'bg-green-400 scale-110 shadow-green-500/40 shadow-2xl' 
                                            : 'bg-green-500 hover:bg-green-400 hover:scale-110' }}">

                                @if($currentTrackId != $track['id'] || !$isPlaying)
                                    <svg xmlns="http://www.w3.org/2000/svg" 
                                         viewBox="0 0 16 16" 
                                         fill="currentColor" 
                                         class="w-4 h-4 text-black">
                                        <path d="M3 3.732a1.5 1.5 0 0 1 2.305-1.265l6.706 4.267a1.5 1.5 0 0 1 0 2.531l-6.706 4.268A1.5 1.5 0 0 1 3 12.267V3.732Z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" 
                                         viewBox="0 0 16 16" 
                                         fill="currentColor" 
                                         class="w-4 h-4 text-black">
                                        <path d="M5.5 3.5A1.5 1.5 0 0 1 7 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5Zm5 0A1.5 1.5 0 0 1 12 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5Z" />
                                    </svg>
                                @endif

                            </div>
                        </div>

                    </div>

                    <div class="mt-4">
                        <div class="text-sm font-semibold truncate">{{ $track['title'] }}</div>
                        <div class="text-xs text-gray-400 truncate">{{ $track['artist'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</div>