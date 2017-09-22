// var qrcode = new QRCode("qrcode");


var qrcode = new QRCode("qrcode", {
    text: "http://jindo.dev.naver.com/collie",
    width: 300,
    height: 300,
    colorDark: "#000000",
    colorLight: "#fcf7f7",
    correctLevel: QRCode.CorrectLevel.H
});


function makeCode() {
    var elText = document.getElementById("text");

    if (!elText.value) {
        alert("Input a text");
        elText.focus();
        return;
    }

    qrcode.makeCode(elText.value);
}

makeCode();

$("#text").on("blur", function () {
    makeCode();
}).on("keydown", function (e) {
    if (e.keyCode == 13) {
        makeCode();
    }
});
