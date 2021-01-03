<?php class user_Model
{
    
    private $db;

    public function __construct()
    {
        $this->db = new db();
    }

    public function userGet()
    {
        return $this->db->proc("CALL `wb_user`();");
    }

}