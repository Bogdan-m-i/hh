<?php
// header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
// header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
// header("Access-Control-Allow-Headers: Content-Type");
// header("Access-Control-Allow-Credentials: true");
// if (isset($_SERVER['HTTP_ORIGIN'])) {
// 	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
// 	header('Access-Control-Allow-Credentials: true');
// 	header('Access-Control-Max-Age: 86400');    // cache for 1 day
// }
// header('Content-Type: text/html; charset=utf-8');
// if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
// 	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
// 		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
	
// 	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
// 		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

// 	exit(0);
// }
// header('Content-Type: application/json');

$row = $_SERVER['REQUEST_URI'];
if ($str = strpos($row, "?")) {
	$row = substr($row, 0, $str);
}
$routes = explode('/', $row);

class Controller {

	function __construct($action = 'test') {
		if (method_exists($this, $action)) {

			if(file_get_contents('php://input')) {
				$_POST = json_decode(file_get_contents('php://input'), true);
			}

			$this->$action();

		} else {
			header("HTTP/1.0 404 Not Found");
		}
	}

	public function test() {
		print_r($_POST);
		echo json_encode('test action');
	}

	public function getComments() {
		$sql = $this->sql();
		$q = $sql->query("SELECT * FROM `comments`");

		if ($q) {
			echo json_encode($q->fetch_all(MYSQLI_ASSOC));
		} else {
			echo json_encode(false);
		}
	}

	public function addComment() {
		$sql = $this->sql();

		$name = $sql->real_escape_string($_POST['name']);
		$email = $sql->real_escape_string($_POST['email']);
		$msg = $sql->real_escape_string($_POST['msg']);

		$q = $sql->query("INSERT INTO `comments` (`name`, `email`, `msg`) VALUES ('$name', '$email', '$msg')");

		if ($q) {
			echo json_encode(['name' => $name, 'email' => $email, 'msg' => $msg]);
		} else {
			echo json_encode(false);
		}
	}

	private function sql() {
		$mysqli = new mysqli('localhost', 'root', 'root', 'hh_comments');
		if($mysqli->connect_errno) {
			throw new \Exception (
                $this->connect_error,
                $this->connect_errno
            );
		} else {
			$mysqli->set_charset('utf8mb4');
		}

		return $mysqli;
	}
}

new Controller($routes[2]);