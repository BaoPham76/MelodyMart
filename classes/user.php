<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/database.php');
include_once($filepath . '/../lib/PHPMailer.php');
include_once($filepath . '/../lib/SMTP.php');
include_once($filepath . '/../lib/session.php');
use PHPMailer\PHPMailer\PHPMailer;
?>

<?php
/**
 * 
 */
class user
{
	private $db;
	public function __construct()
	{
		$this->db = new Database();
	}


	public function login($email, $password)
{
    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password' LIMIT 1 ";
    $result = $this->db->select($query);
    if ($result) {
        $value = $result->fetch_assoc();
        if ($value['isConfirmed'] == 1) {
            Session::set('user', true);
            Session::set('userId', $value['id']);
            Session::set('role_id', $value['role_id']);
            header("Location: index.php");
            exit();
        } else {
            $userId = $value['id'];
            // Chuyển hướng người dùng đến trang xác minh và truyền userId
            header("Location: confirm.php?userId=$userId");
            exit();
        }
    } else {
        $alert = "Tên đăng nhập hoặc mật khẩu không đúng!";
        return $alert;
    }
}

	public function insert($data)
	{
		$fullName = $data['fullName'];
		$email = $data['email'];
		$dob = $data['dob'];
		$address = $data['address'];
		$password = md5($data['password']);


		$check_email = "SELECT * FROM users WHERE email='$email' LIMIT 1";
		$result_check = $this->db->select($check_email);
		if ($result_check) {
			return 'Email đã tồn tại!';
		} else {
			// Genarate captcha
			$captcha = rand(10000, 99999);

			$query = "INSERT INTO users VALUES (NULL,'$email','$fullName','$dob','$password',2,1,'$address',0,'" . $captcha . "') ";
			$result = $this->db->insert($query);
			if ($result) {
				// Send email
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->Mailer = "smtp";

				$mail->SMTPDebug  = 0;
				$mail->SMTPAuth   = TRUE;
				$mail->SMTPSecure = "tls";
				$mail->Port       = 587;
				$mail->Host       = "smtp.gmail.com";
				$mail->Username   = "phamngocbao2104@gmail.com";
				$mail->Password   = "ssxp cxrt qrnw pcrj";

				$mail->IsHTML(true);
				$mail->CharSet = 'UTF-8';
				$mail->AddAddress($email, "recipient-name");
				$mail->SetFrom("phamngocbao2104@gmail.com", "Instrument Store");
				$mail->Subject = "Xác nhận email tài khoản - Instruments Store";
				$mail->Body = "<h3>Cảm ơn bạn đã đăng ký tài khoản tại website InstrumentStore</h3></br>Đây là mã xác minh tài khoản của bạn: " . $captcha . "";

				$mail->Send();

				return true;
			} else {
				return false;
			}
		}
	}

	public function get()
	{
		$userId = Session::get('userId');
		$query = "SELECT * FROM users WHERE id = '$userId' LIMIT 1";
		$mysqli_result = $this->db->select($query);
		if ($mysqli_result) {
			$result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];
			return $result;
		}
		return false;
	}

	public function getLastUserId()
	{
			$query = "SELECT id FROM users ORDER BY id DESC LIMIT 1";
			$result = $this->db->select($query); // Giả sử $this->db->select() là phương thức để thực hiện truy vấn SQL
	
			if ($result && mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_assoc($result);
					return $row['id'];
			}
			
			return false;
	}
	

	public function confirm($userId, $captcha)
	{
		$query = "SELECT * FROM users WHERE id = '$userId' AND captcha = '$captcha' LIMIT 1";
		$mysqli_result = $this->db->select($query);
		if ($mysqli_result) {
			// Update comfirmed
			$sql = "UPDATE users SET isConfirmed = 1 WHERE id = $userId";
			$update = $this->db->update($sql);
			if ($update) {
				return true;
			}
		}
		return 'Mã xác minh không đúng!';
	}
}
?>