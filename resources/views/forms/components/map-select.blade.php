<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin="">
    </script>


    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }"
         x-init="
            let map = L.map('map').setView([0, 0], 2);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        let lat = position.coords.latitude;
                        let lng = position.coords.longitude;

                        map.setView([lat, lng], 13);



                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;

                        Livewire.dispatch('updateCoordinates', {
                            lat: lat,
                            lng: lng
                        });
                    },
                    function () {
                        alert('Location access denied. Please select your location on the map.');
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
            }

            let marker;

            map.on('click', function (e) {
                let lat = e.latlng.lat;
                let lng = e.latlng.lng;

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                state = [
                    lat,
                    lng
                ];

                Livewire.dispatch('updateCoordinates', {
                    lat: lat,
                    lng: lng
                });

                if (marker) {
                    map.removeLayer(marker);
                }

                marker = L.marker([lat, lng], {
                    title: '',
                    riseOnHover: true,
                }).addTo(map);
            });
        ">
        <input  id="longitude" type="hidden">
        <input  id="latitude" type="hidden">

        <div class="mb-3" wire:ignore>
            <div id="map" style="width: 100%; height: 400px;"></div>
        </div>
    </div>
</x-dynamic-component>
