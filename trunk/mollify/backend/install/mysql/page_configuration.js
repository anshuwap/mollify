function validate() {
	$(".user-data").removeClass("user-data-invalid");

	var result = true;
	if ($("#admin-username-field").val().length == 0) {
		$("#admin-username").addClass("user-data-invalid");
		result = false;
	}
	if ($("#admin-password-field").val().length == 0) {
		$("#admin-password").addClass("user-data-invalid");
		result = false;
	}
	return result;
}

function on_error(error) {
	show_error(error);
	add_button('#conf-footer', 'conf-retry', 'Retry', on_install);
}

function on_install() {
	if (!validate()) return;
	empty_errors();
	
	$("#conf-footer").html("<div class='progress'>Installing...</div>");
	var data = "action=install&username=" + $("#admin-username-field").val() + "&password=" + generate_md5($("#admin-password-field").val());
	
	request("mysql/installation.php", data,
		function(result) {
			$("#conf-footer").empty();
			
			if (!result.success) on_error(result.error);
			else $("#page").load("mysql/page_install.html");
		}
	);
}

function init_page_configuration() {
	set_multipage_title(2, 3, "Mollify Configuration");
	$("#install.button").click(on_install);
}