<?php

class init extends view
{
	public function slug($str)
	{
		$tr = array('ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'Ç', 'ç');
		$eng = array('s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c');
		$str = str_replace($tr, $eng, $str);
		$str = preg_replace('/&.+?;/', '', $str);
		$str = preg_replace('/[^%a-zA-Z0-9 _-]/', '', $str);
		$str = preg_replace('/\s+/', '-', $str);
		$str = preg_replace('|-+|', '-', $str);
		$str = trim($str, '-');
		$str = strtolower($str);
		return $str;
	}

	public function number_code($length = 4)
	{
		return strrev(substr(rand(1111, 999999), 0, $length));
	}

	public function text_code($length = 4)
	{
		$characters = array();
		$characters = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
		srand((float)microtime() * 100000);
		shuffle($characters);
		$result = '';
		for ($i = 0; $i < $length; $i++) {
			$result .= $characters[$i];
		}
		unset($characters);
		return $result;
	}

	public function daysLeft($gelecekTarih)
	{
		$gelecek = new DateTime($gelecekTarih);
		$bugun = new DateTime(date('d-m-Y'));
		$zamanFarki = $gelecek->diff($bugun);
		$kalanGun = $zamanFarki->format('%a');
		return $kalanGun;
	}

	function timeAgo($tarih)
	{
		$cevrilenzaman = strtotime($tarih);
		$zamanismi = array("Saniye", "Dakika", "Saat", "Gün", "Ay", "Yıl");
		$sure = array("60", "60", "24", "30", "12", "10");
		$simdikizaman = time();
		if ($simdikizaman >= $cevrilenzaman) {
			$fark     = time() - $cevrilenzaman;
			for ($i = 0; $fark >= $sure[$i] && $i < count($sure) - 1; $i++) {
				$fark = $fark / $sure[$i];
			}
			$fark = round($fark);
			return $fark . " " . $zamanismi[$i] . " Önce";
		}
	}

	public function valid_email($e)
	{
		return (bool)preg_match("`^[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$`i", trim($e));
	}

	public function valid_password($password)
	{
		return preg_match('/\S*((?=\S{8,})(?=\S*[A-Z]))\S*/', $password);
	}

	public function clean_phone($phone)
	{
		$phone = preg_replace('/\D+/', '', $phone);
		$filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
		$phone_to_check = str_replace("-", "", $filtered_phone_number);
		return $phone_to_check;
	}

	public function clean_email($email)
	{
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		return $email;
	}

	public function valid_phone($phone)
	{
		$phone = $this->clean_phone($phone);
		return (is_numeric($phone) && strlen($phone) == 10) ? true : false;
	}

	public function valid_tc_number($tc_number)
	{
		$first = null;
		$last = null;
		$all = null;
		$block = array('11111111110', '22222222220', '33333333330', '44444444440', '55555555550', '66666666660', '7777777770', '88888888880', '99999999990');
		if ($tc_number[0] == 0 or !ctype_digit($tc_number) or strlen($tc_number) != 11) {
			return false;
		} else {
			for ($a = 0; $a < 9; $a = $a + 2) {
				$first = $first + $tc_number[$a];
			}
			for ($a = 1; $a < 9; $a = $a + 2) {
				$last = $last + $tc_number[$a];
			}
			for ($a = 0; $a < 10; $a = $a + 1) {
				$all = $all + $tc_number[$a];
			}
			if (($first * 7 - $last) % 10 != $tc_number[9] or $all % 10 != $tc_number[10]) {
				return false;
			} else {
				foreach ($block as $isValue) {
					if ($tc_number == $isValue) {
						return false;
					}
				}
				return true;
			}
		}
	}

	public function send_mail($email, $subject, $content)
	{
		error_reporting(0);
		$mail = new SMTPMailer();
		$mail->addTo($email);
		$mail->Subject($subject);
		$mail->Body('<h3>' . $subject . '</h3><p>' . $content . '</p>');
		return $mail->Send() ? true : false;
	}

	public function message($type, $content)
	{
		if ($type == 'primary')
			$export = '<div class="alert shake alert-icon alert-primary" role="alert"><i class="fe fe-bell mr-2" aria-hidden="true"></i>' . $content . '</div>';
		elseif ($type == 'success')
			$export = '<div class="alert shake alert-icon alert-success" role="alert"><i class="fe fe-check mr-2" aria-hidden="true"></i>' . $content . '</div>';
		elseif ($type == 'danger')
			$export = '<div class="alert shake alert-icon alert-danger" role="alert"><i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i>' . $content . '</div>';
		elseif ($type == 'info')
			$export = '<div class="alert shake alert-info" role="alert"><i class="fe fe-info mr-2" aria-hidden="true"></i>' . $content . '</div>';
		elseif ($type == 'warning')
			$export = '<div class="alert shake alert-warning" role="alert"><i class="fe fe-alert-circle mr-2" aria-hidden="true"></i>' . $content . '</div>';

		return $export;
	}
}
