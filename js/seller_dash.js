var previous;
var previous_serial = -1;
var authorized = false;
var presentshift = null;
var added_503 = false;

$(document).ready(function () {
    $('#title_qit').addClass('animated fadeInDown');
    $('#animate_row').addClass('animated fadeInUp');
    $('#title2').addClass('animated slideInLeft');
    $('#chatButton').addClass('animated rotateInDownRight');


});

$('#dashboardMain').ready(function () {
    console.log("Fuck");
    $.ajax({
        type: "GET", 		//GET or POST
        url: '/main/api/web/serve_stat.php', 		// Location of the service
        dataType: "json", 	//Expected data format from server
        processdata: true 	//True or False
    }).done(function (data) {
        console.log(data);
        if (data.status != true) {
            console.log("Error=" + data.status_code);
            swal("Oops!", "Serving error code:" + data.status_code, "error");
            doit = false;
        }
        else {
            if (data.serving == 0) {
                console.log("entered 1");
                document.getElementById('serve2').style.display = 'block';
                document.getElementById('serve').style.display = 'none';
                $("#dashboardMain *").attr("disabled", true);
                $('#dashboardMain').addClass("blackout");
                flag = 1;
            }
        }

    });
});

function toggle() {
    console.log("CLicked");

    var x = document.getElementById('hide');
    var y = document.getElementById('chatButton');
    //var z = document.getElementById('hideMe');
    //console.log(x.style.display);
    if (x.style.display == 'none') {
        x.style.display = 'block';
        y.style.backgroundColor = '#575c82';
        // z.style.color:
    } else {
        x.style.display = 'none';
    }
}
$(".orderWaiting").click(function () {
    $('#counterSend').css('display', 'block');
    $('#counterSend').addClass('animated bounce');


});


var flag = 0;
function serveToggle() {
    var state, doit = true;
    if (flag == 0)
        state = 2;
    else
        state = 1;
    $.ajax({
        type: "GET", 		//GET or POST
        url: '/main/api/web/serving.php', 		// Location of the service
        data: {state: state}, 		//Data sent to server
        dataType: "json", 	//Expected data format from server
        processdata: true, 	//True or False
    }).done(function (data) {
        console.log(data);
        if (data.status != true) {
            console.log("Error=" + data.status_code);
            window.alert("Serving error code:" + data.status_code);
            doit = false;
        }
        else {
            if (flag == 0) {
                console.log("entered 1");
                document.getElementById('serve2').style.display = 'block';
                document.getElementById('serve').style.display = 'none';
                $("#dashboardMain *").attr("disabled", true);
                $('#dashboardMain').addClass("blackout");
                flag = 1;
            }
            else if (flag == 1) {
                document.getElementById('serve2').style.display = 'none';
                document.getElementById('serve').style.display = 'block';
                $('#dashboardMain').removeClass("blackout");
                $("#dashboardMain *").attr("disabled", false);

                flag = 0;
            }
        }

    });

}


function updatequeue() {

    $("#reload").load(location.href + " #reload");
    if (flag == 0) {
        $.ajax({
            type: "GET", 		//GET or POST
            url: '/main/api/web/getqueue.php', 		// Location of the service
            dataType: "json", 	//Expected data format from server
            processdata: true, 	//True or False
        }).done(function (data) {
            console.log(data);
            if (data.status == true) {
                added_503 = false;
                $('#_serving').empty();
                if (previous != JSON.stringify(data)) {
                    var serial = 1;
                    previous = JSON.stringify(data);
                    console.log("Now");
                    console.log(previous - data);
                    previous_serial = data[0].serial;
                    $('#queue_repeat').empty();
                    if (previous_serial != data[0].serial) {
                        authorized = false;
                        $('#otp_but').attr("disabled", false);
                    }
                    $('#otp_header').text(data[0].serial);

                    for (var i = 0; i < data.size; ++i) {
                        var _add =
                            '<tr><td>' + serial + '</td>' +
                            '<td>' + data[i].serial + '</td>' +
                            '<td>' + data[i].customer_name + '</td>' +
                            '<td>' + data[i].queueuid + '</td>' +
                            '<td>' +
                            '<a class="fa fa-check orderDone" aria-hidden="true" data-serial="' + data[i].serial + '" data-queueuid="' + data[i].queueuid + '"disabled="true"></a>' +
                            '<a class="fa fa-times orderCancelled" aria-hidden="true" data-serial="' + data[i].serial + '" data-queueuid="' + data[i].queueuid + '"></a>' +
                            '<a class="fa  fa-align-justify orderWaiting" aria-hidden="true" data-serial="' + data[i].serial + '" data-queueuid="' + data[i].queueuid + '"></a>' +
                            '</td></tr>';

                        $('#queue_repeat').append(_add);
                        //_add.click(fuck);
                        serial++;

                    }
                }
            }
            if (data.status_code == 503) {
                if (!added_503) {
                    Materialize.toast('Your Queue is Empty', 4000, 'rounded');
                    $('#_serving').append('<span class="new badge red" data-badge-caption="Warning!">Counter empty</span>');
                    added_503 = true;
                }
                $('#queue_repeat').empty();
                previous = null;
                previous_serial = -1;
            }
            if (data.status_code == 401 || data.status_code == 402) {
                swal({
                    title: "You have been Logged Out!",
                    text: "Your session has expired, please LogIn again.",
                    timer: 2000,
                    showConfirmButton: true
                });
                window.location.href = "/logout/";
            }
        });
    }
}

