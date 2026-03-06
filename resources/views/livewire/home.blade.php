<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Models\Track;
use App\Models\Artist;
use Illuminate\Support\Facades\DB;

new
#[Layout('components.layouts.app.frontend')]
class extends Component {

    public int|null $currentTrackId = null;
    public bool $shuffle = false;
    public bool $isPlaying = false;

    public array $tracksForJs = [];
    public array $newReleases = [];
    public array $recommended = [];
    public array $trending = [];
    public array $recentlyPlayed = [];
    public array $artists = [];
    public int|null $currentIdForJs = null;

    public function mount(): void
    {
        $this->loadTracks();
    }

    protected function loadTracks(): void
    {
        // Get all published tracks for the player (limit to recent 50 for performance)
        $allTracks = Track::with(['mainArtist', 'artists'])
            ->published()
            ->where('release_date', '<=', now())
            ->orderBy('release_date', 'desc')
            ->limit(50)
            ->get();

        // Format tracks for JavaScript player
        $this->tracksForJs = $allTracks->map(fn($track) => [
            'id' => $track->id,
            'title' => $track->title,
            'artist' => $this->formatArtists($track),
            'artist_id' => $track->artist_id,
            'album' => $track->album?->title ?? 'Single',
            'file_path' => $track->file_path,
            'cover_path' => $track->cover_path,
            'duration' => $track->duration,
        ])->values()->all();

        // New Releases (latest published tracks)
        $this->newReleases = Track::with(['mainArtist', 'artists'])
            ->published()
            ->where('release_date', '<=', now())
            ->orderBy('release_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get()
            ->map(fn($track) => [
                'id' => $track->id,
                'title' => $track->title,
                'artist' => $track->mainArtist?->stage_name ?? 'Unknown Artist',
                'featured' => $track->artists->map(fn($a) => $a->stage_name)->toArray(),
                'artist_object' => $track->mainArtist,
                'featured_objects' => $track->artists,
                'cover_path' => $track->cover_path,
                'duration' => $track->duration,
                'plays' => $track->plays,
                'release_date' => $track->release_date,
            ])
            ->toArray();

        // Trending (based on plays)
        $this->trending = Track::with(['mainArtist', 'artists'])
            ->published()
            ->where('release_date', '<=', now())
            ->orderBy('plays', 'desc')
            ->orderBy('release_date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($track) => [
                'id' => $track->id,
                'title' => $track->title,
                'artist' => $track->mainArtist?->stage_name ?? 'Unknown Artist',
                'featured' => $track->artists->map(fn($a) => $a->stage_name)->toArray(),
                'artist_object' => $track->mainArtist,
                'featured_objects' => $track->artists,
                'cover_path' => $track->cover_path,
                'plays' => $track->plays,
            ])
            ->toArray();

        // Recommended (mix of recent and popular, randomized)
        $this->recommended = Track::with(['mainArtist', 'artists'])
            ->published()
            ->where('release_date', '<=', now())
            ->orderBy('plays', 'desc')
            ->orderBy('release_date', 'desc')
            ->limit(20)
            ->get()
            ->shuffle()
            ->take(10)
            ->map(fn($track) => [
                'id' => $track->id,
                'title' => $track->title,
                'artist' => $track->mainArtist?->stage_name ?? 'Unknown Artist',
                'featured' => $track->artists->map(fn($a) => $a->stage_name)->toArray(),
                'artist_object' => $track->mainArtist,
                'featured_objects' => $track->artists,
                'cover_path' => $track->cover_path,
            ])
            ->toArray();

        // Recently Played (placeholder - would need session tracking)
        $this->recentlyPlayed = Track::with(['mainArtist', 'artists'])
            ->published()
            ->where('release_date', '<=', now())
            ->inRandomOrder()
            ->limit(6)
            ->get()
            ->map(fn($track) => [
                'id' => $track->id,
                'title' => $track->title,
                'artist' => $track->mainArtist?->stage_name ?? 'Unknown Artist',
                'featured' => $track->artists->map(fn($a) => $a->stage_name)->toArray(),
                'cover_path' => $track->cover_path,
            ])
            ->toArray();

        // Published Artists
        $this->artists = Artist::whereHas('tracks', function($query) {
                $query->published()->where('release_date', '<=', now());
            })
            ->withCount(['tracks' => function($query) {
                $query->published()->where('release_date', '<=', now());
            }])
            ->orderBy('stage_name')
            ->limit(10)
            ->get()
            ->map(fn($artist) => [
                'id' => $artist->id,
                'name' => $artist->stage_name, // Using stage_name instead of name
                'slug' => $artist->slug,
                'profile_image' => $artist->profile_image, // Using profile_image for avatar
                'cover_image' => $artist->cover_image, // Using cover_image for cover
                'bio' => $artist->bio,
                'tracks_count' => $artist->tracks_count,
            ])
            ->toArray();

        // Set current track
        $first = $allTracks->first();
        $this->currentTrackId = $first ? $first->id : null;
        $this->currentIdForJs = $this->currentTrackId;
    }

