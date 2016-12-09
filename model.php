<?php

require_once 'classes/main.php';

$obj = new Main();

// get Json post data
$json_data = json_decode(file_get_contents("php://input"));

$data = formatPostData($json_data);

switch ($data['fn'])
{
	// User Registration
	case 1:
		$username_exist = $obj->checkUsername($data['email']);
		
		if($username_exist){
			outputJson(302, "Found", null, "Email already registered, please login");
		}else{
			$user_id = $obj->createUser($data);
		
			if($user_id == 0)
			{
				outputJson(203, "Bad Request", null, "Error Occured");
			}
			else
			{
				outputJson(201, "Success", null, "Registration was successful, please login");
			}
		}
		break;
	
	// User Login
	case 2:
		$logged_data = $obj->loginUser($data);
		
		if($logged_data != false)
		{
			outputJson(200, "Ok", $logged_data, "Login");
		}
		else
		{
			outputJson(203 , "Non-Authoritative Information", null, "Wrong login credentials, please try again");
		}		
		break;
		
	// Display Chatroom users	
	case 3:
			$users = $obj->getChatUsers($data['user_id']);
			
			if(!empty($users))
			{
				outputJson(200, "Ok", $users, "Found");
			}
			else
			{
				outputJson(404, "Not Found", null, "No other chatroom users were found");
			}
		break;
		
	// get Messages
	case 4:
			$messages = $obj->getChatMessages($data);
				
			if(!empty($messages))
			{
				outputJson(200, "Ok", $messages, "Found");
			}
			break;		
	
	//	get Messages and status		
	case 5:
		$livechat = $obj->getLiveChatMessage($data);
		
		if(!empty($livechat))
		{
			outputJson(200, "Ok", $livechat, "Found");
		}
		break;					
		
	//	save chat
	case 6:
		$new_chat_id = $obj->saveNewChat($data);
		if($new_chat_id){
			outputJson(201, "success", null, "Created");
		}
		else
		{
			outputJson(203, "Bad Request", null, "Error Occured");
		}
		break;	
		
	// logout
	case 7:
			$logout = $obj->clearUserSession($data['user_id']);
			outputJson(200, "Ok", null, "Logo Out");
		break;
			
	default:
	
}

// format post data from json post to
function formatPostData($postdata)
{	
	$data = array();

	for($i = 0; $i < count($postdata); $i++)
	{
		$key = $postdata[$i]->name;
		$value = $postdata[$i]->value;
		
		$data[$key] = $value;
	}
	return $data;
}

function outputJson($status_code, $status = null, $data = null, $message = null)
{
	$response_data = new stdClass();
	$response_data->statusCode = $status_code;
	$response_data->status = $status;
	if(!empty($data)) $response_data->data = $data;
	if(!empty($message)) $response_data->message = $message;

	echo json_encode($response_data);
	exit();
}
?>