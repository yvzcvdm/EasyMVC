<?php class login_Model
{
    
    private $db;

    public function __construct()
    {
        $this->db = new db();
    }

    public function loginGet()
    {
        return $this->db->proc("CALL `wb_user`();");
    }

}