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
<body>
  {{ $slot }}

  {{-- REQUIRED for Livewire --}}
  @livewireScripts

  {{-- your Flux / other scripts --}}
  @fluxScripts
</body>
</html>
