var map;
var websocket;
var websocketSession;
var selfMarker;
var currentTeam;
var teamMarkerList = [];
var greenIcon;

function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

$(document).ready(function () {

    getCurrentTeam();

    L.Icon.Default.imagePath = '/bundles/dondrekielapp/js/leaflet/images/';

    greenIcon = L.icon({
        iconUrl: '/bundles/dondrekielapp/js/leaflet/images/marker-icon-green.png',
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

    $('#station-map').width($('#map-container').width());
    $('#station-map').height($('#map-container').height());

    if (wss_enabled == 1) {
        websocket = WS.connect('wss://' + app_hostname + ':' + wss_port);

        websocket.on("socket/connect", function (session) {
            console.log("Successfully Connected!");

            websocketSession = session;

            session.subscribe("dondrekiel/channel", function (uri, payload) {
                //$.notify("Received message: " + payload);
                console.log("Received message: ", payload);
                if (payload["position"] !== undefined && payload["position"]["team"] != undefined) {
                    console.log("Set position for team " + payload["position"]["team"]);
                    team_id = payload["position"]["team"];
                    latitude = payload["position"]["latitude"];
                    longitude = payload["position"]["longitude"];

                    if (teamMarkerList[team_id] != undefined) {
                        console.log("marker found for team " + payload["position"]["team"]);
                        var teamMarker = teamMarkerList[team_id];
                        if (currentTeam.id == team_id) {
                            console.log("Current Team: " + currentTeam.id + "(" + currentTeam.locationLat + "/" + currentTeam.locationLng + ")");
                            teamMarker.setIcon(redIcon);

                        } else {
                            console.log("Foreign team: " + currentTeam.id + "(" + currentTeam.locationLat + "/" + currentTeam.locationLng + ")");
                        }

                        var newLatLng = new L.LatLng(latitude, longitude);
                        teamMarker.setLatLng(newLatLng);
                    }

                }
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
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        //navigator.geolocation.watchPosition(successCallback, errorCallback, options);
        console.log('Geolocation is supported');

        setInterval(function () {
            console.log('Position update');
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

function successCallback(position) {
    console.log("successCallback");
    try {
        console.log("Current position is: " + position.coords.latitude + "/" + position.coords.longitude);
        if (websocketSession) {
            websocketSession.publish("dondrekiel/channel", {
                position: {
                    longitude: position.coords.longitude,
                    latitude: position.coords.latitude,
                    team: currentTeam.id
                }
            });
            if (teamMarkerList[currentTeam.id] != undefined) {

                var teamMarker = teamMarkerList[currentTeam.id];

                var newLatLng = new L.LatLng(position.coords.latitude, position.coords.longitude);
                teamMarker.setLatLng(newLatLng);
            }

        }

        if (map === undefined) {

            map = L.map('station-map').setView([position.coords.latitude, position.coords.longitude], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '',
                id: 'mapbox.streets'
            }).addTo(map);

            initMap();
        }
    }
    catch (err) {
        console.log("error: " + err.message);
    }

}

function initMap() {

    var jqxhr = $.get("/rest/station", function (stations) {
    })
        .done(function (stations) {
            for (var key in stations) {
                console.log("Station: " + stations[key].id.toString());

                stationMarker = L.marker([stations[key].location.latitude, stations[key].location.longitude], {
                    title: stations[key].id,
                    id: stations[key].id,
                    alt: stations[key].id
                });

                stationMarker.on('click', function (e) {
                    console.log(e);
                    var stationId = e.sourceTarget.options.id
                    if (stationId !== undefined) {
                        var jqxhr = $.get("/rest/station/info/" + stationId, function (info) {
                        })
                            .done(function (info) {
                                $('#modalHeader').html("Station \"" + info.name + "\"");
                                $('#modalContent').html(
                                    "<strong>Stationsnummer:</strong> " + info.identifier + "<br>" +
                                    "<strong>Veranstalter:</strong> " + info.organizer + "<br><br>" +
                                    nl2br(info.description, true)
                                );
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
                    console.log("Team: " + teams[key].team_id + "(" + teams[key].locationLat + "/" + teams[key].locationLng + ")");

                    if (currentTeam.id == teams[key].team_id) {
                        console.log("Current team should already been set!");
                    }

                    teamMarker = L.marker([teams[key].locationLat, teams[key].locationLng], {
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

                    teamMarkerList[teams[key].team_id] = teamMarker;
                }

            }
            console.log("Initialized all teams");
        })
        .fail(function () {
            console.log("Error initializing teams!");
        });


}


function getCurrentTeam() {
    console.log("getCurrentTeam");
    var jqxht = $.get("/rest/team/current", function (ctresult) {
    })
        .done(function (ctresult) {
            if (ctresult["result"] == true) {
                currentTeam = ctresult["current_team"];
                /*                if (teamMarkerList[currentTeam.id] != undefined) {
                                    console.log("Current Team: " + currentTeam.id + "(" + currentTeam.locationLat + "/" + currentTeam.locationLng + ")");

                                    var teamMarker = teamMarkerList[currentTeam.id];
                                    teamMarker.setIcon(redIcon);

                                } else {
                                    console.log("Create current team marker: " + currentTeam.id + "(" + currentTeam.locationLat + "/" + currentTeam.locationLng + ")");

                                    teamMarker = L.marker([currentTeam.locationLat, currentTeam.locationLng], {
                                        title: currentTeam.id,
                                        id: currentTeam.id,
                                        alt: currentTeam.id,
                                        icon: redIcon
                                    });
                                    teamMarker.addTo(map);

                                    teamMarkerList[currentTeam.id] = teamMarker;


                                }*/
            }
            console.log("Initialized current team");
        })
        .fail(function () {
            console.log("Error initializing teams!");
        });

}
