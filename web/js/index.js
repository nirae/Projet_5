var input = document.getElementById('adresse');
var geoloc = document.querySelector("#geoloc");
var map;
var markers = [];
var infos = [];

function initMap() {
    // Map
    var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 46.46813299215553, lng: 2.57080078125},
        scroll: false,
        zoom: 5
    });
    // Formulaire dans la map
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    // Geoloc dans la map
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(geoloc);
    // Récupere les circuits en Ajax
    $.post('/ajax',
    {status: 'load' },
    function(rep) {
        for (var i = 0; i < rep.rep.length; i++) {
            addMarker(
                {lat: parseFloat(rep.rep[i].latitude),lng: parseFloat(rep.rep[i].longitude)},
                rep.rep[i].name,
                rep.rep[i].hours,
                rep.rep[i].licence,
                rep.rep[i].phone,
                rep.rep[i].email,
                rep.rep[i].id
            );
        }
    }, "json");

    // Autocomplete
    var autocomplete = new google.maps.places.Autocomplete(input, {componentRestrictions: {country: 'fr'}});
    autocomplete.bindTo('bounds', map);
    // Si l'adresse est entrée à la main
    input.addEventListener('change', function(e) {
        ajaxGet('https://maps.googleapis.com/maps/api/geocode/json?address=' + input.value + '&key=AIzaSyC0kpgqFBKDY2mmNCRDuRGWqzUBPi9PiK4', function(rep) {
            var reponse = JSON.parse(rep);
            setMapPos(reponse.results[0].geometry.location);
        });
    });
    // Si adresse selectionnée dans les propositions
    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        setMapPos(place.geometry.location);
    });
    // Si clic sur le bouton de geoloc
    geoloc.addEventListener("click", function(e) {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                setMapPos({lat: pos.coords.latitude, lng: pos.coords.longitude});
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

    // Ajoute un marker
    function addMarker(location, name, hours, licence, phone, email, id) {
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            animation: google.maps.Animation.DROP
        });
        markers.push(marker);
        var infowindow = new google.maps.InfoWindow({
            content: createContent(name, hours, licence, phone, email, id),
        });
        marker.addListener('click', function() {
            infowindow.open(map, marker);
        });
        infos.push(infowindow);
    }

    function createContent(name, hours, licence, phone, email, id) {
        var content =
        '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<h1 id="firstHeading" class="firstHeading">'+ name +'</h1>'+
        '<div id="bodyContent">'+
        '<p>Horaires: '+ hours +'</p>'+
        '<p>Licence(s) acceptée(s): '+ licence +'</p>'+
        '<h3>Contact: </h3>'+
        '<p>Téléphone: 0'+ phone +'</p>'+
        '<p>Email: '+ email +'</p>'+
        '<p><a href="/circuit/'+ id +'">Voir la fiche du terrain <span class="fa fa-hand-o-right"></span></a></p>'+
        '</div>'+
        '</div>';
        return content;
    }

    // Fonction changement centre de la map
    function setMapPos(location) {
        window.setTimeout(function () {
            map.setZoom(10);
            map.setCenter(location);
        }, 300);
    }
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
