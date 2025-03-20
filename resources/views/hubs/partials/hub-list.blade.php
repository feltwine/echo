@foreach($hubs as $hub)
    <div class="w-3/5 bg-gray-300 p-4 shadow-md mb-4 cursor-pointer"
         onclick="window.location='{{ route('hubs.show', ['slug' => $hub->slug]) }}'">
        <h2>{{$hub->name}}</h2>
        <p>{{$hub->description}}</p>
        <p>
            {{$hub->followers_count}} followers,
            @php
                $age = $hub->created_at->diff(now());
            @endphp

            @if($age->y > 0)Community for {{ $age->y }} years
            @elseif($age->m > 0)Community for {{ $age->m }} months
            @elseif($age->d > 0)Community for {{ $age->d }} days
            @else Started today @endif
        </p>
    </div>
@endforeach