    protected function formatArtists($track): string
    {
        $artists = collect();
        
        if ($track->mainArtist) {
            $artists->push($track->mainArtist->name);
        }
        
        foreach ($track->artists as $featured) {
            $artists->push($featured->name);
        }
        
        return $artists->join(', ');
    }

    #[On('tracks-updated')]
    public function refreshTracks(): void
    {
        $this->loadTracks();
        $this->dispatch('tracksRefreshed');
    }

    public function setTrack(int $id): void
    {
        $this->currentTrackId = $id;
        $this->currentIdForJs = $id;

        // Increment plays
        Track::where('id', $id)->increment('plays');

        $this->dispatch('trackChanged', id: $id);
    }

};
?>

@php
$data = get_object_vars($this);
$newReleases = $data['newReleases'] ?? [];
$recommended = $data['recommended'] ?? [];
$trending = $data['trending'] ?? [];
$recentlyPlayed = $data['recentlyPlayed'] ?? [];
$artists = $data['artists'] ?? [];
$tracksForJs = $data['tracksForJs'] ?? [];
$currentId = $data['currentTrackId'] ?? null;
@endphp

<script type="application/json" id="sp_tracks_json">{!! json_encode($tracksForJs) !!}</script>

<div x-data="{ 
    hoveredArtist: null,
    isHovering: false
}">

{{-- NEW RELEASES --}}
<section>
    <div class="flex items-center justify-between my-8"> 
        <h3 class="text-lg font-semibold">New Releases</h3> 
        <a href="#" class="text-xs text-gray-400">See all</a> 
    </div> 

    <div class="flex flex-wrap gap-6 overflow-x-auto pb-4">
        @foreach ($newReleases as $track)
            <div
                class="bg-gray-900 rounded-xl p-4 hover:bg-gray-800 transition-all duration-300 group cursor-pointer relative"
                x-data="{ showMarquee: false }"
                @mouseenter="showMarquee = true"
                @mouseleave="showMarquee = false">

                <div class="relative w-40 h-40">
                    <img
                        src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}"
                        class="w-40 h-40 object-cover rounded-xl shadow-lg group-hover:scale-105 transition duration-500"
                        loading="lazy">

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

                <div class="mt-4 w-40">
                    <div class="text-sm font-semibold truncate">{{ $track['title'] }}</div>
                    
                    {{-- Artist with marquee on hover --}}
                    <div class="text-xs mt-1 text-gray-400 relative overflow-hidden">
                        <div class="truncate" x-show="!showMarquee">{{ $track['artist'] }}</div>
                        <div x-show="showMarquee" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="whitespace-nowrap animate-marquee">
                            {{ $track['artist'] }}
                            @if(!empty($track['featured']))
                                <span class="text-gray-500"> ft. {{ implode(', ', $track['featured']) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- TRENDING --}}
