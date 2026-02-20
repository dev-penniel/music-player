<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;
use App\Models\Track;
use Livewire\Attributes\Layout;

new
#[Layout('components.layouts.app.frontend')]
class extends Component {

    public int|null $currentTrackId = null;
    public bool $shuffle = false;
    public bool $isPlaying = false;

    public array $tracksForJs = [];
    public int|null $currentIdForJs = null;

    public function mount(): void
    {
        $tracks = Track::orderBy('id', 'desc')->get();
        $this->tracksForJs = $tracks->map(fn($t) => [
            'id' => $t->id,
            'title' => $t->title,
            'artist' => $t->artist,
            'album' => $t->album,
            'file_path' => $t->file_path,
            'cover_path' => $t->cover_path,
            'duration' => $t->duration,
        ])->all();

        $first = $tracks->first();
        $this->currentTrackId = $first ? $first->id : null;
        $this->currentIdForJs = $this->currentTrackId;
    }

    #[On('tracks-updated')]
    public function getTracksProperty()
    {
        return Track::orderBy('id')->get();
    }

    public function setTrack(int $id): void
    {
        $this->currentTrackId = $id;
        $this->currentIdForJs = $id;

        $this->dispatch('trackChanged', id: $id);
    }

};
?>

@php
$data = get_object_vars($this);
$tracks = $data['tracksForJs'] ?? [];
$currentId = $data['currentTrackId'] ?? null;
@endphp

<script type="application/json" id="sp_tracks_json">{!! json_encode($tracks) !!}</script>

<div>

    
{{-- NEW RELEASES --}}
<section>
    <div class="flex items-center justify-between my-8"> 
        <h3 class="text-lg font-semibold">New Releases</h3> 
        <a href="#" class="text-xs text-gray-400">See all</a> 
    </div> 

    <div class="flex flex-wrap gap-6 overflow-x-auto pb-4">
        @foreach (collect($tracks)->take(6) as $track)
            <div
                class="bg-gray-900 rounded-xl p-4 hover:bg-gray-800 transition-all duration-300 group cursor-pointer relative">

                <div class="relative w-40 h-40">
                    <img
                        x-on:load="extractColor($el)"
                        src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}"
                        class="w-40 h-40 object-cover rounded-xl shadow-lg group-hover:scale-105 transition duration-500">

                    {{-- Floating Play Button --}}
<div class="absolute bottom-3 right-3 opacity-0 
            group-hover:opacity-100 
            group-hover:translate-y-0 
            translate-y-4 transition-all duration-300">

    <div
        wire:click.stop="setTrack({{ $track['id'] }})"
        class="w-11 h-11 rounded-full flex items-center 
               justify-center shadow-xl transition-all duration-300 cursor-pointer
               
               {{ $currentTrackId == $track['id'] && $isPlaying 
                    ? 'bg-green-400 scale-110 shadow-green-500/40 shadow-2xl' 
                    : 'bg-green-500 hover:bg-green-400 hover:scale-110' }}">

        {{-- PLAY ICON --}}
        @if($currentTrackId != $track['id'] || !$isPlaying)
            <svg xmlns="http://www.w3.org/2000/svg" 
                 viewBox="0 0 16 16" 
                 fill="currentColor" 
                 class="w-4 h-4 text-black">
                <path d="M3 3.732a1.5 1.5 0 0 1 2.305-1.265l6.706 4.267a1.5 1.5 0 0 1 0 2.531l-6.706 4.268A1.5 1.5 0 0 1 3 12.267V3.732Z" />
            </svg>
        @else
        {{-- PAUSE ICON --}}
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

{{-- RECOMMENDED --}}
<section>
    <div class="flex items-center justify-between my-8"> 
        <h3 class="text-lg font-semibold">Recommended for you</h3> 
        <a href="#" class="text-xs text-gray-400">See all</a> 
    </div> 

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach (collect($tracks)->shuffle()->take(10) as $track)
            <div wire:click="setTrack({{ $track['id'] }})"
                class="bg-gray-900 rounded-xl p-4 hover:bg-gray-800 transition duration-300 group cursor-pointer relative">

                <div class="relative">
                    <img
                        x-on:load="extractColor($el)"
                        src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}"
                        class="w-full object-cover rounded-xl shadow-lg group-hover:scale-105 transition duration-500">

                    <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 group-hover:translate-y-0 translate-y-4 transition-all duration-300">
                        <div class="bg-green-500 w-10 h-10 rounded-full flex items-center justify-center shadow-xl hover:scale-110 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                              <path d="M3 3.732a1.5 1.5 0 0 1 2.305-1.265l6.706 4.267a1.5 1.5 0 0 1 0 2.531l-6.706 4.268A1.5 1.5 0 0 1 3 12.267V3.732Z" />
                            </svg>
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

{{-- Artists (derived from tracks) --}} 
<section> 
  <div class="flex items-center justify-between my-8"> 
    <h3 class="text-lg font-semibold">Artists</h3> 
    <a href="#" class="text-xs text-gray-400">See all</a> 
  </div> 
  <div class="flex gap-4 overflow-x-auto pb-2"> @foreach (collect($tracks)->pluck('artist')->filter()->unique()->take(10) as $artist) {{-- find first track for this artist to get a cover --}} @php $aTrack = collect($tracks)->first(fn($t) => ($t['artist'] ?? '') === $artist); @endphp 
    <div class="min-w-[10rem] w-40 rounded-lg p-3 hover:bg-gray-800 cursor-pointer"> 
      <div class="w-full h-34 rounded-full overflow-hidden mb-3"> 
        <img src="{{ ($aTrack && $aTrack['cover_path']) ? asset('storage/'.$aTrack['cover_path']) : asset('images/default-cover.jpg') }}" alt="{{ $artist }}" class="w-full h-full object-cover"> 
      </div> 
      <div class="text-sm text-center font-medium truncate">{{ $artist }}</div>
      <div class="text-xs text-center text-gray-400 truncate">Artist</div> 
    </div> @endforeach </div> 
  </section> {{-- Genres (if present in $tracks as 'genre') --}} @if(collect($tracks)->pluck('genre')->filter()->isNotEmpty()) 
  <section> 
    <div class="flex items-center justify-between mb-3"> 
      <h3 class="text-lg font-semibold">Genres</h3> 
      <a href="#" class="text-xs text-gray-400">Explore</a> 
    </div> 
    <div class="flex gap-4 overflow-x-auto pb-2"> @foreach (collect($tracks)->pluck('genre')->filter()->unique()->take(8) as $genre) 
      <div class="min-w-[10rem] w-40 bg-gray-800 rounded-lg p-3 hover:bg-gray-700 cursor-pointer"> <div class="w-full h-20 rounded overflow-hidden mb-3 flex items-center justify-center bg-gray-900"> 
        <div class="text-sm font-semibold">{{ ucfirst($genre) }}</div> 
      </div> <div class="text-sm font-medium truncate">{{ ucfirst($genre) }}</div> 
      <div class="text-xs text-gray-400">Genre</div> 
    </div> @endforeach 
  </div> 
</section> @endif

</div>