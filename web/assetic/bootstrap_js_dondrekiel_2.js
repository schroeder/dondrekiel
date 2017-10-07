var map;
var websocket;

$(document).ready(function () {
    if (navigator && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    } else {
        console.log('Geolocation is not supported');
    }

    websocket = WS.connect('wss://app.dondrekiel.de:8081');


    websocket.on("socket/connect", function (session) {
        console.log("Successfully Connected!");
    })

    websocket.on("socket/disconnect", function (error) {
        console.log("Disconnected for " + error.reason + " with code " + error.code);
    })

});

function errorCallback() {
}

function successCallback(position) {

    var mystyles = [
        {
            featureType: "poi",
            elementType: "labels",
            stylers: [
                {visibility: "off"}
            ]
        }
    ];
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: {lat: position.coords.latitude, lng: position.coords.longitude},
        disableDefaultUI: true,
        mapTypeId: 'terrain',
        styles: mystyles
    });

    var location = {lat: position.coords.latitude, lng: position.coords.longitude};
    var marker = new google.maps.Marker({
        position: location,
        label: "Y",
        map: map
    });

    var jqxhr = $.get("/rest/station", function (stations) {
//        alert("success");
    })
        .done(function (stations) {
            for (var key in stations) {
                var pinColor = "00FFFF";
                var pinImage = new google.maps.MarkerImage("https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star|A|00FFFF|000000|FF0000",
                    new google.maps.Size(21, 34),
                    new google.maps.Point(0, 0),
                    new google.maps.Point(10, 34));
                var pinShadow = new google.maps.MarkerImage("https://www.google.com/chart?chst=d_map_pin_shadow",
                    new google.maps.Size(40, 37),
                    new google.maps.Point(0, 0),
                    new google.maps.Point(12, 35));

                var stationMarker = new google.maps.Marker({
                    icon: pinImage,
                    shadow: pinShadow,
                    label: stations[key].id.toString(),
                    map: map,
                    position: {lat: stations[key].location.latitude, lng: stations[key].location.longitude}
                });
            }

        })
        .fail(function () {
//            alert("error");
        })
        .always(function (stations) {
//            alert("finished");
        });

    $.notify("done");


}