<section>
    <div class="flex items-center justify-between my-8"> 
        <h3 class="text-lg font-semibold">Trending</h3> 
        <a href="#" class="text-xs text-gray-400">See all</a> 
    </div> 

    <div class="flex flex-wrap gap-6 overflow-x-auto pb-4">
        @foreach ($trending as $track)
            <div
                class="bg-gray-900 rounded-xl p-4 hover:bg-gray-800 transition-all duration-300 group cursor-pointer relative"
                x-data="{ showMarquee: false }"
                @mouseenter="showMarquee = true"
                @mouseleave="showMarquee = false">

                <div class="relative w-40 h-40">
                    <img
                        src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}"
                        class="w-40 h-40 object-cover rounded-xl shadow-lg group-hover:scale-105 transition duration-500"
                        loading="lazy">

                    {{-- Floating Play Button --}}
                    <div class="absolute bottom-3 right-3 opacity-0 
                                group-hover:opacity-100 
                                group-hover:translate-y-0 
                                translate-y-4 transition-all duration-300">

                        <div
                            wire:click.stop="setTrack({{ $track['id'] }})"
                            class="w-11 h-11 rounded-full flex items-center 
                                   justify-center shadow-xl transition-all duration-300 cursor-pointer
                                   bg-green-500 hover:bg-green-400 hover:scale-110">

                            <svg xmlns="http://www.w3.org/2000/svg" 
                                 viewBox="0 0 16 16" 
                                 fill="currentColor" 
                                 class="w-4 h-4 text-black">
                                <path d="M3 3.732a1.5 1.5 0 0 1 2.305-1.265l6.706 4.267a1.5 1.5 0 0 1 0 2.531l-6.706 4.268A1.5 1.5 0 0 1 3 12.267V3.732Z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Play count badge --}}
                    <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm px-2 py-1 rounded-full text-xs text-white">
                        {{ number_format($track['plays']) }} plays
                    </div>
                </div>

                <div class="mt-4 w-40">
                    <div class="text-sm font-semibold truncate">{{ $track['title'] }}</div>
                    
                    {{-- Artist with marquee on hover --}}
                    <div class="text-xs mt-1 text-gray-400 relative overflow-hidden">
                        <div class="truncate" x-show="!showMarquee">{{ $track['artist'] }}</div>
                        <div x-show="showMarquee" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="whitespace-nowrap animate-marquee">
                            {{ $track['artist'] }}
                            @if(!empty($track['featured']))
                                <span class="text-gray-500"> ft. {{ implode(', ', $track['featured']) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- RECENTLY PLAYED --}}
@if(!empty($recentlyPlayed))
<section>
    <div class="flex items-center justify-between my-8"> 
        <h3 class="text-lg font-semibold">Recently Played</h3> 
        <a href="#" class="text-xs text-gray-400">See all</a> 
    </div> 

    <div class="flex flex-wrap gap-6 overflow-x-auto pb-4">
        @foreach ($recentlyPlayed as $track)
            <div
                class="bg-gray-900 rounded-xl p-4 hover:bg-gray-800 transition-all duration-300 group cursor-pointer relative"
                x-data="{ showMarquee: false }"
                @mouseenter="showMarquee = true"
                @mouseleave="showMarquee = false">

                <div class="relative w-40 h-40">
                    <img
                        src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}"
                        class="w-40 h-40 object-cover rounded-xl shadow-lg group-hover:scale-105 transition duration-500"
                        loading="lazy">

                    {{-- Floating Play Button --}}
                    <div class="absolute bottom-3 right-3 opacity-0 
                                group-hover:opacity-100 
                                group-hover:translate-y-0 
                                translate-y-4 transition-all duration-300">

                        <div
                            wire:click.stop="setTrack({{ $track['id'] }})"
                            class="w-11 h-11 rounded-full flex items-center 
                                   justify-center shadow-xl transition-all duration-300 cursor-pointer
                                   bg-green-500 hover:bg-green-400 hover:scale-110">

                            <svg xmlns="http://www.w3.org/2000/svg" 
                                 viewBox="0 0 16 16" 
                                 fill="currentColor" 
                                 class="w-4 h-4 text-black">
                                <path d="M3 3.732a1.5 1.5 0 0 1 2.305-1.265l6.706 4.267a1.5 1.5 0 0 1 0 2.531l-6.706 4.268A1.5 1.5 0 0 1 3 12.267V3.732Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="mt-4 w-40">
                    <div class="text-sm font-semibold truncate">{{ $track['title'] }}</div>
                    
                    {{-- Artist with marquee on hover --}}
                    <div class="text-xs mt-1 text-gray-400 relative overflow-hidden">
                        <div class="truncate" x-show="!showMarquee">{{ $track['artist'] }}</div>
                        <div x-show="showMarquee" 
                             class="whitespace-nowrap animate-marquee">
                            {{ $track['artist'] }}
                            @if(!empty($track['featured']))
                                <span class="text-gray-500"> ft. {{ implode(', ', $track['featured']) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- RECOMMENDED --}}
