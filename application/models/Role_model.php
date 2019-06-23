<?php 
 class Role_model extends CI_Model {
		
		
			 public function __construct() 
			 {
				  // parent::__construct(); 
				   $this->load->database();
			 }

		
        public function update($data, $role_id)
		{
			$this->db->where('role_id', $role_id); 
			
			if($this->db->update("user_role", $data)) 
			{
				return true;
			}else
			{
				return false;
			}
			
		}

		
		
    }
?>