$(".game-subject").click(function () {

    var gameSubjectList = $(this).attr('id').split('_');
    var gameSubject = gameSubjectList[1];

    console.log("[OnPlay] Subject selected: " + gameSubject);
    window.location = "/play/selectgame?subject=" + gameSubject;

    /*
     var jqxhr = $.ajax({
     type: "POST",
     url: '/play/selectGame',
     data: {gameSubject: gameSubject},
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
     })
     .always(function () {
     console.log("finished");
     //setTimeout(setDefaultText, 3000);
     });
     */
});


function codeReadOnLevelJumpPage(a) {
    if (isValidMD5(a)) {
        console.log("[OnLevelJump] Found code: " + a);
        window.location = "/play/jumplevel?qr=" + a;
    }
    else {
        showLoginErrorMessage("DAS HAT LEIDER NICHT GEKLAPPT!");
        setTimeout(setDefaultText, 3000);
    }
}