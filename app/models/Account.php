<?php

namespace App\models;

use App\core\Model;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\lib\Db;
use PDO;

class Account extends Model
{
	public $error;
	private $id;
	private $name;
	private $email;
	private $loginStatus;
	private $regStatus;
	private $password;
	private $lastLogin;
	private $avatar;
	private $token;
	private $hideEmail;
	public $dbConn;

	function setId($id)
	{
		$this->id = $id;
	}

	function getId()
	{
		return $this->id;
	}

	function setName($name)
	{
		$this->name = $name;
	}

	function getName()
	{
		return $this->name;
	}

	function setEmail($email)
	{
		$this->email = $email;
	}

	function getEmail()
	{
		return $this->email;
	}

	function setLoginStatus($loginStatus)
	{
		$this->loginStatus = $loginStatus;
	}

	function getLoginStatus()
	{
		return $this->loginStatus;
	}

	function setRegStatus($regStatus)
	{
		$this->regStatus = $regStatus;
	}

	function getRegStatus()
	{
		return $this->regStatus;
	}

	function setPassword($password)
	{
		$this->password = $password;
	}

	function getPassword()
	{
		return $this->password;
	}

	function setLastLogin($lastLogin)
	{
		$this->lastLogin = $lastLogin;
	}

	function getLastLogin()
	{
		return $this->lastLogin;
	}

	function setAvatar($avatar)
	{
		$this->avatar = $avatar;
	}

	function getAvatar()
	{
		return $this->avatar;
	}

	function setHideEmail($hideEmail)
	{
		$this->hideEmail = $hideEmail;
	}

	function getHideEmail()
	{
		return $this->hideEmail;
	}

	function setToken($token)
	{
		$this->token = $token;
	}

	function getToken()
	{
		return $this->token;
	}

	public function __construct()
	{
		$db = new Db();
		$this->dbConn = $db->connect();
	}

	public function validate($input, $post)
	{
		$rules =
			[
				'email' =>
				[
					'pattern' => '#^([a-z0-9_.-]{1,20}+)@([a-z0-9_.-]+)\.([a-z\.]{2,10})$#',
					'message' => 'Email указан неверно',
				],

				'login' =>
				[
					'pattern' => '#^[a-z0-9]{2,15}$#',
					'message' => 'Логин указан неверно (разрешены только латинские буквы и цифры от 2 до 15 символов)',
				],

				'password' =>
				[
					'pattern' => '#^[a-z0-9]{3,30}$#',
					'message' => 'Пароль указан неверно (разрешены только латинские буквы и цифры от 3 до 30 символов)',
				],
			];

		foreach ($input as $val) {
			if (!isset($post[$val]) or !preg_match($rules[$val]['pattern'], $post[$val])) {
				$_SESSION['errors'][] = $rules[$val]['message'];
				return false;
			}
		}
		return true;
	}

	public function save()
	{
		$sql = "INSERT INTO `users`(`id`, `name`, `email`, `password`, `token`, `reg_status`, `login_status`, `last_login`, `avatar`, `hide_email`) VALUES (null, :name, :email, :password, :token, :regStatus, :loginStatus, :lastLogin, :avatar, :hideEmail)";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindParam(":name", $this->name);
		$stmt->bindParam(":email", $this->email);
		$stmt->bindParam(":loginStatus", $this->loginStatus);
		$stmt->bindParam(":lastLogin", $this->lastLogin);
		$stmt->bindParam(":password", $this->password);
		$stmt->bindParam(":token", $this->token);
		$stmt->bindParam(":avatar", $this->avatar);
		$stmt->bindParam(":regStatus", $this->regStatus);
		$stmt->bindParam(":hideEmail", $this->hideEmail);

		$mail = new PHPMailer();
		try {
			$mail->CharSet = 'utf-8';
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'r2521663@gmail.com';
			$mail->Password = 'djcarciahikhsvhe';
			$mail->SMTPSecure = 'tls';
			$mail->Port = 587;
			$mail->setFrom('r2521663@gmail.com');
			$mail->addAddress($this->email);
			$mail->isHTML(true);
			$mail->Subject = 'Подтверждение регистрации SF-33';
			$mail->Body = 'Для подтверждения регистрации нажмите ссылку http://' . $_SERVER['HTTP_HOST'] . '/confirm/' . urlencode($this->token);
			$mail->send();
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}

		try {
			if ($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function createToken()
	{
		return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 30)), 0, 30);
	}

