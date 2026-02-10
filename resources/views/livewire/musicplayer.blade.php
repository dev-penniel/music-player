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

      dd('hello');

        $this->currentTrackId = $id;
        $this->currentIdForJs = $id;
        $this->emit('trackChanged', $id);
    }

};
?>

@php
// Access component variables via get_object_vars($this)
$data = get_object_vars($this);
$tracks = $data['tracksForJs'] ?? [];
$currentId = $data['currentTrackId'] ?? null;
@endphp

<!-- JSON data for Alpine -->
<script type="application/json" id="sp_tracks_json">{!! json_encode($tracks) !!}</script>

<div class="">
  <div class="">

    {{-- resources/views/music/home.blade.php --}}

<div class="min-h-screen bg-gray-900 text-white">
  <div class="max-w-7xl mx-auto px-4">

    {{-- Top nav --}}
    <div class="flex bg-gray-900 fixed z-10 p-5  items-center justify-between mb-6 w-full">
      <div class="flex conteiner items-center gap-4">
        <div class="text-2xl font-bold">SoundScape</div>
        <div class="hidden md:block text-gray-400">Discover • Play • Share</div>
      </div>

      <div class="flex items-center gap-3">
        <div class="hidden sm:block">
          <input type="search" placeholder="Search songs, artists, albums"
                 class="bg-gray-800 rounded-full px-3 py-2 text-sm w-64 focus:outline-none focus:ring-1 focus:ring-gray-600" />
        </div>
        <button class="text-xs bg-gray-800 px-3 py-2 rounded hover:bg-gray-700">Sign in</button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 pt-22">

      {{-- Left sidebar (playlists, quick nav) --}}
      {{-- Left sidebar (playlists, quick nav) - improved --}}
<aside class="hidden md:block md:col-span-1">
  <div class="bg-gray-800 rounded-lg p-3 space-y-3 fixed w-55">
    <div class="text-xs text-gray-400 uppercase tracking-wider">Your library</div>

    <nav class="space-y-1" aria-label="Primary">
      <a href="#"
         class="flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-700 text-xs text-gray-200 transition active:scale-95"
         title="Home">
        <!-- Home icon -->
        <svg class="w-4 h-4 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 4l9 5.75V20a1 1 0 0 1-1 1h-5.25a1 1 0 0 1-1-1v-4.25a1 1 0 0 0-1-1H10.25a1 1 0 0 0-1 1V20a1 1 0 0 1-1 1H3.999A1 1 0 0 1 3 20V9.75z"/>
        </svg>
        <span class="truncate">Home</span>
      </a>

      <a href="#"
         class="flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-700 text-xs text-gray-200 transition active:scale-95"
         title="Browse">
        <!-- Compass / browse icon -->
        <svg class="w-4 h-4 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.364-6.364-2.121 2.121M8.757 15.243l-2.121 2.121m12.728 0-2.121-2.121M8.757 8.757 6.636 6.636"/>
        </svg>
        <span class="truncate">Browse</span>
      </a>

      <a href="#"
         class="flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-700 text-xs text-gray-200 transition active:scale-95"
         title="Radio">
        <!-- Radio icon -->
        <svg class="w-4 h-4 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15a1 1 0 0 0 1-1v-7.5a1 1 0 0 0-1-1h-15a1 1 0 0 0-1 1V18.5a1 1 0 0 0 1 1zM7 8.5V6.5A3.5 3.5 0 0 1 10.5 3h3A3.5 3.5 0 0 1 17 6.5v2"/>
        </svg>
        <span class="truncate">Radio</span>
      </a>

      <a href="#"
         class="flex items-center gap-3 px-2 py-2 rounded hover:bg-gray-700 text-xs text-gray-200 transition active:scale-95"
         title="Made for you">
        <!-- Sparkles / curated -->
        <svg class="w-4 h-4 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l1.5 3 3 1.5-3 1.5L13.5 12 12 9.75 10.5 12 9 9.75 6 8.25 9 6.75 10.5 3 12 3zM6 18a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
        </svg>
        <span class="truncate">Made for you</span>
      </a>
    </nav>

    <div class="pt-2 border-t border-gray-700">
      <div class="text-xs text-gray-400 mb-2 uppercase tracking-wider">Quick playlists</div>

      <div class="space-y-1">
        <button class="w-full flex items-center gap-3 px-2 py-2 rounded text-xs text-gray-200 hover:bg-gray-700 transition active:scale-95" title="Liked songs">
          <svg class="w-4 h-4 text-pink-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 21s-7-4.35-9-7.06C1.38 11.93 3 8.5 6 7c2-1.2 3.5-.5 6 2 2.5-2.5 4-3.2 6-2 3 1.5 4.62 4.94 3 6.94C19 16.65 12 21 12 21z"/>
          </svg>
          <span class="truncate">Liked Songs</span>
        </button>

        <button class="w-full flex items-center gap-3 px-2 py-2 rounded text-xs text-gray-200 hover:bg-gray-700 transition active:scale-95" title="Top 50">
          <svg class="w-4 h-4 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
          </svg>
          <span class="truncate">Top 50</span>
        </button>

        <button class="w-full flex items-center gap-3 px-2 py-2 rounded text-xs text-gray-200 hover:bg-gray-700 transition active:scale-95" title="Chill playlist">
          <svg class="w-4 h-4 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3"/>
          </svg>
          <span class="truncate">Upload your Music</span>
        </button>
      </div>
    </div>
  </div>
