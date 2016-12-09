jQuery(document).ready(function() {
	jQuery("#register_form, #login_form").validate({
			rules: {
				firstname: {
					required: true,
					minlength: 3
				},
				surname: {
					required: true,
					minlength: 3
				},
				phone: {
					required: true,
					minlength: 10,
					maxlength: 10
				},
				password: {
					required: true,
					minlength: 3
				},			
				gender: {
					required: true
				},				
				email: {
					required: true,
					email: true,
				}
			},
			messages: {
				firstname: {
					required: "Please enter your firstname",
					minlength: "Your firstname must consist of at least 3 characters"
				},
				surname: {
					required: "Please enter your surname",
					minlength: "Your surname must consist of at least 3 characters"
				},
				phone: {
					required: "Please enter your phon enumber",
					minlength: "Your phone number must be 10 digit"
				},				
				gender: {
					required: "Please select your gender"
				},				
				password: {
					required: "Please enter your password",
					minlength: "Your password must consist of at least 3 characters"
				},				
				email: "Please enter a valid email address",
			}
		});	
});