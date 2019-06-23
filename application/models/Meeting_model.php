<?php 
 class Meeting_model extends CI_Model {
		
		
			 public function __construct() 
			 {
				  // parent::__construct(); 
				   $this->load->database();
				   $this->load->library('mailer');
			       $this->load->library('mailTemplate');
			       $this->load->library('sms');
			 }

		
		
        public function insert($data) {
				
				
				$this->db->insert('meetings', $data);
				$insert_id = $this->db->insert_id();
			    return  $insert_id;
				//echo $this->db->last_query(); die;
			
        }
		
		


        public function update($data,$user_id)
		{
			$this->db->where('user_id', $user_id); 
			//$this->db->update('tbl_user', $data);

			if($this->db->update("meetings", $data)) 
			{
				return true;
			}else
			{
				return false;
			}
			
		}

		public function get_meeting_date()
		{
			$user_id = $this->session->userdata('user_id');

			$qry = "select m.* from meeting_data m where m.chapter_id = '".$user_id."' and m.meeting_data_id = (select max(m2.meeting_data_id) from meeting_data m2 where m2.chapter_id = '".$user_id."')";

			$query = $this->db->query($qry);
			$metting_dt_arr = $query->result_array();
 
			$this->db->where('user_id', $user_id); 
			$data = $this->db->get("meetings")->result_array();

			if(!empty($metting_dt_arr))
			{
				$meeting_start_date = $metting_dt_arr[0]["to_date"];
				if($meeting_start_date <= date("Y-m-d"))
				{

				}else
				{
					$meeting_start_date = $data[0]["metting_date"];
				}
			}else
			{
				$meeting_start_date = $data[0]["metting_date"];
			}
			
			$meeting_end_date = date('Y-m-d', strtotime($meeting_start_date. ' + '.$data[0]["metting_day"].' days'));

			$rtn_dt["no_of_day"] = $data[0]["metting_day"];
			$rtn_dt["start_date"] = $meeting_start_date;
			$rtn_dt["end_date"] = $meeting_end_date;

			return $rtn_dt;
		}

 
		public function delete($id){
		  $this->db->where('user_id', $id);
		  $this->db->delete('meetings');
		}

		public function insert_schedule($data) {
				
				
				$this->db->insert('meeting_schedules', $data);
				$insert_id = $this->db->insert_id();
			    return  $insert_id;
				//echo $this->db->last_query(); die;
			
        }

        public function update_schedule($data,$schedule_id)
		{
			$this->db->where('schedule_id', $schedule_id); 
			//$this->db->update('tbl_user', $data);

			if($this->db->update("meeting_schedules", $data)) 
			{
				return true;
			}else
			{
				return false;
			}
			
		}

	  public function add_opportunity($user_id, $chapter_id,$type,$to_chapter_id = '')
	  {
	        $c_date = date("Y-m-d");
	        //$user_id = $this->session->userdata('user_id');

	        $this->db->where("chapter_id",$chapter_id);
	        $this->db->where("start_date <= ",$c_date);
	        $this->db->where("end_date >= ",$c_date);
	        $schedule_date = $this->db->get("meeting_schedules")->result_array();

	        if(!empty($schedule_date))
	        {
	         
	          $to_user = $this->input->post('member');
	          $opportunity_type = $this->input->post('opportunity_type');
	          $connect_to = $this->input->post('connect_to');
	          $phone_no = $this->input->post('phone_no');
	          $description = $this->input->post('description');
	          $amount = $this->input->post('amount');

	          $this->db->where("member_id",$user_id);
	          $this->db->where("from_date",$schedule_date[0]["start_date"]);
	          $this->db->where("to_date",$schedule_date[0]["end_date"]);
	          $from_usr_mtn_dt = $this->db->get("meeting_data")->result_array();

	          $this->db->where("member_id",$to_user);
	          $this->db->where("from_date <= ",$c_date);
	          $this->db->where("to_date >= ",$c_date);
	          $to_usr_mtn_dt = $this->db->get("meeting_data")->result_array();


	          $data_arr["chapter_id"] = $chapter_id;
	          $data_arr["member_id"] = $user_id;
	          
	          $data_arr["from_date"] = $schedule_date[0]["start_date"];
	          $data_arr["to_date"] = $schedule_date[0]["end_date"];


	           $to_usr_data_arr["chapter_id"] = ($to_chapter_id != '') ? $to_chapter_id : $chapter_id;
	           $to_usr_data_arr["member_id"] = $to_user;
	           $to_usr_data_arr["from_date"] = $schedule_date[0]["start_date"];
	           $to_usr_data_arr["to_date"] = $schedule_date[0]["end_date"];

	          
	          if($type == 1)
	          {
	            

	            if($opportunity_type == 1){
	              if(@$from_usr_mtn_dt[0]["ongi"] != '')
	              {
	                 $data_arr["ongi"] = $from_usr_mtn_dt[0]["ongi"] + 1;
	              }else
	              {
	                $data_arr["ongi"] = 1;
	              }

	              if(@$to_usr_mtn_dt[0]["onri"] != '')
	              {
	                 $to_usr_data_arr["onri"] = $to_usr_mtn_dt[0]["onri"] + 1;
	              }else
	              {
	                $to_usr_data_arr["onri"] = 1;
	              }

	            }else
	            {
	               if(@$from_usr_mtn_dt[0]["ongo"] != '')
	                {
	                   $data_arr["ongo"] = $from_usr_mtn_dt[0]["ongo"] + 1;
	                }else
	                {
	                  $data_arr["ongo"] = 1;
	                }

	                if(@$to_usr_mtn_dt[0]["onro"] != '')
	                  {
	                     $to_usr_data_arr["onro"] = $to_usr_mtn_dt[0]["onro"] + 1;
	                  }else
	                  {
	                    $to_usr_data_arr["onro"] = 1;
	                  }
	            }


	            if(!empty($from_usr_mtn_dt))
	            {
	              $data_arr["description"] = $from_usr_mtn_dt[0]["description"].",".$description; 
	              $data_arr["phone_no"] = $from_usr_mtn_dt[0]["phone_no"].",".$phone_no;
	              $data_arr["connect_to"] = $from_usr_mtn_dt[0]["connect_to"].",".$connect_to;
	              $data_arr["member_to"] = $from_usr_mtn_dt[0]["member_to"].",".$to_user;

	            }else
	            {
	              $data_arr["description"] = $description;
	              $data_arr["phone_no"] = $phone_no;
	              $data_arr["connect_to"] = $connect_to;
	              $data_arr["member_to"] = $to_user;
	            }


	            
	            $this->db->where("user_id",$user_id);
	            $from_user_detail = $this->db->get("users")->result_array();
	            //echo $this->db->last_query(); exit;
	            $from_user_detail = $from_user_detail[0];

	            $from_name = $from_user_detail["first_name"];

	            $this->db->where("user_id",$to_user);
	            $to_user_detail = $this->db->get("users")->result_array();
	            $to_user_detail = $to_user_detail[0];

	            $to_name = $to_user_detail["first_name"];
	            $to_mail = $to_user_detail["email"];
	            $contact_no = $to_user_detail["contact_no"];

	            $msg = 'Dear, '.$to_name.' Thank you for your interest'. $from_name. ' shared a Opportunity Note';
	            $this->sms->send_sms($contact_no,$msg);

	              $this->mailer->send(array('subject' => 'Thank You for Interested', 'body' => $this->mailtemplate->opportunity_note($from_name,$to_name), 'to' => $to_mail));

	           
	          }elseif($type == 2)
	          {
	              if(@$from_usr_mtn_dt[0]["tng"] != '')
	              {
	                 $data_arr["tng"] = $from_usr_mtn_dt[0]["tng"] + $amount;
	              }else
	              {
	                $data_arr["tng"] = $amount;
	              }

	              if(@$to_usr_mtn_dt[0]["tnr"] != '')
	                {
	                   $to_usr_data_arr["tnr"] = $to_usr_mtn_dt[0]["tnr"] + $amount;
	                }else
	                {
	                  $to_usr_data_arr["tnr"] = $amount;
	                }
	          }elseif($type == 3)
	          {
	              if(@$from_usr_mtn_dt[0]["kyc"] != '')
	              {
	                 $data_arr["kyc"] = $from_usr_mtn_dt[0]["kyc"] + 1;
	              }else
	              {
	                $data_arr["kyc"] = 1;
	              }

	              if(@$to_usr_mtn_dt[0]["kyc"] != '')
	                {
	                   $to_usr_data_arr["kyc"] = $to_usr_mtn_dt[0]["kyc"] + 1;
	                }else
	                {
	                  $to_usr_data_arr["kyc"] = 1;
	                }
	          }

	          //print_r($data_arr); exit();

	          $check_rtn_dt = '';
	          if(!empty($from_usr_mtn_dt))
	          {
	            $this->db->where("meeting_data_id",$from_usr_mtn_dt[0]["meeting_data_id"]);
	            $check_rtn_dt = $this->db->update("meeting_data",$data_arr);
	          }else
	          {
	            $check_rtn_dt = $this->db->insert("meeting_data",$data_arr);
	          }

	           $check_rtn_dt2 = '';

	           if(!empty($to_usr_mtn_dt))
	            {
	              $this->db->where("meeting_data_id",$to_usr_mtn_dt[0]["meeting_data_id"]);
	              $check_rtn_dt2 = $this->db->update("meeting_data",$to_usr_data_arr);
	            }else
	            {
	              $check_rtn_dt2 = $this->db->insert("meeting_data",$to_usr_data_arr);
	            }



	            if($check_rtn_dt && $check_rtn_dt2)
	            {
	              return 1;
	            }else
	            {
	              return 2;
	            }


	        }else
	        {
	          return 3;
	        }
	        
	  }


	
		
    }
?>