</aside>


      {{-- Main area --}}
      <main class="md:col-span-4 space-y-6">

        {{-- Hero / Featured --}}
        {{-- <section class="bg-gradient-to-r from-gray-800 to-gray-700 rounded-lg p-6 flex items-center gap-6">
          <div class="w-36 h-36 rounded overflow-hidden shadow">
            <img
              src="{{ $tracks[0]['cover_path'] ? asset('storage/'.$tracks[0]['cover_path']) : asset('images/default-cover.jpg') }}"
              alt="{{ $tracks[0]['title'] ?? 'Featured' }}"
              class="w-full h-full object-cover"
            />
          </div>
          <div class="flex-1">
            <div class="text-sm text-gray-400">Featured</div>
            <div class="text-2xl font-semibold mt-1">{{ $tracks[0]['title'] ?? 'Featured Track' }}</div>
            <div class="text-sm text-gray-400 mt-1">{{ $tracks[0]['artist'] ?? '' }} · {{ $tracks[0]['album'] ?? '' }}</div>

            <div class="mt-4 flex items-center gap-3">
              <button @click="void(0)" wire:click="setTrack({{ $tracks[0]['id'] ?? 'null' }})" class="bg-white text-gray-900 rounded-full px-4 py-2 font-medium shadow">Play</button>
              <button @click="void(0)" class="px-3 py-2 rounded bg-gray-800 hover:bg-gray-700 text-sm">Save</button>
            </div>
          </div>
        </section> --}}

        {{-- <section class="relative w-full rounded-lg overflow-hidden h-30 sm:h-62 md:h-50 bg-gray-800">
          <img
              src="{{ $tracks[0]['cover_path'] ? asset('storage/'.$tracks[1]['cover_path']) : asset('images/default-cover.jpg') }}"
              alt="Featured Cover"
              class="w-full h-full object-cover opacity-100"
          /> --}}

          {{-- subtle gradient overlay for a polished look --}}
          {{-- <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent pointer-events-none"></div>
      </section> --}}


        {{-- Curated rows (horizontal scroll) --}}
        {{-- Helper: we'll derive lists from $tracks using Blade collection helpers --}}

        {{-- New Releases --}}
        <section>
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">New Releases</h3>
            <a href="#" class="text-xs text-gray-400">See all</a>
          </div>

          <div class="flex gap-4 overflow-x-auto pb-2">
            @foreach (collect($tracks)->take(8) as $track)
              <div wire:click="setTrack({{ $track['id'] }})" class="w-44 min-w-[11rem]  rounded-lg p-3 hover:bg-gray-800 cursor-pointer">
                <div class="w-full h-36  rounded overflow-hidden mb-3">
                  <img src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}" alt="{{ $track['title'] }}" class="w-full h-full object-cover">
                </div>
                <div class="text-sm text-center font-medium truncate">{{ $track['title'] }}</div>
                <div class="text-xs text-center text-gray-400 truncate">{{ $track['artist'] }}</div>
              </div>
            @endforeach
          </div>
        </section>

        {{-- Recommended for you (random-ish selection) --}}
        <section>
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">Recommended for you</h3>
            <a href="#" class="text-xs text-gray-400">Refresh</a>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach (collect($tracks)->shuffle()->take(8) as $track)
              <div  wire:click="setTrack({{ $track['id'] }})" class="bg-gray-800 rounded-lg p-3 hover:bg-gray-700 cursor-pointer">
                <div class="w-full h-50 rounded overflow-hidden mb-3">
                  <p>{{ $track['id'] }}</p>
                  <img src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}" alt="{{ $track['title'] }}" class="w-full h-full object-cover">
                </div>
                <div class="text-sm font-medium truncate">{{ $track['title'] }}</div>
                <div class="text-xs text-gray-400 truncate">{{ $track['artist'] }}</div>
              </div>
            @endforeach
          </div>
        </section>

        {{-- Artists (derived from tracks) --}}
        <section>
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">Artists</h3>
            <a href="#" class="text-xs text-gray-400">See all</a>
          </div>

          <div class="flex gap-4 overflow-x-auto pb-2">
            @foreach (collect($tracks)->pluck('artist')->filter()->unique()->take(10) as $artist)
              {{-- find first track for this artist to get a cover --}}
              @php $aTrack = collect($tracks)->first(fn($t) => ($t['artist'] ?? '') === $artist); @endphp
              <div class="min-w-[10rem] w-40  rounded-lg p-3 hover:bg-gray-800 cursor-pointer">
                <div class="w-full h-34 rounded-full overflow-hidden mb-3">
                  <img src="{{ ($aTrack && $aTrack['cover_path']) ? asset('storage/'.$aTrack['cover_path']) : asset('images/default-cover.jpg') }}" alt="{{ $artist }}" class="w-full h-full object-cover">
                </div>
                <div class="text-sm text-center font-medium truncate">{{ $artist }}</div>
                <div class="text-xs text-center text-gray-400 truncate">Artist</div>
              </div>
            @endforeach
          </div>
        </section>

        {{-- Genres (if present in $tracks as 'genre') --}}
        @if(collect($tracks)->pluck('genre')->filter()->isNotEmpty())
          <section>
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-lg font-semibold">Genres</h3>
              <a href="#" class="text-xs text-gray-400">Explore</a>
            </div>

            <div class="flex gap-4 overflow-x-auto pb-2">
              @foreach (collect($tracks)->pluck('genre')->filter()->unique()->take(8) as $genre)
                <div class="min-w-[10rem] w-40 bg-gray-800 rounded-lg p-3 hover:bg-gray-700 cursor-pointer">
                  <div class="w-full h-20 rounded overflow-hidden mb-3 flex items-center justify-center bg-gray-900">
                    <div class="text-sm font-semibold">{{ ucfirst($genre) }}</div>
                  </div>
                  <div class="text-sm font-medium truncate">{{ ucfirst($genre) }}</div>
                  <div class="text-xs text-gray-400">Genre</div>
                </div>
              @endforeach
            </div>
          </section>
        @endif

        {{-- Library (your track list) --}}
        <section>
          <div class="flex items-center justify-between mb-10">
            <h3 class="text-lg font-semibold">Library</h3>
            <a href="#" class="text-xs text-gray-400">Manage</a>
          </div>

          <div class="bg-gray-800 rounded-lg p-4">
            <div class="space-y-3">
              @foreach ($tracks as $track)
                <div wire:click="setTrack({{ $track['id'] }})"
                     class="flex items-center gap-4 p-3 rounded hover:bg-gray-700 cursor-pointer {{ $currentId === $track['id'] ? 'bg-gray-700' : '' }}">
                  <img src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}" class="w-14 h-14 rounded object-cover">
                  <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate">{{ $track['title'] }}</div>
                    <div class="text-xs text-gray-400 truncate">{{ $track['artist'] }} · {{ $track['album'] }}</div>
                  </div>
                  <div class="text-xs text-gray-400">
                    @php
                      $m = floor(($track['duration'] ?? 0) / 60);
                      $s = str_pad(($track['duration'] ?? 0) % 60, 2, '0', STR_PAD_LEFT);
                    @endphp
                    {{ $m }}:{{ $s }}
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </section>

      </main>

    </div> {{-- grid end --}}
  </div> {{-- container end --}}