	public function getUserByEmail()
	{
		$stmt = $this->dbConn->prepare('SELECT * FROM users WHERE email = :email');
		$stmt->bindParam(':email', $this->email);
		try {
			if ($stmt->execute()) {
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return $user;
	}

	public function getUserById()
	{
		$stmt = $this->dbConn->prepare('SELECT * FROM users WHERE id = :id');
		$stmt->bindParam(':id', $this->id);
		try {
			if ($stmt->execute()) {
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return $user;
	}

	public function getUserByToken()
	{
		$stmt = $this->dbConn->prepare('SELECT * FROM users WHERE token = :token');
		$stmt->bindParam(':token', $this->token);
		try {
			if ($stmt->execute()) {
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
				var_dump($user);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return $user;
	}

	public function query($sql, $params = [])
	{
		$stmt = $this->dbConn->prepare($sql);
		if (!empty($params)) {
			foreach ($params as $key => $val) {
				if (is_int($val)) {
					$type = PDO::PARAM_INT;
				} else {
					$type = PDO::PARAM_STR;
				}
				$stmt->bindValue(':' . $key, $val, $type);
			}
		}
		$stmt->execute();
		return $stmt;
	}

	public function column($sql, $params = [])
	{
		$result = $this->query($sql, $params);
		return $result->fetchColumn();
	}

	public function checkTokenExists($token)
	{
		$params =
			[
				'token' => $token,
			];

		if ($this->column('SELECT id FROM users WHERE token = :token', $params)) {
			return true;
		} else return false;
	}

	public function checkUserExists($name)
	{
		$params =
			[
				'name' => $name,
			];

		if ($this->column('SELECT id FROM users WHERE name = :name', $params)) {
			return true;
		} else return false;
	}

	public function getIdByName($name)
	{
		$params =
			[
				'name' => $name,
			];

		return $this->column('SELECT id FROM users WHERE name = :name', $params);
	}

	public function activate($token)
	{
		$stmt = $this->dbConn->prepare('UPDATE users SET reg_status = 1, token = ""  WHERE token = :token');
		$stmt->bindParam(':token', $token);
		try {
			if ($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function updateHideEmail($id)
	{
		$stmt = $this->dbConn->prepare('UPDATE users SET hide_email = :hide_email  WHERE id = :id');
		$stmt->bindParam(':id', $id);
		$stmt->bindParam(':hide_email', $this->hideEmail);
		try {
			if ($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function updateUser($id)
	{
		$stmt = $this->dbConn->prepare('UPDATE users SET name = :name  WHERE id = :id');
		$stmt->bindParam(':id', $id);
		$stmt->bindParam(':name', $this->name);
		try {
			if ($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function checkLogin($email, $password)
	{
		$params =
			[
				'email' => $email,
			];

		$hash = $this->column('SELECT password FROM users WHERE email = :email', $params);

		if (!$hash || !password_verify($password, $hash)) {
			return false;
		}
		return true;
	}

	public function checkReg($email)
	{
		$params =
			[
				'email' => $email,
			];

		$reg = $this->column('SELECT reg_status FROM users WHERE email = :email', $params);

		if ($reg == 0) {
			return false;
		}
		return true;
	}

	public function updateLoginStatus()
	{
		$stmt = $this->dbConn->prepare('UPDATE users SET login_status = :loginStatus, last_login = :lastLogin WHERE id = :id');
		$stmt->bindParam(':loginStatus', $this->loginStatus);
		$stmt->bindParam(':lastLogin', $this->lastLogin);
		$stmt->bindParam(':id', $this->id);
		try {
			if ($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function getAllUsers()
	{
		$stmt = $this->dbConn->prepare("SELECT * FROM users");
		$stmt->execute();
		$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $users;
	}

	// avatar image uploads
	public function imageValidate()
	{
		if (empty($_FILES['img']['tmp_name'])) {
			$_SESSION['errors'][] = 'Загрузите изображение';
			header('Location: ' . $_SERVER['REQUEST_URI']);
			return false;
		}

		if ($_FILES['img']['size'] > MAX_FILE_SIZE) {
			$_SESSION['errors'][] = 'Недопустимый размер файла';
			header('Location: ' . $_SERVER['REQUEST_URI']);
			return false;
		}

		if (!in_array($_FILES['img']['type'], ALLOWED_TYPES)) {
			$_SESSION['errors'][] = 'Недопустимый формат файла';
			header('Location: ' . $_SERVER['REQUEST_URI']);
			return false;
		}
		return true;
	}

	public function imageAdd($id)
	{
		$stmt = $this->dbConn->prepare('UPDATE users SET avatar = 1 WHERE id = :id');
		$stmt->bindParam(':id', $id);
		try {
			if ($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function imageUpload($path, $file)
	{
		move_uploaded_file($path, UPLOAD_DIR . DIRECTORY_SEPARATOR . $file . '.png');
	}

	static public function checkAuth(): bool
	{
		return isset($_SESSION['user']);	
	}
}
