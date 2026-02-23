<script>
    var map;
    $(document).ready(function() {

        // Initialize map when tab is shown
        $('a[href="#map_tab"]').on('shown.bs.tab', function(e) {
            if (!map) {
                initMap();
            } else {
                google.maps.event.trigger(map, 'resize');
            }
        });

        // Initialize immediately if map tab is active
        if ($('#map_tab').hasClass('active')) {
            initMap();
        }

    });

    function initMap() {
        var defaultLocation = {lat: 11.562108, lng: 104.888535};
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            center: defaultLocation,
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true
        });
        
        @if(isset($lat) && isset($long))
            var contactLocation = {
                lat: {{ $lat }},
                lng: {{ $long }}
            };
            var marker = new google.maps.Marker({
                position: contactLocation,
                map: map,
                title: "{{ $contact->name }}",
                icon: {url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"}
            });
            var infowindow = new google.maps.InfoWindow({
                content: '<div><strong>{{ $contact->name }}</strong><br>' +
                        '@if(isset($contact->address)){{ $contact->address }}@endif</div>'
            });
            marker.addListener('click', function() {
                infowindow.open(map, marker);
            });
            map.setCenter(contactLocation);
        @else
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        var userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        var marker = new google.maps.Marker({
                            position: userLocation,
                            map: map,
                            title: 'Your Location',
                            icon: {url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"}
                        });
                        map.setCenter(userLocation);
                    }, 
                    function(error) {
                        new google.maps.Marker({
                            position: defaultLocation,
                            map: map,
                            title: 'Default Location'
                        });
                    }
                );
            } else {
                new google.maps.Marker({
                    position: defaultLocation,
                    map: map,
                    title: 'Default Location'
                });
            }
        @endif
    }
</script>