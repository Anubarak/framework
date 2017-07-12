$(document).ready(function (){
	$('.toggle').on('click', function() {
	  $('.container').stop().addClass('active');
	});

	$('.close').on('click', function() {
	  $('.container').stop().removeClass('active');
	});
	
	$("#showLoginPanel").on('click', '#loginBtn' , function(event) {
		var container = $(".container");
		$(".div_blackscreen").fadeIn();
		$("#errorMessageLogin").text("");
		return false;
	});
	
	$(".div_blackscreen").click(function(){
		$(this).fadeOut();
	}).children().click(function(e) {
		return false;
	});
	
	$("#login-btn").click(function(){
		var name = $("#Username").val();
		var password = $("#Password").val();
		if(!(name == null || name == "") && !(password == null || password == "")){
			$.ajax({
				type: "POST",
				url: "index.php?page=ajaxCallLoginUser&renderOnlyContent=true",
				data: {
					name: name, password:password
				},
				success: function (data) {
					if(data == "true"){
						$(".div_blackscreen").fadeOut();
                        $("#showLoginPanel").html("Hallo " + name + '<a href="" class="" id="logoutBtn">Logout</a>');
					}else{
						$("#errorMessageLogin").text(data);
					}
				}
			});
		}else{
			$("#errorMessageLogin").text("username or password not set");
		}
	});
	
	$("#showLoginPanel").on('click', '#logoutBtn' , function(event) {
		$.ajax({
			type: "POST",
			url: "index.php?page=ajaxCallLogout&renderOnlyContent=true",
			data: {
			},
			success: function (data) {
				$("#showLoginPanel").html(data);
			}
		});
	});

    $(".container").on('keypress', '.loginInput' , function(event) {
        console.log(event);
        if(event.which == 13) {
            $("#login-btn").click();
        }
    });

    $(".container").on('keypress', '.registerInput' , function(event) {
        console.log(event);
        if(event.which == 13) {
            $("#register-btn").click();
        }
    });
	
	$("#register-btn").click(function(){
		var name = $("#UsernameRegister").val();
		var password = $("#PasswordRegister").val();
		var passwordRepeat = $("#RepeatPasswordRegister").val();
		if(!(name == null || name == "") && !(password == null || password == "") && !(passwordRepeat == null || passwordRepeat == "")){
			if (passwordRepeat == password){
				$.ajax({
					type: "POST",
					url: "index.php?page=ajaxCallInsertUser&renderOnlyContent=true",
					data: {
						name: name, password:password
					},
					success: function (data) {
						if(data == "true"){
							$(".div_blackscreen").fadeOut();
                            $("#showLoginPanel").html("Hallo " + name + '<a href="" class="" id="logoutBtn">Logout</a>');
						}else{
							$("#errorMessageRegistration").text(data);
						}
					}
				});
			}else{
				$("#errorMessageRegistration").text("passwords must be equal");
			}
		}else{
			$("#errorMessageRegistration").text("username or password not set");
		}
	});
	
});