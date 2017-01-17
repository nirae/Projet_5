var map;
var marker;
var latitude = parseFloat(document.getElementById('latitude').innerHTML);
var longitude = parseFloat(document.getElementById('longitude').innerHTML);

console.log(latitude);

function initMap() {

    // Map
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: latitude, lng: longitude},
        zoom: 12
    });
    // Marker
    var marker = new google.maps.Marker({
        position: {lat: latitude, lng: longitude},
        map: map,
        animation: google.maps.Animation.DROP,
    });
}
