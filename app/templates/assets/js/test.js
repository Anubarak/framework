$(document).ready(function(){
    $.ajax({
        type: "post",
        url: '',
        dataType : 'json',
        data: {
            action: "home/test"
        },
        success: function(data){

            console.log(data);
        },
        error: function (XMLHttpRequest, textStatus) {
            console.log("Status: " + textStatus);
        }
    });
});