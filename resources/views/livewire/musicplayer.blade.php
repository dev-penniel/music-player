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
$data = get_object_vars($this);
$tracks = $data['tracksForJs'] ?? [];
$currentId = $data['currentTrackId'] ?? null;
@endphp

<script type="application/json" id="sp_tracks_json">{!! json_encode($tracks) !!}</script>

<div
    x-data="{
        headerColor: 'rgb(20,20,20)',
        extractColor(img) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0, img.width, img.height);
            const data = ctx.getImageData(0, 0, 1, 1).data;
            this.headerColor = `rgb(${data[0]},${data[1]},${data[2]})`;
        }
    }"
    class="min-h-screen text-white transition-colors duration-700"
    :style="`background: linear-gradient(to bottom, ${headerColor}, #000)`"
>

{{-- TOP NAV --}}
<div class="fixed top-0 left-0 right-0 backdrop-blur bg-black/40 z-40 h-20 flex items-center px-6 lg:px-10 border-b border-gray-800">
    <div class="flex items-center justify-between w-full">
        <div class="text-2xl font-bold">SoundScape Music</div>

        <div class="hidden md:block w-96">
            <input type="search"
                placeholder="Search..."
                class="w-full bg-gray-800 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-white/20">
        </div>

        <button class="bg-white text-black px-4 py-2 rounded-full text-sm font-semibold">
            Sign in
        </button>
    </div>
</div>

<div class="flex pt-20">

{{-- SIDEBAR --}}
        <aside class="hidden md:flex md:flex-col w-64 bg-black fixed top-20 left-0 h-[calc(100vh-5rem)] px-6 py-6 space-y-8 border-r border-gray-800">
            <div>
                <div class="text-xs text-gray-400 uppercase tracking-widest mb-4">
                    Your Library
                </div>

                <nav class="space-y-2 text-sm">
                    <a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-800 transition">Home</a>
                    <a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-800 transition">Browse</a>
                    <a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-800 transition">Podcast</a>
                </nav>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase tracking-widest mb-4">
                    Playlists
                </div>

                <div class="space-y-2 text-sm text-gray-300">
                    <div class="hover:text-white cursor-pointer">Liked Songs</div>
                    <div class="hover:text-white cursor-pointer">Saved Songs</div>
                    <div class="hover:text-white cursor-pointer">Local Top 50</div>
                    <div class="group relative inline-flex items-center gap-3 px-6 py-2
                                  bg-gradient-to-r from-emerald-500 to-green-600 
                                  rounded-full text-white font-semibold 
                                  cursor-pointer overflow-hidden 
                                  transition-all duration-300 ease-out 
                                  hover:scale-105 hover:shadow-2xl hover:shadow-emerald-500/30">

                          <!-- Glow Background -->
                          <span class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition duration-300"></span>

                          <!-- Icon -->
                          <svg xmlns="http://www.w3.org/2000/svg" 
                              class="w-5 h-5 transition-transform duration-300 group-hover:-translate-y-1 group-hover:scale-110"
                              fill="none" 
                              viewBox="0 0 24 24" 
                              stroke="currentColor">
                              <path stroke-linecap="round" 
                                    stroke-linejoin="round" 
                                    stroke-width="2" 
                                    d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12V4m0 0l-4 4m4-4l4 4" />
                          </svg>

                          <!-- Text -->
                          <span class="relative z-10 tracking-wide">
                              Upload Music
                          </span>

                          <!-- Shine Effect -->
                          <span class="absolute left-[-100%] top-0 h-full w-full 
                                      bg-gradient-to-r from-transparent via-white/30 to-transparent 
                                      skew-x-12 transition-all duration-700 
                                      group-hover:left-[100%]"></span>
                      </div>

                </div>
            </div>
        </aside>

{{-- MAIN --}}
<main class="flex-1 md:ml-64 px-6 lg:px-10 pb-40 space-y-16">

