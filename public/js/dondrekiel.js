var map;
var websocket;
var websocketSession;
var selfMarker;
var currentTeam;
var teamMarkerList = [];
var greenIcon;
var blueIcon;
var redIcon;
var grayIcon;
var stationMarkerList = [];
var wss_enabled = 0;

function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

$(document).ready(function () {

    getCurrentTeam();

    installPrompt();

    L.Icon.Default.imagePath = '/js/leaflet/images/';

    greenIcon = L.icon({
        iconUrl: '/js/leaflet/images/marker-icon-green.png',
        shadowUrl: '/js/leaflet/images/marker-shadow.png',

        iconSize: [25, 41],
        shadowSize: [41, 41]
    });

    blueIcon = L.icon({
        iconUrl: '/js/leaflet/images/marker-icon.png',
        shadowUrl: '/js/leaflet/images/marker-shadow.png',

        iconSize: [25, 41],
        shadowSize: [41, 41]
    });

    grayIcon = L.icon({
        iconUrl: '/js/leaflet/images/marker-icon-gray.png',
        shadowUrl: '/js/leaflet/images/marker-shadow.png',

        iconSize: [25, 41],
        shadowSize: [41, 41]
    });

    redIcon = L.icon({
        iconUrl: '/js/leaflet/images/marker-icon-red.png',
        shadowUrl: '/js/leaflet/images/marker-shadow.png',

        iconSize: [25, 41],
        shadowSize: [41, 41]
    });

    $('#station-map').width($('#map-container').width());
    $('#station-map').height(window.innerHeight - 150);

    if (navigator && navigator.geolocation) {
        options = {
            enableHighAccuracy: false,
            timeout: 15000,
            maximumAge: 0
        };
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        //navigator.geolocation.watchPosition(successCallback, errorCallback, options);
        console.log('Geolocation is supported');

        setInterval(function () {
            console.log('Position update');
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        }, 15000);
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
        /*if (websocketSession) {
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

        }*/
        if (currentTeam) {

            var posting = $.post( "/rest/team/position", { 
                "longitude": position.coords.longitude,
                "latitude": position.coords.latitude,
                "team": currentTeam.id

            } 
            );
            var teamMarker = teamMarkerList[currentTeam.id];

            if (teamMarker) {
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

                stationIcon = blueIcon;
                if (stations[key].status == 0) {
                    stationIcon = grayIcon;
                }

                stationMarker = L.marker([stations[key].location.latitude, stations[key].location.longitude], {
                    title: "Station " + stations[key].id,
                    id: stations[key].id,
                    alt: "Station " + stations[key].id,
                    icon: stationIcon
                });

                stationMarker.on('click', function (e) {
                    console.log(e);
                    var stationId = e.sourceTarget.options.id
                    if (stationId !== undefined) {
                        var jqxhr = $.get("/rest/station/info/" + stationId, function (info) {
                        })
                            .done(function (info) {
                                $('#modalHeader').html("Station \"" + info.name + "\"");
                                var extra_content = "";
                                if (info.status == 0) {
                                    extra_content = "<h5><strong>Diese Station ist momentan nicht besetzt!</strong></h5>"
                                }
                                $('#modalContent').html(
                                    extra_content +
                                    "<strong>Stationsnummer:</strong> " + info.identifier + "<br>" +
                                    "<strong>Veranstalter:</strong> " + info.organizer + "<br><br>" +
                                    nl2br(info.description, true)
                                );
                                $('#myModal').modal('show');

                            })
                            .fail(function () {
                                console.log("Error getting station info!");
                            });
                    }
                });
                stationMarker.addTo(map);

                stationMarkerList[stations[key].id] = stationMarker;
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
                    console.log("Team: " + teams[key].id + "(" + teams[key].locationLat + "/" + teams[key].locationLng + ")");

                    if (currentTeam.id == teams[key].id) {
                        if (currentTeam.team == true) {

                            if (teamMarkerList[currentTeam.id] != undefined) {
                                console.log("Current Team: " + currentTeam.id + "(" + currentTeam.locationLat + "/" + currentTeam.locationLng + ")");


                                var teamMarker = teamMarkerList[currentTeam.id];
                                teamMarker.setIcon(redIcon);

                            } else {
                                console.log("Create current team marker: " + currentTeam.id + "(" + currentTeam.locationLat + "/" + currentTeam.locationLng + ")");

                                teamMarker = L.marker([currentTeam.locationLat, currentTeam.locationLng], {
                                    title: "Hier seid ihr!",
                                    id: currentTeam.id,
                                    alt: "Hier seid ihr!",
                                    icon: redIcon
                                });
                                if (map != undefined) {
                                    teamMarker.addTo(map);
                                } else {
                                    console.log("Error: map undefined!");
                                }

                                teamMarkerList[currentTeam.id] = teamMarker;


                            }
                        }
                    } else {

                        teamMarker = L.marker([teams[key].locationLat, teams[key].locationLng], {
                            title: "Hier ist ein Team",
                            id: teams[key].id,
                            alt: "Hier ist ein Team",
                            icon: greenIcon
                        });

                        teamMarker.addTo(map);
                        teamMarkerList[teams[key].id] = teamMarker;
                    }
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

    var jqxht = $.get("/rest/team/current", function (result) {
    })
        .done(function (result) {
            if (result["result"] == true) {
                currentTeam = result["current_team"];
                console.log("Initialized current team");
            } else {
                $('#info-text').html("You are not logged in!")
            }
        })
        .fail(function () {
            console.log("Error initializing current team!");
        });
};


function installPrompt() {
    $( "#info-text" ).append( "<pre>" + "installPrompt" + "</pre>");

    const pwaInstall = document.getElementById('a2hs');
    const choiseResult = pwaInstall.prompt();
    
    const relatedApps = pwaInstall.getInstalledRelatedApps();
    let isInstallSupportedPropertyValue = pwaInstall.isInstallSupported;
    
    let isInstallAvailablePropertyValue = pwaInstall.isInstallAvailable;
    
    let platformsPropertyValue = pwaInstall.platforms;
    
    let choiceResultPropertyValue = pwaInstall.choiceResult;
    
    let isGetInstalledRelatedAppsSupportedPropertyValue = pwaInstall.isGetInstalledRelatedAppsSupported;
    
    let relatedAppsPropertyValue = pwaInstall.relatedApps;
    let isInstallSupportedAttributeValue = pwaInstall.hasAttribute('is-install-supported');
    
    let isInstallAvailableAttributeValue = pwaInstall.hasAttribute('is-install-available');
    
    let isGetInstalledRelatedAppsSupportedAttributeValue = pwaInstall.hasAttribute('is-get-installed-related-apps-supported');
    pwaInstall.addEventListener('pwa-install-available', handlePWAInstallAvailableEvent);
    
    pwaInstall.addEventListener('pwa-install-installing', handlePWAInstallInstallingEvent);
    
    pwaInstall.addEventListener('pwa-install-installed', handlePWAInstallInstalledEvent);
    
    pwaInstall.addEventListener('pwa-install-error', handlePWAInstallErrorEvent);
};

const handlePWAInstallAvailableEvent = (event) => {
    // Use event.detail.value and/or run any code
    $( "#info-text" ).append( "<pre>" + "handlePWAInstallAvailableEvent" + "</pre>");
}
  
const handlePWAInstallInstallingEvent = (event) => {
    // Use event.detail.value and/or run any code
    $( "#info-text" ).append( "<pre>" + "handlePWAInstallInstallingEvent" + "</pre>");
}
  
const handlePWAInstallInstalledEvent = (event) => {
    // Use event.detail.value and/or run any code
    $( "#info-text" ).append( "<pre>" + "handlePWAInstallInstalledEvent" + "</pre>");
}
  
const handlePWAInstallErrorEvent = (event) => {
    // Use event.detail.message.error, event.detail.value and/or run any code
    $( "#info-text" ).append( "<pre>" + "handlePWAInstallErrorEvent" + "</pre>");
}


