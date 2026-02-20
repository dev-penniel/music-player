<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Your App</title>

  {{-- existing head content --}}
  @include('partials.head')

  {{-- REQUIRED for Livewire --}}
  @livewireStyles
</head>
<body class="bg-gray-900 text-gray-100">

    <div
    x-data="{}"
    class="min-h-screen text-white transition-colors duration-700"
    :style="`background: linear-gradient(to bottom, #111, #000)`"
>

{{-- TOP NAV --}}
<div class="fixed top-0 left-0 right-0 backdrop-blur bg-black/40 z-40 h-20 flex items-center px-6 lg:px-10 border-b border-gray-800">
    <div class="flex items-center justify-between w-full">
        <div class="text-2xl font-bold">MusicPlayer</div>

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

    

        {{-- Your Library --}}
<div>
    <div class="text-xs text-gray-400 uppercase tracking-widest mb-4">
        Your Library
    </div>

    <nav class="space-y-2 text-sm">
        <a href="{{ route('home') }}" 
           wire:navigate
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('home') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 text-gray-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
            </svg>
            Home
        </a>

        <a href="{{ route('explore') }}" 
           wire:navigate
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('explore') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 text-gray-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                <path fill-rule="evenodd" d="M13.5 4.938a7 7 0 1 1-9.006 1.737c.202-.257.59-.218.793.039.278.352.594.672.943.954.332.269.786-.049.773-.476a5.977 5.977 0 0 1 .572-2.759 6.026 6.026 0 0 1 2.486-2.665c.247-.14.55-.016.677.238A6.967 6.967 0 0 0 13.5 4.938ZM14 12a4 4 0 0 1-4 4c-1.913 0-3.52-1.398-3.91-3.182-.093-.429.44-.643.814-.413a4.043 4.043 0 0 0 1.601.564c.303.038.531-.24.51-.544a5.975 5.975 0 0 1 1.315-4.192.447.447 0 0 1 .431-.16A4.001 4.001 0 0 1 14 12Z" clip-rule="evenodd" />
            </svg>
            Explore
        </a>

        <a href="{{ route('podcast') }}" 
           wire:navigate
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('podcast') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 text-gray-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                <path d="M7 4a3 3 0 0 1 6 0v6a3 3 0 1 1-6 0V4Z" />
                <path d="M5.5 9.643a.75.75 0 0 0-1.5 0V10c0 3.06 2.29 5.585 5.25 5.954V17.5h-1.5a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-1.5v-1.546A6.001 6.001 0 0 0 16 10v-.357a.75.75 0 0 0-1.5 0V10a4.5 4.5 0 0 1-9 0v-.357Z" />
            </svg>
            Podcast
        </a>
    </nav>
</div>

    {{-- Playlists --}}
    <div>
        <div class="text-xs text-gray-400 uppercase tracking-widest mb-4">
            Playlists
        </div>

        <div class="space-y-2 text-sm text-gray-300">
            <div class="flex items-center gap-2 hover:text-white cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                  <path d="M2 6.342a3.375 3.375 0 0 1 6-2.088 3.375 3.375 0 0 1 5.997 2.26c-.063 2.134-1.618 3.76-2.955 4.784a14.437 14.437 0 0 1-2.676 1.61c-.02.01-.038.017-.05.022l-.014.006-.004.002h-.002a.75.75 0 0 1-.592.001h-.002l-.004-.003-.015-.006a5.528 5.528 0 0 1-.232-.107 14.395 14.395 0 0 1-2.535-1.557C3.564 10.22 1.999 8.558 1.999 6.38L2 6.342Z" />
                </svg>


                Liked Songs
            </div>

            <div class="flex items-center gap-2 hover:text-white cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                  <path d="M3.75 2a.75.75 0 0 0-.75.75v10.5a.75.75 0 0 0 1.28.53L8 10.06l3.72 3.72a.75.75 0 0 0 1.28-.53V2.75a.75.75 0 0 0-.75-.75h-8.5Z" />
                </svg>



                Saved Songs
            </div>

            <div class="flex items-center gap-2 hover:text-white cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                    <path fill-rule="evenodd" d="M3.75 3.5c0 .563.186 1.082.5 1.5H2a1 1 0 0 0 0 2h5.25V5h1.5v2H14a1 1 0 1 0 0-2h-2.25A2.5 2.5 0 0 0 8 1.714 2.5 2.5 0 0 0 3.75 3.5Zm3.499 0v-.038A1 1 0 1 0 6.25 4.5h1l-.001-1Zm2.5-1a1 1 0 0 0-1 .962l.001.038v1h.999a1 1 0 0 0 0-2Z" clip-rule="evenodd" />
                    <path d="M7.25 8.5H2V12a2 2 0 0 0 2 2h3.25V8.5ZM8.75 14V8.5H14V12a2 2 0 0 1-2 2H8.75Z" />
                </svg>

                Local Top 50
            </div>
        </div>
    </div>

    {{-- Profile --}}
    <div>
        <div class="text-xs text-gray-400 uppercase tracking-widest mb-4">
            Artist Profile
        </div>

        <div class="space-y-2 text-sm text-gray-300">
            <div class="flex items-center gap-2 hover:text-white cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                  <path d="M14 1.75a.75.75 0 0 0-.89-.737l-7.502 1.43a.75.75 0 0 0-.61.736v2.5c0 .018 0 .036.002.054V9.73a1 1 0 0 1-.813.983l-.58.11a1.978 1.978 0 0 0 .741 3.886l.603-.115c.9-.171 1.55-.957 1.55-1.873v-1.543l-.001-.043V6.3l6-1.143v3.146a1 1 0 0 1-.813.982l-.584.111a1.978 1.978 0 0 0 .74 3.886l.326-.062A2.252 2.252 0 0 0 14 11.007V1.75Z" />
                </svg>

                My Music
            </div>

            <div class="flex items-center gap-2 hover:text-white cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                    <path d="M12 2a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1h-1ZM6.5 6a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V6ZM2 9a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V9Z" />
                </svg>

                My Analytics
            </div>

            <div class="flex items-center gap-2 hover:text-white cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                    <path d="M3.6 1.7A.75.75 0 1 0 2.4.799a6.978 6.978 0 0 0-1.123 2.247.75.75 0 1 0 1.44.418c.187-.644.489-1.24.883-1.764ZM13.6.799a.75.75 0 1 0-1.2.9 5.48 5.48 0 0 1 .883 1.765.75.75 0 1 0 1.44-.418A6.978 6.978 0 0 0 13.6.799Z" />
                    <path fill-rule="evenodd" d="M8 1a4 4 0 0 1 4 4v2.379c0 .398.158.779.44 1.06l1.267 1.268a1 1 0 0 1 .293.707V11a1 1 0 0 1-1 1h-2a3 3 0 1 1-6 0H3a1 1 0 0 1-1-1v-.586a1 1 0 0 1 .293-.707L3.56 8.44A1.5 1.5 0 0 0 4 7.38V5a4 4 0 0 1 4-4Zm0 12.5A1.5 1.5 0 0 1 6.5 12h3A1.5 1.5 0 0 1 8 13.5Z" clip-rule="evenodd" />
                </svg>

                My Notifications
            </div>
        </div>
    </div>

    {{-- Upload Music Button (keep as is) --}}
    <div class=" group relative inline-flex items-center gap-3 px-6 py-2
                bg-gradient-to-r from-emerald-500 to-green-600 
                rounded-full text-white font-semibold 
                cursor-pointer overflow-hidden 
                transition-all duration-300 ease-out 
                hover:shadow-2xl hover:shadow-emerald-500/30">

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

