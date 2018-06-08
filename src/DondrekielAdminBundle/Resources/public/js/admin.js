var map;

$(document).ready(function () {

    L.Icon.Default.imagePath = '/bundles/dondrekielapp/js/leaflet/images/';

    greenIcon = L.icon({
        iconUrl: '/bundles/dondrekielapp/js/leaflet/images/marker-icon-green.png',
        shadowUrl: '/bundles/dondrekielapp/js/leaflet/images/marker-shadow.png',

        iconSize: [25, 41],
        shadowSize: [41, 41]
    });

    blueIcon = L.icon({
        iconUrl: '/bundles/dondrekielapp/js/leaflet/images/marker-icon.png',
        shadowUrl: '/bundles/dondrekielapp/js/leaflet/images/marker-shadow.png',

        iconSize: [25, 41],
        shadowSize: [41, 41]
    });

    redIcon = L.icon({
        iconUrl: '/bundles/dondrekielapp/js/leaflet/images/marker-icon-red.png',
        shadowUrl: '/bundles/dondrekielapp/js/leaflet/images/marker-shadow.png',

        iconSize: [25, 41],
        shadowSize: [41, 41]
    });

    grayIcon = L.icon({
        iconUrl: '/bundles/dondrekielapp/js/leaflet/images/marker-icon-gray.png',
        shadowUrl: '/bundles/dondrekielapp/js/leaflet/images/marker-shadow.png',

        iconSize: [25, 41],
        shadowSize: [41, 41]
    });


    $('#station-map').width($('#map-container').width());
    $('#station-map').height('400px');
    if (map === undefined) {

        map = L.map('station-map').setView([51.8350587, 7.819898], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '',
            id: 'mapbox.streets'
        }).addTo(map);

        initMap();
    }

});

function initMap() {
    var options = {
        enableHighAccuracy: false,
        timeout: 5000,
        maximumAge: 0
    };

    var jqxhr = $.get("/rest/station", function (stations) {
    })
        .done(function (stations) {
            for (var key in stations) {
                console.log("Station: " + stations[key].id.toString());

                if (stations[key].status == 0) {
                    var stationIcon = grayIcon;
                } else {
                    var stationIcon = blueIcon;
                }

                stationMarker = L.marker([stations[key].location.latitude, stations[key].location.longitude], {
                    title: "Station " + stations[key].id + ": " + stations[key].name,
                    id: stations[key].id,
                    alt: stations[key].description,
                    icon: stationIcon
                });

                stationMarker.on('click', function (e) {
                    console.log(e);
                    var stationId = e.sourceTarget.options.id
                    if (stationId !== undefined) {
                        var jqxhr = $.get("/rest/station/info/" + stationId, function (info) {
                        })
                            .done(function (info) {
                                $('#modalHeader').html("Station " + info.id);
                                $('#modalContent').html(info.description);
                                $('#myModal').modal(options);

                            })
                            .fail(function () {
                                console.log("Error getting station info!");
                            });
                    }
                });
                stationMarker.addTo(map);
            }
            console.log("Initialized all stations");

        })
        .fail(function () {
            console.log("Error initializing stations!");
        });


    var jqxht = $.get("/rest/team", function (result) {
    })
        .done(function (result) {
            if (result["result"] == true) {
                var teams = result["teams"];
                for (var key in teams) {
                    console.log("Team: " + teams[key].team_id + "(" + teams[key].location_lat + "/" + teams[key].location_lng + ")");

                    teamMarker = L.marker([teams[key].location_lat, teams[key].location_lng], {
                        title: teams[key].team_id,
                        id: teams[key].team_id,
                        alt: teams[key].team_id,
                        icon: greenIcon
                    });

                    teamMarker.on('click', function (e) {
                        console.log(e);
                        var teamId = e.sourceTarget.options.id
                        if (teamId !== undefined) {

                            var jqxhr = $.get("/rest/team/info/" + teamId, function (info) {
                            })
                                .done(function (info) {
                                    $('#modalHeader').html("Team " + info.id);
                                    $('#modalContent').html(info.description);
                                    $('#myModal').modal(options);

                                })
                                .fail(function () {
                                    console.log("Error getting team info!");
                                });
                        }
                    });
                    teamMarker.addTo(map);


                }

            }
            console.log("Initialized all teams");
        })
        .fail(function () {
            console.log("Error initializing teams!");
        });
}

