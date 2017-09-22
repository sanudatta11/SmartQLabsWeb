$(document).ready(function () {
    $('#title_qit').addClass('animated fadeInDown');
    $('#animate_row').addClass('animated fadeInUp');
    $('#title2').addClass('animated slideInLeft');
    $('#chatButton').addClass('animated rotateInDownRight');

    // $('#chart_div').addClass('animated fadeInUp');
    // $('#chart_div_2').addClass('animated fadeInUp stayHidden');
    // $('#chart_div_3').addClass('animated fadeInUp stayHidden');

});

function toggle() {
    //console.log("CLicked");

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
var myFlag = 0;
$('#doneCounter').click(function () {
    $('#topRight').css("display", "none");
    console.log("SHRIOM FUCKS");
    myFlag = 0;
});

$('#increaseCounter').click(function () {
    if (myFlag == 0) {
        $('#topRight').css("display", "block");
        myFlag = 1;
    }
    else {
        $('#topRight').css("display", "none");
        myFlag = 0;
    }

    // $('#topRight').addClass('animated fadeInUp');


});