</div>


    <!-- Now Playing -->
    {{-- <div class="bg-gray-800 rounded-lg p-4 flex flex-col">
      <div class="text-sm text-gray-400 mb-2">Now Playing</div>

      <div
        x-data="playerComponentFromJson('#sp_tracks_json', {{ $currentId ?? 'null' }})"
        x-init="init()"
        class="flex-1 flex flex-col"
      >
        <div class="flex items-center gap-4">
          <img :src="currentCover" alt="cover" class="w-28 h-28 rounded object-cover">
          <div>
            <div class="text-lg font-semibold" x-text="currentTitle"></div>
            <div class="text-sm text-gray-400" x-text="currentArtist"></div>
          </div>
        </div>

        <!-- Controls -->
        <div class="mt-6">
          <div class="flex items-center justify-center gap-6">
            <button @click="prev()" class="p-2 hover:text-white text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
              </svg>


            </button>

            <button @click="togglePlay()" class="bg-white text-gray-900 rounded-full w-14 h-14 flex items-center justify-center shadow">
              <template x-if="!isPlaying">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M5 3.868v16.264L19 12 5 3.868z"/></svg>
              </template>
              <template x-if="isPlaying">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
              </template>
            </button>

            <button @click="next()" class="p-2 hover:text-white text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
              </svg>
            </button>
          </div>

          <!-- Progress -->
          <div class="mt-6">
            <div class="flex items-center gap-2 text-xs text-gray-400">
              <div x-text="formatTime(currentTime)"></div>
              <div class="flex-1">
                <div class="w-full relative h-1 bg-gray-700 rounded" @click="seekTo($event)">
                  <div :style="`width:${progress}%`" class="h-1 bg-gray-900 rounded"></div>
                </div>
              </div>
              <div x-text="formatTime(duration)"></div>
            </div>
          </div>

        
          <div class="mt-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor"><path d="M5 9v6h4l5 5V4L9 9H5z"/></svg>
            <input type="range" min="0" max="1" step="0.01" x-model.number="volume" @input="updateVolume()" class="w-full">
          </div>
        </div>

        <audio x-ref="audio" class="hidden"></audio>
      </div>
    </div> --}}
  </div>

  <!-- Mini player -->
  <!-- Mini player (drop-in replacement) -->
