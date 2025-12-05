<?php class index_Model extends mysql
{
    public function index($p = null)
    {
        $p_sel = isset($p["p_sel"]) ? $p["p_sel"] : 10;
        $p_brand_id = isset($p["p_user_id"]) ? $p["p_user_id"] : 0;
        $p_company_id = isset($p["p_user_name"]) ? $p["p_user_name"] : "null";
        $p_id = isset($p["p_user_phone"]) ? $p["p_user_phone"] : "null";
        $p_title = isset($p["p_user_email"]) ? $p["p_user_email"] : "null";
        $p_description = isset($p["p_pass_new"]) ? sha1($p["p_pass_new"]) : "null";
        $p_logo = isset($p["p_pass_new"]) ? sha1($p["p_pass_new"]) : "null";
        $p_status = isset($p["p_pass_new"]) ? sha1($p["p_pass_new"]) : "null";
        return mysql::query("CALL mp_brand($p_sel,$p_brand_id,$p_company_id,'$p_id','$p_title','$p_description','$p_logo',$p_status);");
    }
}
