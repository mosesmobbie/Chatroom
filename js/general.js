jQuery(document).ready(function (){
	
	var userslist;	
	var loggedUser;
	var recipientUser;
	var interval = null;
	
	jQuery("#register_btn").click(function()
	{
		displayRegistration();
	});
	
	jQuery("#login_btn").click(function()
	{
		displayLogin();
	});
	
	jQuery(".logout").click(function(){
		jQuery(document.body).css({'cursor' : 'wait'});
		logout();
	});
		
	jQuery(document).on('click', '#listchatusers', function() {
		if(loggedUser)
		{
			jQuery(document.body).css({'cursor' : 'wait'});
			 $("#users_list").find("tr:gt(0)").remove();			
			 hideAll();
			displayUsersList(loggedUser);
		}
	});
		
	jQuery(document).on('click', '.viewprofile', function() {		
		var el_id = jQuery(this).attr('id');
		var row_id = el_id.substr(2);
		viewProfile(row_id);
		jQuery(document.body).css({'cursor' : 'wait'});
	});
	
	jQuery(document).on('click', '.openchat', function() {		
		var el_id = jQuery(this).attr('id');
		var row_id = el_id.substr(4);
		
		recipientUser = (userslist[row_id] ) ? userslist[row_id] : null;
				
		chatNow(recipientUser);
	});
	
	jQuery( "#register_form" ).submit(function( event ){
		event.preventDefault();
		
		if(jQuery("#register_form").valid())
		{
			var form_data = jQuery( this ).serializeArray();
			
			var fn = 1;
			form_data.push({name: 'fn', value: fn});
			
			jQuery(document.body).css({'cursor' : 'wait'});
			
			jQuery.ajax({
				url : "model.php",
				type: 'POST',
				data: form_data,
				data: JSON.stringify(form_data),
				contentType: "application/json",
			    dataType: "json"
			}).done(function (responseData){
				jQuery(document.body).css({'cursor' : 'default'});
				 
				if(responseData.statusCode == '201'){
					jQuery("#login_info_text").addClass("success");
					jQuery("#login_info_text").text(responseData.message);
					clearFields();
					displayLogin();
				}				
				else if(responseData.statusCode == '203'){
					jQuery("#reg_info_text").addClass("error");
					jQuery("#reg_info_text").text(responseData.message);
				}					
				else if(responseData.statusCode == '302'){
					jQuery("#reg_info_text").addClass("warning");
					jQuery("#reg_info_text").text(responseData.message);
				}	
			});
		}
	});
	
	jQuery( "#login_form" ).submit(function( event ){
		event.preventDefault();
		
		if(jQuery("#login_form").valid())
		{
			var form_data = jQuery( this ).serializeArray();
			var fn = 2;
			form_data.push({name: 'fn', value: fn});

			jQuery(document.body).css({'cursor' : 'wait'});
			
			jQuery.ajax({
				url : "model.php",
				type: 'POST',
				data: form_data,
				data: JSON.stringify(form_data),
				contentType: "application/json",
			    dataType: "json"
			}).done(function (responseData){
				
				jQuery(document.body).css({'cursor' : 'default'});
				 
				if(responseData.statusCode == '200'){	
					loggedUser = responseData.data;
					displayUsersList(responseData.data);
					clearFields();
				}								
				else{
					jQuery("#login_info_text").text("");
					jQuery("#login_info_text").addClass("warning");
					jQuery("#login_info_text").text(responseData.message);					
				}	
			});
		}
	});
		
		
	function displayUsersList(userdata)
	{
		clearInterval(interval); // stop the interval

		var fn = 3;
		jQuery(".username").text(userdata.firstname);
		
		$("#users_list").find("tr:gt(0)").remove();	
			
		jQuery("#login_page").hide();
		jQuery("#users_page").show();
		
		var form_data = new Array();

		form_data.push({name: 'fn', value: fn});
		form_data.push({name: 'user_id', value: userdata.id});
		
		jQuery.ajax({
			url : "model.php",
			type: 'POST',
			data: form_data,
			data: JSON.stringify(form_data),
			contentType: "application/json",
		    dataType: "json"
		}).done(function (responseData){
			jQuery(document.body).css({'cursor' : 'default'});
			if(responseData.statusCode == 404)
			{
				alert(responseData.message);
				return false;
			}
			var table_row = "";
			var row_id = 0;
			
			userslist = responseData.data;	
			
			$.each(responseData.data, function(index, val) {
				jQuery(document.body).css({'cursor' : 'default'});
				var viewlink = '<span class="link viewprofile" title="View '+val.firstname+' profile" id="vw'+row_id+'">View</span>';
				var chatlink = '<span class="link openchat" id="chat'+row_id+'">'+val.chat_status+' Chat</span>';
				var namelink = '<span class="link openchat"  title="Chat" id="chat'+row_id+'">'+val.firstname+'</span>';
				
				table_row += '<tr><td>'+namelink+'</td>'
							 +'<td>'+chatlink+' </td>'
							 +'<td>'+val.read_status+'</td>'
							 +'<td class="'+val.status+'">'+val.status+'</td>'
							 +'<td>'+viewlink+'</td></tr>';
				row_id++;
			});
	
			jQuery("#users_list").append(table_row);
		});
	}
	
	function viewProfile(row_id)
	{
		clearInterval(interval); // stop the interval
		jQuery(document.body).css({'cursor' : 'default'});
		
		if(userslist[row_id])
		{
			jQuery("#profile").text('');
			var table_rows = "";		
			
			var chatlink = '<span class="link openchat" id="chat'+row_id+'">'+userslist[row_id].chat_status+' Chat</span>';
			var listlink = '<span class="link" id="listchatusers" title="All Users">Back</span>';
			
			table_rows = '<tr><td><strong>FirstName :</strong> '+userslist[row_id].firstname+'</td></tr>'
					+  '<tr><td><strong>Surname :</strong> '+userslist[row_id].surname+'</td></tr>'
					+ '<tr><td><strong>Email :</strong> '+userslist[row_id].username+'</td></tr>'
					+  '<tr><td><strong>Phone :</strong> '+userslist[row_id].phone+'</td></tr>'
					+  '<tr><td><strong>Gender :</strong> '+userslist[row_id].gender+'</td></tr>'
					+  '<tr><td>'+chatlink+'&nbsp;&nbsp;|&nbsp;&nbsp;'+listlink+'</td></tr>';
			jQuery("#profile").append(table_rows);
			
			jQuery("#users_page").hide();
			jQuery("#profile_page").show();
		}
		else
		{
			alert("Error, profile not found");
		}
	}
	
	function chatNow(recipientUser)
	{		
		jQuery("#chat_messages").html("");
		
		jQuery("#chatting_name").html(recipientUser.firstname);
		jQuery("#chatting_status").html('<h3 class="'+recipientUser.status+'">'+recipientUser.status+'</h3>');
	
		if(recipientUser.chat_status == 'Start')
		{
			// Get Latest Chat Messages
			interval = setInterval( function() { getLatestMessage() }, 500);	
			hideAll();
			jQuery("#chat_page").show();

		}
		else
		{
			var fn = 4;
	
			var form_data = new Array();
			form_data.push({name: 'fn', value: fn});
			form_data.push({name: 'to_user_id', value: recipientUser.user_id});
			form_data.push({name: 'from_user_id', value:loggedUser.id});
			form_data.push({name: 'read_status', value: recipientUser.read_status})
			jQuery.ajax({
				url : "model.php",
				type: 'POST',
				data: form_data,
				data: JSON.stringify(form_data),
				contentType: "application/json",
			    dataType: "json"
			}).done(function (responseData){
				jQuery(document.body).css({'cursor' : 'default'});
				if(responseData.statusCode == 200)
				{	
					var chat_string = '';
					$.each(responseData.data, function(index, val) {
						var bgcolor_class  = (loggedUser.id == val.from_user_id) ? 'lime' : 'cream';
						
						chat_string	+= '<div class="'+bgcolor_class+'" title="'+val.datetime+'"><strong>'+val.firstname+'</strong> - '+val.message+'</div>';	
					});
					jQuery("#chat_messages").html(chat_string);
					
					scrollMessageToTop();
					hideAll();
					jQuery("#chat_page").show();
				}
				interval = setInterval( function() { getLatestMessage() }, 500);					
			});
		}
	}
	
	function getLatestMessage()
	{
		var fn = 5;
		
		var form_data = new Array();
		form_data.push({name: 'fn', value: fn});
		form_data.push({name: 'to_user_id', value:loggedUser.id});
		form_data.push({name: 'from_user_id', value:recipientUser.user_id});
		jQuery.ajax({
			url : "model.php",
			type: 'POST',
			data: form_data,
			data: JSON.stringify(form_data),
			contentType: "application/json",
		    dataType: "json"
		}).done(function (responseData){
			jQuery(document.body).css({'cursor' : 'default'});
			if(responseData.statusCode == 200)
			{				
				recipientUser.read_status = responseData.data.user_status;
				var chat_string = '<div class="cream" title="'+responseData.data.datetime+'"><strong>'+responseData.data.firstname+'</strong> - '+responseData.data.message+'<div>';
				jQuery("#chat_messages").append(chat_string);	
				scrollMessageToTop();
				jQuery("#chatting_status").html('<h3 class="'+responseData.data.user_status+'">'+responseData.data.user_status+'</h3>');
			}
		});		
	}
	
	jQuery("#send_chat_btn").click(function( event ){
		var chat_input_text = jQuery("#chat_input").val();
		if(chat_input_text != '')
		{
			var d = new Date();
			var datetime = d.toLocaleString();
			var chat_string = '<div class="lime" title="'+datetime+'"><strong>'+loggedUser.firstname+'</strong> - '+chat_input_text+'<div>';
			jQuery("#chat_messages").append(chat_string);
			scrollMessageToTop();
			
			jQuery("#chat_input").val('');
			// Store into the database
			var fn = 6;

			var form_data = new Array();
			form_data.push({name: 'fn', value: fn});
			form_data.push({name: 'from_user_id', value:loggedUser.id});
			form_data.push({name: 'message', value:chat_input_text});
			form_data.push({name: 'to_user_id', value:recipientUser.user_id});
			
			var user_status = (recipientUser.status == "online") ? 1 : 0; 
			form_data.push({name: 'status', value:user_status});
			
			jQuery.ajax({
				url : "model.php",
				type: 'POST',
				data: form_data,
				data: JSON.stringify(form_data),
				contentType: "application/json",
			    dataType: "json"
			}).done(function (responseData){
				jQuery(document.body).css({'cursor' : 'default'});
			});	
		}
	});
	
	function displayRegistration()
	{
		hideAll();
		jQuery("#registration_page").show();
	}
	
	function displayLogin()
	{
		hideAll();
		jQuery("#login_page").show();
	}
	
	function hideAll()
	{
		jQuery("#registration_page").hide();	
		jQuery("#profile_page").hide();	
		jQuery("#chat_page").hide();	
		jQuery("#users_page").hide();	
		jQuery("#registration_page").hide();	
		jQuery("#login_page").hide();
	}
	
	function logout()
	{
		var fn = 7;

		var form_data = new Array();
		form_data.push({name: 'fn', value: fn});
		form_data.push({name: 'user_id', value: loggedUser.id});
		
		jQuery.ajax({
			url : "model.php",
			type: 'POST',
			data: form_data,
			data: JSON.stringify(form_data),
			contentType: "application/json",
		    dataType: "json"
		}).done(function (response){
			jQuery(document.body).css({'cursor' : 'default'});
			clearInterval(interval); // stop the interval
			displayLogin();
		});
	}
	
	function clearFields()
	{
		$("#reg_info_text").text('');
		$("#login_info_text").text('');
		$('#register_form input[type="text"]').val('');
		$('#login_form input[type="text"]').val('');
		$('#register_form input[type="password"]').val('');
		$('#login_form input[type="password"]').val('');
		jQuery("#chat_messages").html("");
	}
	
	function scrollMessageToTop()
	{
		  var chat_window    = $('#chat_messages');
		  var height = chat_window[0].scrollHeight;
		  chat_window.scrollTop(height);
	}

});
