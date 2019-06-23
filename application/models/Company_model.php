<?php 
 class Company_model extends CI_Model {
		
		
			 public function __construct() 
			 {
				  // parent::__construct(); 
				   $this->load->database();
			 }

		
		
        public function insert($data) {
			
				$this->db->insert('company', $data);
				$insert_id = $this->db->insert_id();
			    return  $insert_id;
				//echo $this->db->last_query(); die;
			
        }
		
		


        public function update($data,$user_id)
		{
			$this->db->where('user_id', $user_id); 
			//$this->db->update('tbl_user', $data);

			if($this->db->update("company", $data)) 
			{
				return true;
			}else
			{
				return false;
			}
			
		}


		public function delete($id){
		  $this->db->where('user_id', $id);
		  $this->db->delete('company');
		}

	
		
    }
?>