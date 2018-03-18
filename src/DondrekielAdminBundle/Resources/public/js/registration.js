var gCtx = null;
var gCanvas = null;
var c = 0;
var stype = 0;
var gUM = false;
var webkit = false;
var moz = false;
var v = null;

var vidhtml = '<video id="v" autoplay></video>';

function initCanvas(w, h) {
    gCanvas = document.getElementById("qr-canvas");
    gCanvas.style.width = w + "px";
    gCanvas.style.height = h + "px";
    gCanvas.width = w;
    gCanvas.height = h;
    gCtx = gCanvas.getContext("2d");
    gCtx.clearRect(0, 0, w, h);
}

function captureToCanvas() {
    if (stype != 1)
        return;
    if (gUM) {
        try {
            gCtx.drawImage(v, 0, 0);
            try {
                qrcode.decode();
            }
            catch (e) {
                console.log(e);
                setTimeout(captureToCanvas, 500);
            }
        }
        catch (e) {
            console.log(e);
            setTimeout(captureToCanvas, 500);
        }
    }
}

function codeReadOnRegisterPage(a) {
    if (isValidMD5(a)) {
        console.log("[OnRegistration] Found code: " + a);
        url = window.location.href;

        var jqxhr = $.ajax({
            type: "POST",
            url: '/register/addPasscode',
            data: {qr: a},
            success: function (data) {
                console.log("success");
                if (data.success_message != undefined) {
                    message = data.success_message;
                }
                if (data.team_id != undefined) {
                    $("team_id").text = data.team_id;
                }

                if (data.member_id != undefined) {
                    $("#member_list").append('<li>' + data.member_id + '</li>');
                }

                if (data.enable_finish != undefined && data.enable_finish != false) {
                    $("#finish").removeClass('hidden');
                }
                showRegistrationSuccessMessage(message);
            }
        })
            .done(function () {
                console.log("second success");
            })
            .fail(function (data) {
                console.log("second success");
                message = "Irgendwas hat nicht geklappt!";
                if (data.error_message !== undefined) {
                    message = data.responseJSON.error_message;
                }
                showRegistrationErrorMessage(message);
            })
            .always(function () {
                console.log("finished");
                setTimeout(setDefaultText, 3000);
            });

    }
    else {
        showLoginErrorMessage("DAS HAT LEIDER NICHT GEKLAPPT!");
        setTimeout(setDefaultText, 3000);
    }
}

function showRegistrationErrorMessage(message) {
    $("#login_result").text(message);
    $("#login_result").addClass("alert");
    $("#login_result").addClass("alert-danger");

}

function showRegistrationSuccessMessage(message) {
    $("#login_result").text(message);
    $("#login_result").addClass("alert");
    $("#login_result").addClass("alert-success");

}

function setDefaultText() {
    $("#login_result").text("Jetzt Teamkarte und Teilnehmerkarten Scannen!");
    $("#login_result").removeClass("alert");
    $("#login_result").removeClass("alert-danger");
    setTimeout(captureToCanvas, 500);
}

function isValidMD5(s) {
    /* Should check for valid type:md5
     s.toString().match("(.*):[a-fA-F0-9]{32}")
     * */
    return true;
}

function isCanvasSupported() {
    var elem = document.createElement('canvas');
    return !!(elem.getContext && elem.getContext('2d'));
}
function success(stream) {
    if (webkit)
        v.src = window.URL.createObjectURL(stream);
    else if (moz) {
        v.mozSrcObject = stream;
        v.play();
    }
    else
        v.src = stream;
    gUM = true;
    setTimeout(captureToCanvas, 500);
}

function error(error) {
    gUM = false;
    return;
}

function setwebcam() {
    var options = true;
    if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
        try {
            navigator.mediaDevices.enumerateDevices()
                .then(function (devices) {
                    devices.forEach(function (device) {
                        if (device.kind === 'videoinput') {
                            if (device.label.toLowerCase().search("back") > -1)
                                options = {'deviceId': {'exact': device.deviceId}, 'facingMode': 'environment'};
                        }
                        console.log(device.kind + ": " + device.label + " id = " + device.deviceId);
                    });
                    setwebcam2(options);
                });
        }
        catch (e) {
            console.log(e);
        }
    }
    else {
        console.log("no navigator.mediaDevices.enumerateDevices");
        setwebcam2(options);
    }

}

function setwebcam2(options) {
    console.log(options);
    if (stype == 1) {
        setTimeout(captureToCanvas, 500);
        return;
    }
    var n = navigator;
    document.getElementById("outdiv").innerHTML = vidhtml;
    v = document.getElementById("v");


    if (n.getUserMedia) {
        webkit = true;
        n.getUserMedia({video: options, audio: false}, success, error);
    }
    else if (n.webkitGetUserMedia) {
        webkit = true;
        n.webkitGetUserMedia({video: options, audio: false}, success, error);
    }
    else if (n.mozGetUserMedia) {
        moz = true;
        n.mozGetUserMedia({video: options, audio: false}, success, error);
    }

    stype = 1;
    setTimeout(captureToCanvas, 500);
}
