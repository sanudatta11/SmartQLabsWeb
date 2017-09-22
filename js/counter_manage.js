$(document).ready(function () {
    $("select ul").attr("class", "");

    $('#title_qit').addClass('animated fadeInDown');
    $('#animate_row').addClass('animated fadeInUp');
    $('#title2').addClass('animated slideInLeft');
    $('#chatButton').addClass('animated rotateInDownRight');
    $('#counterDetails').addClass('animated bounceInRight');
    $('ul.tabs').tabs({'swipeable': true});
    $("select").material_select({"stopPropagation": true});
    update_number();
});

function update_number() {
    $.ajax({
        type: "GET", 		//GET or POST
        url: '/main/api/web/counter_number.php', 		// Location of the service
        dataType: "json", 	//Expected data format from server
        processdata: true 	//True or False
    }).done(function (data) {
        console.log(data);
        if (data.status_code == 401 || data.status_code == 402) {
            swal({
                title: "You have been Logged Out!",
                text: "Your session has expired, please LogIn again.",
                timer: 2000,
                showConfirmButton: true
            });
            window.location.href = "/logout/";
        }
        else if (data.status != true) {
            console.log("Error=" + data.status_code);
            swal(
                'Error',
                "Counter Error:" + data.status_code,
                'error'
            );
        }
        else {

            $('#total_counters_text').html(data.total_counters);
            $('#counters_allocated_text').html(data.counters_allocated);
            $('#allCounters').empty();

            for (var i = 0; i < data.counters_allocated; ++i) {
                var serving = "Not";
                var email = "";
                var color = "#cebcff";
                var back = 'transparent';

                email = data.counter[i].email;
                if (data.counter[i].serving === 1) {
                    serving = "";
                    color = "#26A69A";
                    back = "#26A69A";

                }

                var add =
                    '<div class="col s4" id="c"' + (i + 1) + '>' +
                    '<div class="row">' +
                    '<div class="col s12">' +
                    '<div class="card-panel" style="background-color: ' + color + '">' +
                    '<div class="row">' +
                    '<div class="col s12 middle">' +
                    'Counter ' + (i + 1) +
                    '</div>' +
                    '<hr>' +
                    '<div class="cl s12 middle">' +
                    '<div class="row">' +
                    '<div class="col s9">' +
                    email +
                    '</div>' +
                    '<div class="col s3" style="padding-top: 3px;">' +
                    '<a class="fa fa-trash-o deleteCounter delete_btn" style="background-color: ' + back + '; color: white;" data-id="' + data.counter[i].counteruid + '" data-email="' + email + '"></a>' +
                    '&nbsp;&nbsp; <a class="fa fa-qrcode" aria-hidden="true" style="padding-bottom: 5px; color: white; background-color: ' + back + '; color: white;" href="/main/qr/?qrcode=' + data.counter[i].qrdata + '" target="_blank"></a>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

                $('#allCounters').append(add);
            }

        }

    });
}

$('#addCounter').click(function () {
    $('#counterDetails').css("display", "block");
});
//add this JS
$('#closeIt').click(function () {
    $('#counterDetails').css("display", "none");
});


$('#saveCounterDetails').click(function () {
    console.log("In");
    var email = $('#remail').val();
    var pass1 = $('#pass').val();
    var pass2 = $('#rpass').val();
    var gid = $('#group_id').val();
    console.log(gid);
    if (pass1 == pass2) {
        $.ajax({
            type: "POST", 		//GET or POST
            url: '/main/api/web/addcounter_api.php', 		// Location of the service
            data: {
                'cemail': email,
                'password': pass1,
                'cpassword': pass2,
                'groupid': gid
            },
            dataType: "json", 	//Expected data format from server
            processdata: true 	//True or False
        }).done(function (data) {
            if (data.status == true) {
                swal('Success!', 'Your Counter is successfully added', 'success');
            }
            else if (data.status_code == 320) {
                swal('Waring!', 'Your Passwords don\'t match', 'warning');
            }
            else if (data.status_code == 530) {
                swal('Waring!', 'Your email is already used in a counter', 'warning');
            }
            else if (data.status_code == 370) {
                swal('Waring!', 'Fatal Error! Contact Administrator.', 'warning');
            }
            else {
                var msg = 'Error: ' + data.status_code;
                swal('Waring!', msg, 'warning');
            }
        });

        $('#counterDetails').css("display", "none");
        update_number();
    }
    else {
        swal('Wrong Password!', 'Sorry your passwords don\'t match', 'warning');
        $('#pass').empty();
        $('#rpass').empty();
    }


});
$(document).on('click', '.delete_btn', function () {
    console.log("Clicked");
    var dataid = $(this).attr('data-id');
    var dataemail = $(this).attr('data-email');
    console.log($(this).attr('data-id'));
    console.log($(this).attr('data-email'));

    $.ajax({
        type: "POST", 		//GET or POST
        url: '/main/api/web/delete_counter.php', 		// Location of the service
        data: {
            'cemail': dataemail,
            'cid': dataid
        },
        dataType: "json", 	//Expected data format from server
        processdata: true 	//True or False
    }).done(function (data) {
        if (data.status == true) {
            swal('Success!', 'Your Counter is successfully deleted', 'success');
            update_number();
        }
        else if (data.status_code == 530) {
            swal('Waring!', 'Fatal PDO Error', 'warning');
        }
        else {
            var msg = 'Error: ' + data.status_code;
            swal('Waring!', msg, 'warning');
        }
    });
});
var myFlag = 0;
$('#doneCounter').click(function () {
    if ($('#increase').val() && $('#increase').val() !== "") {
        myFlag = 0;
        var inc = $('#increase').val();
        // $('#topRight').addClass("animated fadeOut");
        $.ajax({
            type: "GET", 		//GET or POST
            url: '/main/api/web/inc_counter.php', 		// Location of the service
            data: {
                'increase': inc
            },
            dataType: "json", 	//Expected data format from server
            processdata: true 	//True or False
        }).done(function (data) {
            if (data.status === true) {
                swal('Success!', 'Your Increase is successfully Accepted', 'success');
            }
            else if (data.status_code >= 500) {
                swal('Waring!', 'Fatal PDO Error', 'warning');
            }
            else {
                var msg = 'Error: ' + data.status_code;
                swal('Waring!', msg, 'warning');
            }
        });
        update_number();
        $('#topRight').hide();
    }

});

$('#increaseCounter').click(function () {
    if (myFlag === 0) {
        $('#topRight').css("display", "block");
        myFlag = 1;
    }
    else {
        $('#topRight').css("display", "none");
        myFlag = 0;
    }
    $('#topRight').addClass('animated fadeInUp');
});

$('.dropdown-button').dropdown({
        inDuration: 300,
        outDuration: 225,
        constrainWidth: false, // Does not change width of dropdown to that of the activator
        hover: true, // Activate on hover
        gutter: 0, // Spacing from edge
        belowOrigin: false, // Displays dropdown below the button
        alignment: 'left', // Displays dropdown with edge aligned to the left of button
        stopPropagation: false // Stops event propagation
    }
);


setInterval(update_number, 5000);