<?php class upload
{
    public function upload_img($pdir)
    {
        $dir = ROOT . $pdir;
        $result = array();
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if ($_FILES) {
            foreach ($_FILES as $k => $files) {
                if (is_array($files['name'])) {
                    //Multifiles upload
                    $countfiles = count($files['name']);
                    for ($i = 0; $i < $countfiles; $i++) {

                        $tmp_file = $files['tmp_name'][$i];
                        $mime_type = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                        $file_type = $files['type'][$i];
                        $file_size = $files['size'][$i];
                        $filename = basename($files['name'][$i], '.' . $mime_type);
                        $send_path = $dir . basename($filename . '-' . time() . $i . '.' . $mime_type);

                        if (in_array($file_type, $allowedMimeTypes) && $file_size < 1000000) {
                            $uploading = move_uploaded_file($tmp_file, $send_path);
                            if ($uploading) {
                                $result[$k][$i] = $pdir . basename($filename . '-' . time() . $i . '.' . $mime_type);
                            }
                        }
                    }
                } else {
                    //Simple file upload
                    $tmp_file = $files['tmp_name'];
                    $mime_type = pathinfo($files['name'], PATHINFO_EXTENSION);
                    $file_type = $files['type'];
                    $file_size = $files['size'];
                    $filename = basename($files['name'], '.' . $mime_type);
                    $send_path = $dir . basename($filename . '-' . time() . '.' . $mime_type);

                    if (in_array($file_type, $allowedMimeTypes) && $file_size < 1000000) {
                        $uploading = move_uploaded_file($tmp_file, $send_path);
                        if ($uploading) {
                            $result[$k] = $pdir . basename($filename . '-' . time() . '.' . $mime_type);
                        }
                    }
                }
            }
        }
        return $result;
    }
}
