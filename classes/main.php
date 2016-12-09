<?php

class Main
{
	public $pdo;
	
	private $config; 
	
	private $hmac_secret = "Cr-*";
	
	public function __construct()
	{
		$this->config = parse_ini_file("config.ini");
		$this->getPDO();
	}
	
	/**
	 * Function gets Database Connection
	 */
	public function getPDO()
	{
		//MySQL connection details.
		$host = $this->config['dbhost'];
		$user = $this->config['dbuser'];
		$pass = $this->config['dbpass'];
		$database = $this->config['dbname'];
		
		try {
			$this->pdo = new PDO("mysql:host=$host;dbname=$database", $user, $pass);
		}
		catch (PDOException $e){
			$error_string = "Database Connection Error : ".$e->getMessage();
			$this->setLog("ERROR", $error_string);
			echo $error_string;
		}
	}
	
	/**
	 * Function stores user details into the Database and send email if user has been insert into the DB
	 * 
	 * @param array $data
	 */
	public function createUser($data)
	{
		$sql =  "INSERT INTO users (username, password, firstname, surname, phone, gender, datetime)
			  	 VALUES (:username, :password, :firstname, :surname, :phone, :gender, :datetime)";
	
		$stmt = $this->pdo->prepare($sql);
		
		$stmt->bindValue(':username', $data['email']);
		$stmt->bindValue(':password', $this->hash_password($data['password']));
		$stmt->bindValue(':firstname', $data['firstname']);		
		$stmt->bindValue(':surname', $data['surname']);
		$stmt->bindValue(':phone', $data['phone']);
		$stmt->bindValue(':gender', $data['gender']);
		$stmt->bindValue(':datetime', date("Y-m-d H:i:s"));		
		
		$stmt->execute();
		
		$id = $this->pdo->lastInsertId();

		if($id > 0){
			$this->setLog("INFO", "User has been created, ID : ".$id." | params => username: ".$data['email'].", phone : ".$data['phone']." and  name : ".$data['firstname']);
		}else{			
			$this->setLog("ERROR", "Error occured while creating a user | params => username: ".$data['email'].", phone : ".$data['phone']." and  name : ".$data['firstname']);
		}
		
		return $id;
	}

	/**
	 * Function gets the user data object given the username and password, if store object in a session
	 * 
	 * @param object $data
	 */
	public function loginUser($data)
	{
		$sql = "SELECT * FROM users WHERE username = :username AND password = :password LIMIT 1";
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array(":username" => $data['email'], ":password" => $this->hash_password($data['password'])));
		$user = $stmt->fetch(PDO::FETCH_OBJ);
		
		if(!empty($user)){
			$this->storeUserSession($user->id);
			return $user;
		}
		