<section>
    <div class="flex items-center justify-between my-8"> 
        <h3 class="text-lg font-semibold">Recommended for you</h3> 
        <a href="#" class="text-xs text-gray-400">See all</a> 
    </div> 

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach ($recommended as $track)
            <div wire:click="setTrack({{ $track['id'] }})"
                class="bg-gray-900 rounded-xl p-4 hover:bg-gray-800 transition duration-300 group cursor-pointer relative"
                x-data="{ showMarquee: false }"
                @mouseenter="showMarquee = true"
                @mouseleave="showMarquee = false">

                <div class="relative">
                    <img
                        src="{{ $track['cover_path'] ? asset('storage/'.$track['cover_path']) : asset('images/default-cover.jpg') }}"
                        class="w-full object-cover rounded-xl shadow-lg group-hover:scale-105 transition duration-500"
                        loading="lazy">

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
                    
                    {{-- Artist with marquee on hover --}}
                    <div class="text-xs mt-1 text-gray-400 relative overflow-hidden">
                        <div class="truncate" x-show="!showMarquee">{{ $track['artist'] }}</div>
                        <div x-show="showMarquee" 
                             class="whitespace-nowrap animate-marquee">
                            {{ $track['artist'] }}
                            @if(!empty($track['featured']))
                                <span class="text-gray-500"> ft. {{ implode(', ', $track['featured']) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- Artists --}} 
<section> 
    <div class="flex items-center justify-between my-8"> 
        <h3 class="text-lg font-semibold">Artists</h3> 
        <a href="#" class="text-xs text-gray-400">See all</a> 
    </div> 
    
    <div class="flex gap-4 overflow-x-auto pb-2">
        @foreach ($artists as $artist)
            <div class="min-w-[10rem] w-40 rounded-lg p-3 hover:bg-gray-800 cursor-pointer"
                 x-data="{ showMarquee: false }"
                 @mouseenter="showMarquee = true"
                 @mouseleave="showMarquee = false">
                
                <div class="w-full h-34 rounded-full overflow-hidden mb-3"> 
                    {{-- FIXED: Using profile_image for artist avatar --}}
                    <img src="{{ $artist['profile_image'] ? asset('storage/'.$artist['profile_image']) : asset('images/default-artist.jpg') }}" 
                         alt="{{ $artist['name'] }}" 
                         class="w-full h-full object-cover"
                         loading="lazy"> 
                </div>
                
                {{-- Artist name with marquee on hover --}}
                <div class="relative overflow-hidden">
                    <div class="text-sm text-center font-medium truncate" x-show="!showMarquee">{{ $artist['name'] }}</div>
                    <div x-show="showMarquee" 
                         class="text-sm text-center font-medium whitespace-nowrap animate-marquee">
                        {{ $artist['name'] }}
                    </div>
                </div>
                
                <div class="text-xs text-center text-gray-400 truncate">
                    {{ $artist['tracks_count'] }} {{ Str::plural('track', $artist['tracks_count']) }}
                </div> 
            </div> 
        @endforeach 
    </div> 
</section>

{{-- Add marquee animation --}}
<style>
    @keyframes marquee {
        0% { transform: translateX(0); }
        100% { transform: translateX(-100%); }
    }
    
    .animate-marquee {
        animation: marquee 8s linear infinite;
        padding-left: 100%;
        display: inline-block;
    }
    
    /* Smooth transitions */
    .group-hover\:scale-105 {
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

</div>