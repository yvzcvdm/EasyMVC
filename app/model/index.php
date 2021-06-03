<? class index_Model extends db
{
    public function index(array $p = null)
    {
        $p_sel = isset($p["p_sel"]) ? $p["p_sel"] : 10;
        $p_user_id = isset($p["p_user_id"]) ? $p["p_user_id"] : 0;
        $p_user_name = isset($p["p_user_name"]) ? $p["p_user_name"] : "null";
        $p_user_phone = isset($p["p_user_phone"]) ? $p["p_user_phone"] : "null";
        $p_user_email = isset($p["p_user_email"]) ? $p["p_user_email"] : "null";
        $p_user_pass = isset($p["p_pass_new"]) ? sha1($p["p_pass_new"]) : "null";
        return db::proc("CALL `s_user`($p_sel,'$p_user_id','$p_user_name','$p_user_phone','$p_user_email','$p_user_pass');");
    }
}