//Done Order
$(document).on('click', '#queue_repeat tr td .orderDone', function () {
    var serialuid = $(this).attr('data-serial');
    var queueuid = $(this).attr('data-queueuid');
    console.log("Hi");
    // do something here
    if (flag == 0 && previous_serial == serialuid && authorized == true) {
        console.log("Hello");
        $.ajax({
            type: "GET", 		//GET or POST
            url: '/main/api/web/done.php', 		// Location of the service
            data: {
                'queueuid': queueuid,
                'serial': serialuid
            },
            dataType: "json", 	//Expected data format from server
            processdata: true 	//True or False
        }).done(function (data) {
            console.log(data);
            $('#queue_repeat').empty();
            previous = null;
            updatequeue();
        });
    }
});

//Late Order
$(document).on('click', '#queue_repeat tr td .orderCancelled', function () {
    var serialuid = $(this).attr('data-serial');
    var queueuid = $(this).attr('data-queueuid');

    // do something here
    if (flag == 0 && previous_serial == serialuid) {
        $.ajax({
            type: "GET", 		//GET or POST
            url: '/main/api/web/late.php', 		// Location of the service
            data: {
                'queueuid': queueuid,
                'serial': serialuid
            },
            dataType: "json", 	//Expected data format from server
            processdata: true 	//True or False
        }).done(function (data) {
            console.log(data);
            $('#queue_repeat').empty();
            previous = null;
            previous_serial = -1;
            updatequeue();
        });
    }
});


//OTP Click
$('#otp_but').click(function () {
    if ($('#otp_header').text() == previous_serial) {
        console.log("Correct");
        var serialuid = previous_serial;
        var otp = $('#getOTP').val();
        $.ajax({
            type: "GET", 		//GET or POST
            url: '/main/api/web/checkotp.php', 		// Location of the service
            data: {
                'serial': serialuid,
                'otp': otp
            },
            dataType: "json", 	//Expected data format from server
            processdata: true 	//True or False
        }).done(function (data) {
            console.log(data);
            if (data.status == true) {
                authorized = true;
                Materialize.toast('OTP Authorized!', 1000, 'rounded');
                $('#otp_but').attr("disabled", true);
            }
            else if (data.status_code == 302) {
                authorized = false;
                Materialize.toast('Wrong OTP!', 1000, 'rounded');
            }
            else {
                authorized = false;
                Materialize.toast('Error Code:' + data.status_code, 'rounded');
            }
        });
    }
    else {
        console.log("Wrong");
        swal('Oops!', 'Serial in OTP Box and OTP doesnot Match', 'error');
    }
});

$(document).on('click', '.orderWaiting', function () {
    $('#counterSend').addClass('animated shake');
    $('#counterSend').css('display', 'block');
    presentshift = this;
});


$('#counterDone').click(function () {
    if (presentshift !== null) {
        console.log("FUck it man");
        var serial = $(presentshift).attr('data-serial');
        var qid = $(presentshift).attr('data-queueuid');
        var counter = $('#counter_shift').val();
        console.log(serial, qid, counter);

        if (counter !== "" && counter !== null) {
            $.ajax({
                type: "GET", 		//GET or POST
                url: '/main/api/web/countershift.php', 		// Location of the service
                data: {
                    'serial': serial,
                    'queueuid': qid,
                    'shift_counteruid': counter
                },
                dataType: "json", 	//Expected data format from server
                processdata: true 	//True or False
            }).done(function (data) {
                console.log(data);
                if (data.status == true) {
                    authorized = true;
                    Materialize.toast('Counter Shifted', 1500, 'rounded');
                    $('#counterSend').css('display', 'none');
                    $('#queue_repeat').empty();
                    previous = null;
                    previous_serial = -1;
                    updatequeue();
                }
                else if (data.status_code == 402) {
                    authorized = false;
                    swal('Oops..', 'Wrong Data Format!', 'warning');
                    $('#counterSend').css('display', 'none');
                }

                else if (data.status_code == 501) {
                    authorized = false;
                    swal('Error!', 'Please Login Again', 'error');
                    document.location.href = "/main/";
                }
                else if (data.status_code == 502) {
                    authorized = false;
                    Materialize.toast('Counter is closed', 1000, 'rounded');
                } else if (data.status_code == 400) {
                    authorized = false;
                    Materialize.toast('Counter is Invalid', 2500, 'rounded');
                }
                else {
                    authorized = false;
                    Materialize.toast('Error Code:' + data.status_code, 'rounded');
                }
                updatequeue();
            });
        }
    }
});

setInterval(updatequeue, 4000);

