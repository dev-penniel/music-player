<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Track;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new 
#[Layout('components.layouts.app.frontend')]
class extends Component {

    use WithFileUploads;

    #[Validate('image|max:10240')] // 1MB Max
    public $coverImage;

    #[Validate('required|file|mimes:mp3,wav,ogg,m4a|max:10540')] // 10MB Max
    public $audio;

    public $title, $artist;

    // Rename method so it doesn't conflict with the property
    public function saveAudio()
    {

        $validated = $this->validate([
            'title' => 'required',
            'artist' => 'required',
        ]);

        // Handle cover image upload if exists
        $coverImagePath = $this->coverImage->store('covers', 'public');

        // handle audio upload
        $audioPath = $this->audio->store('tracks', 'public');

        Track::create([
            'title' => $validated['title'],
            'artist' => $validated['artist'],
            'file_path' => $audioPath,
            'cover_path' => $coverImagePath,
        ]);

        $this->dispatch('song-uploaded');

        $this->reset();

    }

};
?>



<div class="w-full p-20"> {{-- removed wire:ignore --}}

    <form wire:submit.prevent="saveAudio" enctype="multipart/form-data"> {{-- added .prevent --}}
        
        <label for="title">Song Title</label>
        <input type="text" name="title" wire:model="title" >

        <label for="title">Artist Name</label>
        <input type="text" name="artist" wire:model="artist" >


        <label for="audio">Audio File</label>
        <input
            name="audio"
            type="file"
            accept="audio/*" 
            wire:model="audio"
            class="block w-full text-sm"
        >

        {{-- Cover Image --}}
                <div class="space-y-4 w-[300px]">
                    <flux:heading size="sm">Cover Image</flux:heading>
                    <div class="space-y-2">
                        <div x-data="{ isUploading: false, progress: 0 }" 
                             x-on:livewire-upload-start="isUploading = true"
                             x-on:livewire-upload-finish="isUploading = false"
                             x-on:livewire-upload-error="isUploading = false"
                             x-on:livewire-upload-progress="progress = $event.detail.progress">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image (Max 2MB)</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col w-full h-32 border-2 border-dashed rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all">
                                    <div class="flex flex-col items-center justify-center pt-7">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="pt-1 text-sm text-gray-600">Click to upload cover image</p>
                                    </div>
                                    <input type="file" class="opacity-0" wire:model="coverImage" accept="image/*" />
                                </label>
                            </div>
                            <div x-show="isUploading" class="mt-2">
                                <progress max="100" x-bind:value="progress" class="w-full"></progress>
                            </div>
                             @error('coverImage') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Cover Image Preview -->
                        <div wire:loading.remove wire:target="coverImage">
                            @if ($coverImage)
                                <div class="mt-2">
                                    <span class="block text-sm font-medium text-gray-700 mb-1">Preview:</span>
                                    <img src="{{ $coverImage->temporaryUrl() }}" class="h-32 w-full object-cover rounded-md">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

        @error('audio')
            <p>{{$message}}</p>
        @enderror
            
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </div>
    </form>

    @if ($audio)
        <audio controls class="mt-4">
            <source src="{{ $audio->temporaryUrl() }}">
        </audio>
    @endif

</div>
 