</aside>


{{-- MAIN --}}
<main class="flex-1 md:ml-64 px-6 lg:px-10 pb-40 space-y-16">

        {{ $slot }}

</main>
</div>

{{-- MINI PLAYER --}}
@persist('player') 
<div
    class="fixed bottom-16 md:bottom-0 left-0 right-0 backdrop-blur bg-black/40  border-t border-gray-800 h-24 px-6 flex items-center z-50"
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

        <div class="w-1/3 flex justify-end items-center gap-3 group">

    <div class="w-1/3 flex justify-end items-center gap-3 group"
     x-data="{ previousVolume: volume }">

    <!-- Clickable Volume Icon -->
    <div 
        @click="
            if (volume > 0) {
                previousVolume = volume;
                volume = 0;
            } else {
                volume = previousVolume || 1;
            }
            updateVolume();
        "
        class="text-gray-400 hover:text-white cursor-pointer 
               transition duration-300">

        <!-- Muted -->
        <template x-if="volume == 0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 9l6 6m0-6l-6 6M11 5l-4 4H4v6h3l4 4V5z" />
            </svg>
        </template>

        <!-- Low -->
        <template x-if="volume > 0 && volume <= 0.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5l-4 4H4v6h3l4 4V5z" />
            </svg>
        </template>

        <!-- High -->
        <template x-if="volume > 0.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5l-4 4H4v6h3l4 4V5zM15 9a3 3 0 010 6m2-8a6 6 0 010 10" />
            </svg>
        </template>
    </div>

    <!-- Slider -->
    <input type="range"
        min="0"
        max="1"
        step="0.01"
        x-model.number="volume"
        @input="updateVolume()"
        class="w-32 h-1.5 appearance-none bg-gray-600 rounded-full 
               outline-none cursor-pointer
               transition-all duration-300
               hover:bg-gray-500

               [&::-webkit-slider-thumb]:appearance-none
               [&::-webkit-slider-thumb]:w-4
               [&::-webkit-slider-thumb]:h-4
               [&::-webkit-slider-thumb]:rounded-full
               [&::-webkit-slider-thumb]:bg-white
               [&::-webkit-slider-thumb]:shadow-lg
               [&::-webkit-slider-thumb]:transition
               [&::-webkit-slider-thumb]:duration-300
               [&::-webkit-slider-thumb]:hover:scale-110

               [&::-moz-range-thumb]:w-4
               [&::-moz-range-thumb]:h-4
               [&::-moz-range-thumb]:rounded-full
               [&::-moz-range-thumb]:bg-white
               [&::-moz-range-thumb]:border-none">
    </div>



    </div>

    <audio x-ref="audio" class="hidden"></audio>

    @endpersist
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

</body>
</html>