<div
  class="fixed bottom-0 left-0 right-0 bg-gray-800 border-t border-gray-700 p-2 z-50"
  x-data="playerComponentFromJson('#sp_tracks_json', {{ $currentId ?? 'null' }})"
  x-init="init()"
  x-on:toggle-shuffle.window="(typeof toggleShuffle === 'function') ? toggleShuffle() : (shuffle = !shuffle)"
  x-on:volume-change.window="(typeof setVolume === 'function') ? setVolume($event.detail) : (volume = $event.detail)"
>
  <div class="max-w-6xl mx-auto flex items-center gap-4">
    <!-- cover: uses Alpine currentCover, falls back to blade asset if not set -->
    <img
      :src="currentCover || '{{ $tracks[0]['cover_path'] ?? asset('images/default-cover.jpg') }}'"
      alt="cover"
      class="w-12 h-12 rounded object-cover"
    />

    <!-- title / artist -->
    <div class="flex-1 min-w-0">
      <div class="text-sm mb-1 text-gray-400 font-medium truncate" x-text="currentTitle || '{{ $tracks[0]['title'] ?? '—' }}'"></div>
      <div class="text-xs text-gray-500 truncate" x-text="currentArtist || '{{ $tracks[0]['artist'] ?? '' }}'"></div>
    </div>

    <!-- play/pause + shuffle + volume + prev/next -->
    <div class="flex items-center gap-2">

      <!-- shuffle -->
      <button
        @click="$dispatch('toggle-shuffle')"
        :class="(typeof shuffle !== 'undefined' && shuffle) ? 'bg-gray-700 text-white' : 'text-gray-300'"
        class="p-2 rounded hover:bg-gray-700 hover:text-white active:scale-95 transition-transform"
        title="Shuffle"
        aria-pressed="false"
        x-bind:aria-pressed="(typeof shuffle !== 'undefined' && shuffle) ? 'true' : 'false'"
      >
        <!-- simple shuffle icon -->
        <flux:icon.musical-note />
      </button>

      <!-- prev (optional) -->
      <button
        @click="prev()"
        class="p-2 hover:text-white text-gray-300 rounded hover:bg-gray-700 active:scale-95 transition-transform"
        title="Previous"
        aria-label="Previous track"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
      </button>

      <!-- play/pause -->
      <button
        @click="togglePlay()"
        :title="isPlaying ? 'Pause' : 'Play'"
        class="bg-white text-gray-900 rounded-full w-12 h-12 flex items-center justify-center shadow active:scale-95 transition-transform"
        aria-label="Play or pause"
      >
        <template x-if="!isPlaying">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M5 3.868v16.264L19 12 5 3.868z"/></svg>
        </template>
        <template x-if="isPlaying">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
        </template>
      </button>

      <!-- next (optional) -->
      <button
        @click="next()"
        class="p-2 hover:text-white text-gray-300 rounded hover:bg-gray-700 active:scale-95 transition-transform"
        title="Next"
        aria-label="Next track"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
      </button>

      <!-- volume (dropdown) -->
      <div class="relative" x-data="{ openVolume: false, localVol: (typeof volume !== 'undefined' ? volume : 0.8) }" @keydown.escape="openVolume = false" @click.away="openVolume = false">
        <button
          @click="openVolume = !openVolume"
          :class="(typeof volume !== 'undefined' && volume > 0) || (localVol > 0) ? 'text-white' : 'text-gray-400'"
          class="p-2 rounded hover:bg-gray-700 hover:text-white active:scale-95 transition-transform"
          title="Volume"
          aria-haspopup="true"
          :aria-expanded="openVolume ? 'true' : 'false'"
        >
          <!-- heroicons style speaker icon -->
          <flux:icon.speaker-wave class="size-5" />

        </button>

        <!-- dropdown -->
        <div
          x-show="openVolume"
          x-transition
          class="absolute right-0 bottom-12 w-44 bg-gray-800 border border-gray-700 rounded p-3 shadow-lg"
          style="display: none;"
        >
          <div class="text-xs text-gray-400 mb-2">Volume</div>
          <div class="flex items-center gap-2">
            <!-- small speaker icon -->
            <flux:icon.speaker-wave />

            <!-- volume slider -->
            <input
              type="range"
              min="0"
              max="1"
              step="0.01"
              x-model.number="localVol"
              @input="$dispatch('volume-change', localVol)"
              class="w-full accent-white"
              aria-label="Volume slider"
            />
          </div>

          <div class="mt-2 text-xs text-gray-400">
            <button
              @click="localVol = 0; $dispatch('volume-change', 0)"
              class="text-xs px-2 py-1 rounded hover:bg-gray-700"
            >Mute</button>
            <button
              @click="localVol = 1; $dispatch('volume-change', 1)"
              class="text-xs px-2 py-1 rounded hover:bg-gray-700"
            >Max</button>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- optional small progress bar under the player -->
  <div class="max-w-6xl mx-auto mt-2 px-2">
    <div class="flex items-center gap-2 text-xs text-gray-400">
      <div x-text="formatTime(currentTime)"></div>
      <div class="flex-1">
        <div class="w-full relative h-1 bg-gray-700 rounded cursor-pointer" @click="seekTo($event)">
          <div :style="`width:${progress}%`" class="h-1 bg-gray-900 rounded"></div>
        </div>
      </div>
      <div x-text="formatTime(duration)"></div>
    </div>
  </div>

  <!-- keep the audio element reference inside this component so the component can control it -->
  <audio x-ref="audio" class="hidden"></audio>