		return false;
	}
	
	/**
	 * Function, return array object of three left joined table, given the user id
	 * 
	 * @param int $user_id
	 */
	public function getChatUsers( $user_id)
	{
		$sql = "SELECT 
				users.id AS user_id,
				users.firstname, 
				users.surname,
				users.username,
				users.phone,
				IF(users.gender = 0, 'Male', 'Female') AS gender,
				IF(chat.to_user_id = :user_id, 'Continue' , 'Start') AS chat_status,
				IF(chat.read_status = 0, 'yes', '') AS read_status,
				IF(sessions.id IS NULL, 'offline', 'online') AS `status`
				
				FROM users AS users
				LEFT JOIN `session` AS sessions
				ON users.id = sessions.user_id
				LEFT JOIN `chats` AS chat
				ON users.id = chat.from_user_id
				WHERE users.id != :user_id
				GROUP BY users.id";
	
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array(":user_id" => $user_id));
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $users;
	}
		
	/**
	 * Function return chat messages betwwen two users
	 * 
	 * @param int $user_from
	 * @param int $user_to
	 * @return mixed
	 */
	function getChatMessages($data)
	{
		if($data['read_status'] == "yes")
		{
			$this->updateReadMessages($user_from, $user_to);
		}
		
		$sql = "SELECT
				chat.id,
				chat.datetime,
				chat.message,
				chat.from_user_id,
				user.firstname
				FROM chats AS chat
				INNER JOIN users AS user
				ON chat.from_user_id = user.id
				WHERE
				(chat.from_user_id = :user_from	AND chat.to_user_id = :user_to)		
				OR (chat.from_user_id = :user_to AND chat.to_user_id = :user_from)";
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array(':user_from' => $data['from_user_id'], ':user_to' => $data['to_user_id']));
		$messages = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		return $messages;
	}

	/**
	 * Function update unread chat messages betwwen two users
	 *
	 * @param int $user_from
	 * @param int $user_to
	 * @return mixed
	 */
	public function updateReadMessages($user_from, $user_to)
	{
		$sql = "UPDATE chats SET read_status = :read_status WHERE from_user_id = :user_from AND to_user_id = :user_to";
		$stmt = $this->pdo->prepare($sql);
		$status = 1;
		$stmt->bindParam(':read_status', $status);
		$stmt->bindParam(':user_from', $user_from);
		$stmt->bindParam(':user_to', $user_to);

		return $stmt->execute();
	}
	
	/**
	 * Function return Live chat messages betwwen two users
	 *
	 * @param object $data
	 * @return mixed
	 */
	function getLiveChatMessage($data)
	{
		$sql = "SELECT
				chats.id AS live_chat_id,
				chats.datetime,
				chats.`message`,
				chats.from_user_id,
				chats.to_user_id,
				users.firstname,
				IF(session.id > 0, 'online', 'offline') AS user_status
				FROM `chats`
				INNER JOIN users AS users
				ON users.id = chats.from_user_id
				INNER JOIN `session`
				ON session.user_id = chats.from_user_id
				WHERE
				chats.from_user_id = :user_from
				AND chats.to_user_id = :user_to
				AND chats.read_status = :unread
				ORDER BY chats.id DESC
				LIMIT 1";
	
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array(':user_from' => $data['from_user_id'], ':user_to'=> $data['to_user_id'], ':unread' => 0 ));
				
		$message = $stmt->fetch(PDO::FETCH_OBJ);
		
		if(!empty($message))
		{
			$this->updateReadChat($message->live_chat_id);
		}

		return $message;
	}
	
	/**
	 * Function update unread chat messages betwwen two users
	 *
	 * @param int $chat_id
	 * @return mixed
	 */
	public function updateReadChat($chat_id)
	{
		$sql = "UPDATE chats SET read_status = :read_status WHERE id = :chat_id";
		$stmt = $this->pdo->prepare($sql);
		$status = 1;
		$stmt->bindParam(':read_status', $status);
		$stmt->bindParam(':chat_id', $chat_id);

		return $stmt->execute();
	}
	
	/**
	 * Function save new chat message into the database
	 * 
	 * @param object $data
	 */
	public function saveNewChat($data)
	{		
		$sql =  "INSERT INTO chats (datetime, message,  from_user_id, to_user_id, `read_status`)
			  	 VALUES (:datetime, :message, :from_user_id, :to_user_id, :read_status)";
		
		$stmt = $this->pdo->prepare($sql);
		
		$stmt->bindValue(':datetime', date("Y-m-d H:i:s"));
		
		$message = $data['message'];
		$read_status = 0;
		
		$stmt->bindValue(':message', $message);
		$stmt->bindValue(':from_user_id', $data['from_user_id']);
		$stmt->bindValue(':to_user_id', $data['to_user_id']);
		$stmt->bindValue(':read_status', $read_status);
	
		$stmt->execute();
		
		$id = $this->pdo->lastInsertId();
		
		if($id > 0){
			$this->setLog("INFO", "Message Save : ".$id);
		}else{
			$this->setLog("ERROR", "Error Saving Message | params => from: ".$data['from_user_id']);
		}
		
		return $id;		
	}
		
	
	/**
	 * Function return username already exist on the DB
	 *
	 * @param string $username
	 */
	public function checkUsername($username)
	{
		$sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array(':username' => $username));
		$user = $stmt->fetch(PDO::FETCH_OBJ);
		if(!empty($user))
			return true;
		
		return false;
	}
	
	/**
	 * Function store user session on the DB
	 * 
	 * @param int $user_id
	 */	
	public function storeUserSession($user_id)
	{
		$sql =  "INSERT INTO session (datetime, user_id)
			  	 VALUES (:datetime, :user_id)";
		
		$stmt = $this->pdo->prepare($sql);
		
		$stmt->bindValue(':user_id', $user_id);
		$stmt->bindValue(':datetime', date("Y-m-d H:i:s"));
		
		$stmt->execute();
		
		$id = $this->pdo->lastInsertId();		
		$this->setLog("INFO", "User session stored");
		
		return true;
	}

	/**
	 * Function clear user session on the DB
	 *
	 * @param int $user_id
	 */
	public function clearUserSession($user_id)
	{
		
		$sql = "DELETE FROM session WHERE user_id = :user_id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindParam(':user_id', $user_id);
		$status = 1;
		return $stmt->execute();		
		
		$this->setLog("INFO", "User session cleared");
		return true;
	}	
	
		
	/**
	 * Function encrypt user password using hash_mac 
	 * 
	 * @param string $password
	 */
	public function hash_password($password)
	{
		$hashed_string = hash_hmac('ripemd160', $password, $this->hmac_secret);
	
		return $hashed_string;
	}	
	
	/**
	 * Function log string to a file
	 * @param string $error_type
	 * @param string $string
	 */
	public function setLog($error_type, $string)
	{
		$file = dirname(dirname(__FILE__))."/logs/LOG.txt";
		$handle = fopen($file, 'a+');
	
		$line = "[".date("Y-m-d H:i:s")."] ".$error_type. " - ".$string.PHP_EOL;
	
		fwrite($handle, $line);
		fclose($handle);
	}
}