{{-- NEW RELEASES --}}
<section>
    <h3 class="text-2xl font-bold my-8">New Releases</h3>

    <div class="flex gap-6 overflow-x-auto pb-4">
        @foreach (collect($tracks)->take(8) as $track)
            <div wire:click="setTrack({{ $track['id'] }})"
                class="w-44 min-w-[11rem] bg-gray-900 rounded-xl p-4 hover:bg-gray-800 transition-all duration-300 group cursor-pointer relative">

                <div class="relative">
                    <img
                        x-on:load="extractColor($el)"
                        src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}"
                        class="w-full h-40 object-cover rounded-xl shadow-lg group-hover:scale-105 transition duration-500">

                    {{-- Floating Play Button --}}
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

{{-- RECOMMENDED --}}
<section>
    <h3 class="text-2xl font-bold mb-8">Recommended for you</h3>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach (collect($tracks)->shuffle()->take(10) as $track)
            <div wire:click="setTrack({{ $track['id'] }})"
                class="bg-gray-900 rounded-xl p-4 hover:bg-gray-800 transition duration-300 group cursor-pointer relative">

                <div class="relative">
                    <img
                        x-on:load="extractColor($el)"
                        src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}"
                        class="w-full h-44 object-cover rounded-xl shadow-lg group-hover:scale-105 transition duration-500">

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
  <div class="flex items-center justify-between mb-3"> 
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

</main>
</div>

{{-- MINI PLAYER --}}
<div
    class="fixed bottom-16 md:bottom-0 left-0 right-0 bg-black border-t border-gray-800 h-24 px-6 flex items-center z-50"
    x-data="playerComponentFromJson('#sp_tracks_json', {{ $currentId ?? 'null' }})"
    x-init="init()"
>

    <div class="w-full flex items-center justify-between">

        <div class="flex items-center gap-4 w-1/3">
            <img :src="currentCover" class="w-14 h-14 rounded-lg object-cover">
            <div>
                <div class="text-sm font-medium truncate" x-text="currentTitle"></div>
                <div class="text-xs text-gray-400 truncate" x-text="currentArtist"></div>
            </div>
        </div>

        <div class="flex flex-col items-center w-1/3">
            <div class="flex items-center gap-6 mb-2">
                <button @click="prev()" class="text-gray-400 hover:text-white">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                  </svg>
                </button>

                <button @click="togglePlay()"
                    class="bg-white text-black rounded-full w-10 h-10 flex items-center justify-center">
                    <span x-show="!isPlaying">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.841Z" />
                      </svg>
                    </span>
                    <span x-show="isPlaying">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M5.75 3a.75.75 0 0 0-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 0 0 .75-.75V3.75A.75.75 0 0 0 7.25 3h-1.5ZM12.75 3a.75.75 0 0 0-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 0 0 .75-.75V3.75a.75.75 0 0 0-.75-.75h-1.5Z" />
                      </svg>
                    </span>
                </button>

                <button @click="next()" class="text-gray-400 hover:text-white">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                  </svg>
                </button>
            </div>

            <div class="w-full flex items-center gap-2 text-xs text-gray-400">
                <div x-text="formatTime(currentTime)"></div>
                <div class="flex-1">
                    <div class="w-full h-1 bg-gray-700 rounded cursor-pointer" @click="seekTo($event)">
                        <div :style="`width:${progress}%`" class="h-1 bg-white rounded"></div>
                    </div>
                </div>
                <div x-text="formatTime(duration)"></div>
            </div>
        </div>

        <div class="w-1/3 flex justify-end">
            <input type="range" min="0" max="1" step="0.01"
                x-model.number="volume"
                @input="updateVolume()"
                class="w-32">
        </div>

    </div>

    <audio x-ref="audio" class="hidden"></audio>
</div>

{{-- MOBILE BOTTOM NAV --}}
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-black border-t border-gray-800 h-16 flex items-center justify-around text-xs z-40">
    <div class="flex flex-col items-center">
        🏠
        <span>Home</span>
    </div>
    <div class="flex flex-col items-center">
        🔍
        <span>Search</span>
    </div>
    <div class="flex flex-col items-center">
        📚
        <span>Library</span>
    </div>
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
