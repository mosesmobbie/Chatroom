<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'classes/main.php';

$obj = new Main();

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Welcome to Rate N Date</title>
		<meta charset="utf-8">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico"/>
		<link rel="stylesheet" href="css/template.css">
		<script src="js/js_jquery.min.js"></script>		
		<script src="js/jquery.validate.min.js"></script>		
		<script src="js/customValidation.js"></script>
		<script src="js/general.js"></script>
	</head>
<body>
	<div class="wrapper">
		<div class="header"><img src="images/logo.png" height="75px"/></div>
	
		<div id="registration_page" style="display:none;">
		<div class="menu">
			<span class="active">Register </span> | <span id="login_btn">Login</span>
		</div>
		<div class="cls"></div>
			<div id="reg_info_text"></div>	
			<form id="register_form" class="centered">

				<table class="register_table">
					<tr>
						<td class="right"><label for="firstname">Firstname</label></td>
						<td><input type="text" name="firstname" id="firstname" required="required" data-val="true"/></td>
					</tr>
					<tr>
						<td class="right"><label for="surname">Surname</label></td>
						<td><input type="text" name="surname" id="surname" required="required" data-val="true"/></td>
					</tr>	
					<tr>
						<td class="right"><label for="email">Email</label></td>
						<td><input type="text" name="email" id="email" required="required" data-val="true"/></td>
					</tr>									
					<tr>
						<td class="right"><label for="password">Password</label></td>
						<td><input type="password" name="password" id="password" required="required" data-val="true"/></td>
					</tr>
					<tr>
						<td class="right"><label for="phone">Phone</label></td>
						<td><input type="text" name="phone" id="phone" placeholder="0801234567" required="required" data-val="true"/></td>
					</tr>	
					<tr>
						<td class="right"><label for="gender">Gender</label></td>
						<td><select id="gender" name="gender" required="required" data-val="true">
								<option value="">select</option>
								<option value="0">Male</option>
								<option value="1">Female</option>
							</select>
						</td>
					</tr>				
					<tr>
						<td colspan="2" class="center">
							<input type="submit" value="Submit"/>
						</td>
					</tr>
				</table>											
			</form>				
		</div>
		
		<div id="login_page" class="minheight">
			<div class="menu">
				<span id="register_btn">Register </span> | <span class="active">Login</span>
			</div>
			<div class="cls"></div>
			<div id="login_info_text"></div>	
			<form id="login_form" class="centered">
				<table class="login_table">
					<tr>
						<td class="right"><label for="email">Email</label></td>
						<td class="left"><input type="text" name="email" id="email" required="required" data-val="true"/></td>
					</tr>									
					<tr>
						<td class="right"><label for="password">Password</label></td>
						<td class="left"><input type="password" name="password" id="password" required="required" data-val="true"/></td>
					</tr>				
					<tr>
						<td colspan="2" class="center">
							<input type="submit" value="Submit"/>
						</td>
					</tr>
				</table>								
			</form>				
		</div>		
	
		
		<div id="users_page" class="minheight" style="display:<?php "block;"//echo $sales_page_display;?>;">
			<div class="namestrap">
				<div class="floatleft"><strong><span class="username"></span></strong></div>
				<div class="floatright"><a href="#" class="logout">LOGOUT</a></div>				
			</div>
			<table id="users_list" class="list_table">
				<tr>
					<th>Name</th>
					<th>Chats</th>
					<th>Unread</th>
					<th>Status</th>
					<th>View Profile</th>
				</tr>			
			</table>		
		</div>
		
		<div id="profile_page" class="minheight">
			<div class="namestrap">
				<div class="floatleft"><strong><span class="username"></span></strong></div>
				<div class="floatright"><a href="#" class="logout">LOGOUT</a></div>			
			</div>		
			<table id="profile" class="list_table" style="width:30%!important;">	
			</table>
		</div> 
		
		<div id="chat_page" class="minheight">
			<div class="namestrap">
				<div class="floatleft"><strong><span class="username"></span></strong></div>
				<div class="floatright"><a href="#" class="logout">LOGOUT</a></div>			
			</div>	
			<div class="div_35 minheight">
					<h1 id="chatting_name"></h1><br>
					<span id="chatting_status"></span>
			</div> 	
			<div class="div_60">
			<div class="text_area" id="chat_messages"></div>			
				<textarea rows="2" cols="40" id="chat_input"></textarea>
				<button id="send_chat_btn">Send</button>
			</div>
			<div class="cls"></div>
			<span class="link" id="listchatusers" title="All Users">Back user list</span>
		</div> 
	</div>
</body>
</html>