</div>


  <!-- optional small progress bar under the player -->
  <div class="max-w-6xl mx-auto mb-20 mt-2 px-2">
    <div class="flex items-center gap-2 text-xs text-gray-400">
      <div x-text="formatTime(currentTime)"></div>
      <div class="flex-1">
        <div class="w-full relative h-1 bg-gray-700 rounded cursor-pointer" @click="seekTo($event)">
          <div :style="`width:${progress}%`" class="h-1 bg-gray-900 rounded"></div>
        </div>
      </div>
      <div x-text="formatTime(duration)"></div>
    </div>
  </div>

  <!-- keep the audio element reference inside this component so the component can control it -->
  <audio x-ref="audio" class="hidden"></audio>
</div>

</div>

<script>
function playerComponentFromJson(jsonSelector, currentId) {
    const script = document.querySelector(jsonSelector);
    let tracks = [];
    try { tracks = script ? JSON.parse(script.textContent) : []; } 
    catch (e) { console.error('Invalid tracks JSON', e); tracks = []; }

    return {
        tracks: tracks || [],
        currentTrack: null,
        currentIndex: 0,
        currentTitle: '',
        currentArtist: '',
        currentCover: '',
        isPlaying: false,
        progress: 0,
        duration: 0,
        currentTime: 0,
        volume: 0.8,

        init() {
            if (currentId !== null) this.currentIndex = this.tracks.findIndex(t => t.id === currentId);
            if (this.currentIndex < 0) this.currentIndex = 0;
            this.loadTrack(this.currentIndex);

            Livewire.on('trackChanged', id => { const idx = this.tracks.findIndex(t => t.id === id); if (idx>=0){ this.loadTrack(idx); this.play(); } });

            const audio = this.$refs.audio;
            audio.volume = this.volume;

            audio.addEventListener('timeupdate', ()=>{ this.currentTime = Math.floor(audio.currentTime); this.duration = Math.floor(audio.duration||0); this.progress = this.duration?(audio.currentTime/audio.duration)*100:0; });
            audio.addEventListener('ended', ()=>{ this.next(); });
        },

        loadTrack(index) {
            if (!this.tracks[index]) return;
            this.currentIndex = index;
            const t = this.tracks[index];
            this.currentTrack = t;
            this.currentTitle = t.title;
            this.currentArtist = t.artist;
            this.currentCover = t.cover_path?(`/storage/${t.cover_path}`):'/images/default-cover.jpg';
            const audio = this.$refs.audio;
            audio.src = `/storage/${t.file_path}`;
            audio.load();
            this.isPlaying = false;

            const miniCover = document.getElementById('miniCover');
            const miniTitle = document.getElementById('miniTitle');
            const miniArtist = document.getElementById('miniArtist');
            if(miniCover) miniCover.src=this.currentCover;
            if(miniTitle) miniTitle.textContent=this.currentTitle;
            if(miniArtist) miniArtist.textContent=this.currentArtist;
        },

        play() { const audio=this.$refs.audio; const p=audio.play(); if(p&&typeof p.then==='function'){p.then(()=>{this.isPlaying=true;}).catch(()=>{this.isPlaying=false;});}else{this.isPlaying=true;} },
        pause(){ this.$refs.audio.pause(); this.isPlaying=false; },
        togglePlay(){ if(this.isPlaying) this.pause(); else this.play(); },
        next(){ let idx=(this.currentIndex+1)%this.tracks.length; this.loadTrack(idx); this.play(); },
        prev(){ let idx=this.currentIndex-1; if(idx<0) idx=this.tracks.length-1; this.loadTrack(idx); this.play(); },
        seekTo(e){ const rect=e.currentTarget.getBoundingClientRect(); const ratio=(e.clientX-rect.left)/rect.width; const audio=this.$refs.audio; if(audio.duration) audio.currentTime=ratio*audio.duration; },
        updateVolume(){ this.$refs.audio.volume=this.volume; },
        formatTime(seconds){ if(!seconds||isNaN(seconds)) return '0:00'; const m=Math.floor(seconds/60); const s=Math.floor(seconds%60).toString().padStart(2,'0'); return `${m}:${s}`; }
    }
}
</script>
