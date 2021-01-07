<?php 
class init
{

    public function slug($str)
    {
        $tr = array('ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'Ç', 'ç');
        $eng = array('s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c');
        $str = str_replace($tr, $eng, $str);
        $str = preg_replace('/&.+?;/', '', $str);
        $str = preg_replace('/[^%a-zA-Z0-9 _-]/', '', $str);
        // $str = preg_replace('/\s+/', '-', $str);
        $str = preg_replace('|-+|', '-', $str);
        // $str = trim($str, '-');
        // $str = strtolower($str);
        return $str;
    }
}
