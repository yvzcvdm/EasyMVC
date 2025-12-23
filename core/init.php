<?php class init
{
    
	public static function slug($str)
	{
		$str = $str ?? '';
		$tr = array('ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'Ç', 'ç');
		$eng = array('s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c');
		$str = str_replace($tr, $eng, $str);
		$str = preg_replace('/&.+?;/', '', $str);
		$str = preg_replace('/[^%a-zA-Z0-9 _-]/', '', $str);
		$str = preg_replace('/\s+/', '-', $str);
		$str = preg_replace('|-+|', '-', $str);
		$str = trim((string)$str, '-');
		$str = strtolower($str);
		return $str;
	}

    function text_short($text, $chars_limit)
	{
		if (strlen($text) > $chars_limit) {
			$new_text = substr($text, 0, $chars_limit);
				$new_text = trim((string)$new_text);
			return $new_text . "...";
		} else {
			return $text;
		}
	}

	public static function array_clear($array)
	{
		array_walk_recursive($array, function (&$item) {
			$item = htmlspecialchars(addslashes(stripslashes(trim((string)$item))));
		});
		return $array;
	}

	public static function random_number_code($length = 4)
	{
		return strrev(substr(rand(1111, 999999), 0, $length));
	}

	public static function random_text_code($length = 4)
	{
		$characters = array();
		$characters = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
		srand((int)((float)microtime() * 100000));
		shuffle($characters);
		$result = '';
		for ($i = 0; $i < $length; $i++) {
			$result .= $characters[$i];
		}
		unset($characters);
		return $result;
	}

	public static function days_left($date)
	{
		$coming = new DateTime($date);
		$now = new DateTime(date('d-m-Y'));
		$difference = $coming->diff($now);
		$remaining_day = $difference->format('%a');
		return $remaining_day;
	}

	function time_left($tarih)
	{
		$convertime = strtotime($tarih);
		$time_name = array("Saniye", "Dakika", "Saat", "Gün", "Ay", "Yıl");
		$duration = array("60", "60", "24", "30", "12", "10");
		$now_time = time();
		if ($now_time >= $convertime) {
			$difference     = time() - $convertime;
			for ($i = 0; $difference >= $duration[$i] && $i < count($duration) - 1; $i++) {
				$difference = $difference / $duration[$i];
			}
			$difference = round($difference);
			return $difference . " " . $time_name[$i];
		}
	}

	public function valid_email($e)
	{
		return (bool)preg_match("`^[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$`i", trim((string)$e));
	}

	public function valid_password($password)
	{
		return preg_match('/\S*((?=\S{8,})(?=\S*[A-Z]))\S*/', $password);
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
    
    public function clean_phone($phone)
	{
		$phone = preg_replace('/\D+/', '', $phone);
		$filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
		$phone_to_check = str_replace("-", "", $filtered_phone_number);
		return $phone_to_check;
	}

	public function clean_mail($mail)
	{
		$mail = filter_var($mail, FILTER_SANITIZE_EMAIL);
		return $mail;
	}

	public function send_mail($email, $subject, $content)
	{
		$mail = new mail();
        $mail->from("admin@weebim.com", "Weebim");
		$mail->addTo($email);
		$mail->Subject($subject);
		$mail->Body($content);
		return $mail->Send() ? true : false;
	}

	public static function translate($key)
	{
		static $lang_key = $_COOKIE['lang'] ?? 'de';
		static $cache = null;
		$requested = (string)$lang_key;
		$key = (string)$key; 

		// İlk çağırmada tüm JSON dosyalarını yükle ve dil adına göre cache'le
		if ($cache === null) {
			$cache = [];
			// Eğer ana dizin `ROOT` olarak tanımlandıysa onu kullan, değilse __DIR__ üzerinden hesapla
			$dir = realpath(rtrim(ROOT, '/\\') . '/public/local');
			if ($dir && is_dir($dir)) {
				foreach (glob($dir . '/*.json') as $file) {
					$name = pathinfo($file, PATHINFO_FILENAME);
					$content = @file_get_contents($file);
					$json = @json_decode($content, true);
					if (is_array($json)) {
						$cache[$name] = $json;
					}
				}
			}
		}

		// Eğer hiç dil dosyası yoksa çık
		if (empty($cache)) {
			return '';
		}

		// Normalize talep edilen dil (eğer verildiyse)
		$requested = strtolower(preg_replace('/[^a-z]/', '', (string)$requested));

		// Karar verme sırası:
		// 1) Eğer fonksiyona açıkça geçilmiş ve mevcutsa onu kullan
		// 2) Eğer cookie'de `lang` varsa ve destekleniyorsa onu kullan
		// 3) Tarayıcı `Accept-Language` başlığına göre ilk uygun dili kullan
		// 4) Cache içindeki ilk dili kullan (fallback)

		$lang = '';

		if ($requested !== '' && isset($cache[$requested])) {
			$lang = $requested;
		}

		if ($lang === '') {
			$cookieLang = '';
			if (!empty($_COOKIE['lang'])) {
				$cookieLang = strtolower(preg_replace('/[^a-z]/', '', (string)$_COOKIE['lang']));
			}
			if ($cookieLang !== '' && isset($cache[$cookieLang])) {
				$lang = $cookieLang;
			}
		}

		if ($lang === '') {
			$accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
			if ($accept !== '') {
				// Örnek: "tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7"
				$parts = preg_split('/[,;]/', $accept);
				foreach ($parts as $p) {
					$p = trim($p);
					if ($p === '') continue;
					// alttaki pattern ile önceki kısmı al (örn tr-TR -> tr)
					$code = strtolower(explode('-', $p)[0]);
					$code = preg_replace('/[^a-z]/', '', $code);
					if ($code !== '' && isset($cache[$code])) {
						$lang = $code;
						break;
					}
				}
			}
		}

		if ($lang === '') {
			// fallback: cache içindeki ilk anahtar
			$keys = array_keys($cache);
			$lang = $keys[0];
		}

		$node = $cache[$lang] ?? [];

		if ($key === '') {
			return is_array($node) ? '' : (string)$node;
		}

		$parts = explode('.', $key);
		foreach ($parts as $part) {
			if (is_array($node) && array_key_exists($part, $node)) {
				$node = $node[$part];
			} else {
				return '';
			}
		}

		return is_array($node) ? '' : (string)$node;
	}

}
