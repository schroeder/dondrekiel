var map;
var websocket;
var websocketSession;
var selfMarker;
var teamList = [];

$(document).ready(function () {

    if (wss_enabled == 1) {
        websocket = WS.connect('wss://' + app_hostname + ':' + wss_port);

        websocket.on("socket/connect", function (session) {
            console.log("Successfully Connected!");

            websocketSession = session;

            session.subscribe("dondrekiel/channel", function (uri, payload) {
                //$.notify("Received message: " + payload);
                console.log("Received message: ", payload);
            });
        });

        websocket.on('message', function incoming(data) {
            console.log('Roundtrip time: ${Date.now() - data} ms');

        });

        websocket.on("socket/disconnect", function (error) {
            console.log("Disconnected for " + error.reason + " with code " + error.code);
        })
    }

    if (navigator && navigator.geolocation) {
        options = {
            enableHighAccuracy: false,
            timeout: 5000,
            maximumAge: 0
        };
        //navigator.geolocation.watchPosition(successCallback, errorCallback, options);
        console.log('Geolocation is supported');
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);

        setInterval(function () {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        }, 5000);
    } else {
        console.log('Geolocation is not supported');
    }

});

function errorCallback(error) {
    console.log('Error position');
    console.log('code: ' + error.code + '\n' +
        'message: ' + error.message + '\n');
}

function initMap() {
    var jqxhr = $.get("/rest/station", function (stations) {
    })
        .done(function (stations) {
            for (var key in stations) {
                console.log("Station: " + stations[key].id.toString());

                stationMarker = new google.maps.Marker({
                    id: stations[key].id,
                    label: "",
                    map: map,
                    icon: "/bundles/dondrekielapp/images/blue_marker.png",
                    position: {lat: stations[key].location.latitude, lng: stations[key].location.longitude}
                });
                google.maps.event.addListener(stationMarker, 'click', function () {

                    var jqxhr = $.get("/rest/station/info/" + this.id, function (info) {
                    })
                        .done(function (info) {
                            $('#modalHeader').html("Station " + info.id);
                            $('#modalContent').html(info.description);
                            $('#myModal').modal(options);

                        })
                        .fail(function () {
                            console.log("Error getting station info!");
                        });
                });
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
                    console.log("Team: " + teams[key].team_id);

                    //teamList = teamList.concat([teams[key].team_id = > teams[key]);

                    teamMarker = new google.maps.Marker({
                        id: teams[key].team_id,
                        label: "",
                        map: map,
                        icon: "/bundles/dondrekielapp/images/red_marker.png",
                        position: {lat: teams[key].location_lat, lng: teams[key].location_lng}
                    });
                    google.maps.event.addListener(teamMarker, 'click', function () {

                        var jqxhr = $.get("/rest/team/info/" + this.id, function (info) {
                        })
                            .done(function (info) {
                                $('#modalHeader').html("Team " + info.id);
                                $('#modalContent').html(info.description);
                                $('#myModal').modal(options);

                            })
                            .fail(function () {
                                console.log("Error getting team info!");
                            });
                    });
                }

            }
            console.log("Initialized all teams");
        })
        .fail(function () {
            console.log("Error initializing teams!");
        });
}

function successCallback(position) {

    console.log("Current position: " + position.coords.latitude + "/" + position.coords.longitude);
    if (websocketSession) {
        websocketSession.publish("dondrekiel/channel", {
            position: {
                longitude: position.coords.longitude,
                latitude: position.coords.latitude,
                team: team_id
            }
        });
    }

    if (map === undefined) {
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
            zoom: 14,
            center: {lat: position.coords.latitude, lng: position.coords.longitude},
            disableDefaultUI: true,
            mapTypeId: 'terrain',
            styles: mystyles
        });

        google.maps.event.addListener(map, 'click', function (event) {
            if (selfMarker !== undefined) {
                selfMarker.setPosition(event.latLng);
            }
        });
    }

    var location = {lat: position.coords.latitude, lng: position.coords.longitude};
    if (selfMarker === undefined) {
        selfMarker = new google.maps.Marker({
            position: location,
            icon: "/bundles/dondrekielapp/images/green_marker.png",
            map: map
        });
        google.maps.event.addListener(selfMarker, 'click', function () {
            var jqxhr = $.get("/rest/team/info/" + team_id, function (info) {
            })
                .done(function (info) {
                    $('#modalHeader').html("Team " + info.id);
                    $('#modalContent').html(info.content);
                    $('#myModal').modal(options);

                })
                .fail(function () {
                    console.log("Error getting station info!");
                });
        });

    } else {
        selfMarker.setPosition(location);
    }

}