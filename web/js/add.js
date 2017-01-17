var input = document.getElementById('nico_appbundle_circuit_address');
var latitudeElem = document.getElementById('nico_appbundle_circuit_latitude');
var longitudeElem = document.getElementById('nico_appbundle_circuit_longitude');
var geoloc = document.querySelector("#geoloc");

function initMap() {
    // Map
    var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 46.46813299215553, lng: 2.57080078125},
        scrollwheel: false,
        zoom: 5
    });
    // Formulaire dans la map
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    // Geoloc dans la map
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(geoloc);
    // Autocomplete
    var autocomplete = new google.maps.places.Autocomplete(input, {componentRestrictions: {country: 'fr'}});
    autocomplete.bindTo('bounds', map);
    // Création marker
    var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
        draggable: true,
        map: map,
        animation: google.maps.Animation.DROP,
    });

    function setPos(location) {
        marker.setPosition(location);
        window.setTimeout(function () {
            map.setCenter(location);
        }, 300);
    }

    // Si l'adresse est entrée à la main
    input.addEventListener('change', function(e) {
        ajaxGet('https://maps.googleapis.com/maps/api/geocode/json?address=' + input.value + '&key=AIzaSyC0kpgqFBKDY2mmNCRDuRGWqzUBPi9PiK4', function(rep) {
            var reponse = JSON.parse(rep);
            setPos(reponse.results[0].geometry.location);
            map.setZoom(14);
        });
    });
    // Si adresse selectionnée dans les propositions
    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        marker.setPosition(place.geometry.location);
        setPos(place.geometry.location);
        map.setZoom(14);
        latitudeElem.value = place.geometry.location.lat();
        longitudeElem.value = place.geometry.location.lng();
    });
    // Ajoute un marqueur si clic sur la map
    map.addListener('click', function(e) {
        setPos({lat: e.latLng.lat(), lng: e.latLng.lng()});
        latitudeElem.value = e.latLng.lat();
        longitudeElem.value = e.latLng.lng();
        // Remplir l'adresse
        ajaxGet('https://maps.googleapis.com/maps/api/geocode/json?latlng=' + e.latLng.lat() + ',' + e.latLng.lng() + '&language=fr&key=AIzaSyC0kpgqFBKDY2mmNCRDuRGWqzUBPi9PiK4', function(rep) {
            var reponse = JSON.parse(rep);
            input.value = reponse.results[0].formatted_address;
        });
    });
    // Si le marker est déplacé
    marker.addListener('dragend', function(e) {
        latitudeElem.value = e.latLng.lat();
        longitudeElem.value = e.latLng.lng();
        // Remplir l'adresse
        ajaxGet('https://maps.googleapis.com/maps/api/geocode/json?latlng=' + e.latLng.lat() + ',' + e.latLng.lng() + '&language=fr&key=AIzaSyC0kpgqFBKDY2mmNCRDuRGWqzUBPi9PiK4', function(rep) {
            var reponse = JSON.parse(rep);
            input.value = reponse.results[0].formatted_address;
        });
    });
    // Si clic sur le bouton de geoloc
    geoloc.addEventListener("click", function(e) {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                latitudeElem.value = pos.coords.latitude;
                longitudeElem.value = pos.coords.longitude;
                setPos({lat: pos.coords.latitude, lng: pos.coords.longitude});
                map.setZoom(14);
                // Remplir l'adresse
                ajaxGet('https://maps.googleapis.com/maps/api/geocode/json?latlng=' + pos.coords.latitude + ',' + pos.coords.longitude + '&language=fr&key=AIzaSyC0kpgqFBKDY2mmNCRDuRGWqzUBPi9PiK4', function(rep) {
                    var reponse = JSON.parse(rep);
                    input.value = reponse.results[0].formatted_address;
                });
            });
        } else {
            document.querySelector('#error').style.display = "block";
        }
    });
}

function ajaxGet(url, callback) {
    var req = new XMLHttpRequest();
    req.open("GET", url);
    req.addEventListener("load", function () {
        if (req.status >= 200 && req.status < 400) {
            // Appelle la fonction callback en lui passant la réponse de la requête
            callback(req.responseText);
        } else {
            console.error(req.status + " " + req.statusText + " " + url);
        }
    });
    req.addEventListener("error", function () {
        console.error("Erreur réseau avec l'URL " + url);
    });
    req.send(null);
}
