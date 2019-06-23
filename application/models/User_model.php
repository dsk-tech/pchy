<?php 
 class User_model extends CI_Model {
		
		
			 public function __construct() 
			 {
				  // parent::__construct(); 
				   $this->load->database();
			 }

		public function check_login($username, $password, $userType = '')
		{
			$this->db->where('password', md5($password));
			$this->db->where('email', $username);
			$this->db->where('active', '1');
			if ($userType != '') {
				$this->db->where('user_type', $userType);
			}
			//$this->db->or_where('email =', $username); 
			
			$query = $this->db->get('users')->result_array();
			//echo $this->db->last_query(); exit;
			//echo $query->num_rows(); exit;
			if(!empty($query))
			{
				   return $query;
			}else
			{
				return false;
			}
			
		}
		
		
		/*public function check_customer_email($email)
		{
			$query = $this->db->get_where('customers', array('email' => $email));
			//echo $this->db->last_query(); exit;
			//echo $query->num_rows(); exit;
			if($query->num_rows() > 0)
			{
				   return true;
			}else
			{
				return false;
			}
			
		}*/
		
		public function insert($data) {
			
				$this->db->insert('users', $data);
				$insert_id = $this->db->insert_id();
			    return  $insert_id;
				//echo $this->db->last_query(); die;
			
        }
		
		public function insert_visitor($data) {
			
				$this->db->insert('visitors', $data);
				$insert_id = $this->db->insert_id();
			    return  $insert_id;
				//echo $this->db->last_query(); die;
		
        }





        public function update($data, $user_id)
		{
			$this->db->where('user_id', $user_id); 
			//$this->db->update('tbl_user', $data);

			if($this->db->update("users", $data)) 
			{
				return true;
			}else
			{
				return false;
			}
			
		}

		public function delete($id){
		  $this->db->where('user_id', $id);
		  $this->db->delete('users');
		}

		public function update_visitor($user_id,$data)
		{
			$this->db->where('visitor_id', $user_id); 
			//$this->db->update('tbl_user', $data);

			if($this->db->update("visitors", $data)) 
			{
				return true;
			}else
			{
				return false;
			}
			
		}


		
		public function update_profile($user_id,$data)
		{
			$this->db->where('user_id', $user_id); 
			//$this->db->update('tbl_user', $data);

			if($this->db->update("administer", $data)) 
			{
				return true;
			}else
			{
				return false;
			}
			
		}

		
		
		/*public function change_status($table_name, $user_id, $data)
		{
						
			//$this->db->set('status', $status); 
			$this->db->where('id', $user_id); 
			//$this->db->update('tbl_user', $data);

			if($this->db->update($table_name, $data)) 
			{
				return true;
			}else
			{
				return false;
			}
			
		}
		*/
		
    }
?>