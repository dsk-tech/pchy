<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
class Api extends CI_Controller {


	public function __construct()
    {
       parent::__construct();
    	
		if(empty($_POST))
		{
			$_POST = json_decode(file_get_contents('php://input'), true);	
		}
		
   }
	
	public function index()
	{
		response('Invalid request', 400);
		
	}


	/**
	 * @method get patient profile
	 * @param $userId
	 * @return json object
	 * @since 2019-06-21
	 * @author khunti haresh <khuntiharesh@gmail.com>
	 **/
	 public function getPatientProfile()
	 {
	 	checkAuthToken();

	 	$this->form_validation->set_rules('userId', 'user Id', 'required');
		
		if ($this->form_validation->run() == FALSE)
        {
        	response("Parameters are missing", 400);
        } else {

        	$this->db->select("u.*,p.*");
        	$this->db->join('patient_profile p', 'u.id = p.user_id', 'left');
        	$this->db->where("u.id", $this->input->post('userId'));
        	$resultData = $this->db->get("users u")->row();
        	if (!empty($resultData)) {
        		response("Detail listed successfully", 200, ['data' => $resultData]);
        	} else {
        		response("Detail not found", 400);
        	}
        }
	 }	

	/**
	 * @method update patient profile
	 * @param $userId, name, gender, dateOfBirth, mobileNo, preferredNo, address, city, state, zip, bio
	 * @return json object
	 * @since 2019-06-21
	 * @author khunti haresh <khuntiharesh@gmail.com>
	 **/
	 public function UpdatePatientProfile()
	 {
	 	checkAuthToken();

	 	$this->form_validation->set_rules('userId', 'user Id', 'required');
	 	$this->form_validation->set_rules('name', 'Name', 'required');
	 	$this->form_validation->set_rules('gender', 'Gender', 'required');
	 	$this->form_validation->set_rules('dateOfBirth', 'Date Of Birth', 'required');
	 	$this->form_validation->set_rules('mobileNo', 'Mobile No.', 'required');
	 	$this->form_validation->set_rules('preferredNo', 'Preferred No.', 'required');
	 	$this->form_validation->set_rules('address', 'Address', 'required');
	 	$this->form_validation->set_rules('city', 'City', 'required');
	 	$this->form_validation->set_rules('state', 'State', 'required');
	 	$this->form_validation->set_rules('zip', 'Zip', 'required');
	 	//$this->form_validation->set_rules('bio', 'Bio', 'required');

		
		if ($this->form_validation->run() == FALSE)
        {
        	response(validation_errors(), 400);
        } else {
        	$profileData = [
        		"date_of_birth" => $this->input->post('dateOfBirth'),
        		"address1"      => $this->input->post('address'),
        		"city"          => $this->input->post('city'),
        		"state"         => $this->input->post('state'),
        		"pin_code"      => $this->input->post('zip'),
        		"mobile_no"      => $this->input->post('mobileNo'),
			"gender"    => $this->input->post('gender')
        	];

        	$userData = [
        		"name" 		=> $this->input->post('name'),
        		"gender"    => $this->input->post('gender'),
        		"phone"     => $this->input->post('preferredNo')
        	];

        	$imageUpload = $this->upload_image('image');
        	if ($imageUpload['status']) {
        		$profileData["profile_image"] = $imageUpload['data'];
        	} elseif (!$imageUpload['status']) {
        		response($imageUpload['data'], 400);
        	}

        	$this->db->where("user_id", $this->input->post('userId'));
        	$profileUpdate = $this->db->update('patient_profile', $profileData);

        	$this->db->where("id", $this->input->post('userId'));
        	$userUpdate = $this->db->update('users', $userData);
        	

        	if ($profileUpdate && $userUpdate) {
        		response("Profile updated successfully", 200);
        	} else {
        		response("Profile can't be updated. Please try again", 400);
        	}
        }
	 }

	 /**
	 * @method Get Inbox
	 * @param $userId
	 * @return json object
	 * @since 2019-06-22
	 * @author khunti haresh <khuntiharesh@gmail.com>
	 **/
	public function getInbox()
	{
	 	checkAuthToken();

	 	$this->form_validation->set_rules('userId', 'user Id', 'required');
		
		if ($this->form_validation->run() == FALSE)
        {
        	response("Parameters are missing", 400);
        } else {

        	$this->db->select("q.*,pp.profile_image_url,u.name,u.first_name,u.last_name,u.middle_name");
        	$this->db->join('users u', 'u.id = q.psych_id', 'left');
        	$this->db->join('psychiatrist_details pp', 'pp.users_id = q.psych_id', 'left');
        	$this->db->join('patient_profile p', 'u.id = p.user_id', 'left');
        	$this->db->where("q.patient_id", $this->input->post('userId'));
        	$this->db->group_by('q.psych_id'); 
 			$this->db->order_by('q.id', 'desc'); 
        	$resultData = $this->db->get("question_and_answer q")->result_array();
        	if (!empty($resultData)) {
        		response("Message listed successfully", 200, ['data' => $resultData]);
        	} else {
        		response("Detail not found", 400);
        	}
        }
	}	


	/**
	 * @method Create Message
	 * @param $userId, $messageTo, $subject, $message
	 * @return json object
	 * @since 2019-06-22
	 * @author khunti haresh <khuntiharesh@gmail.com>
	 **/
	public function getCreateMessage()
	{
	 	checkAuthToken();

	 	$this->form_validation->set_rules('userId', 'user Id', 'required');
	 	$this->form_validation->set_rules('messageTo', 'To', 'required');
	 	$this->form_validation->set_rules('subject', 'Subject', 'required');
	 	$this->form_validation->set_rules('message', 'Message', 'required');
		
		if ($this->form_validation->run() == FALSE)
        {
        	response(validation_errors(), 400);
        } else {

        	$data = [
        		"patient_id"          => $this->input->post('userId'),
        		"psych_id"   	      => $this->input->post('messageTo'),
        		"question_title"      => $this->input->post('subject'),
        		"question_details"    => $this->input->post('message'),
        		"question_created_on" => date("Y-m-d"),
        		"status" 			  => 'pending',
        		"created_at"		  => date("Y-m-d H:i:s"),
        	];
        	$this->db->insert('question_and_answer', $data);
			$insert_id = $this->db->insert_id();

        	if (!empty($insert_id)) {
        		response("Message successfully created", 200);
        	} else {
        		response("Message can't be created. Please try again", 400);
        	}
        }
	}	

	/**
	 * @method Get Psychiatrist
	 * @return json object
	 * @since 2019-06-22
	 * @author khunti haresh <khuntiharesh@gmail.com>
	 **/
	public function getPsychiatrist()
	{
	 	checkAuthToken();
	 	$this->db->select('name,first_name, last_name, middle_name, id');
 		$this->db->where("user_role_id", '2');
 		$this->db->where("active", '1');
    	$resultData = $this->db->get("users")->result_array();
    	if (!empty($resultData)) {
    		response("Psychiatrist listed successfully", 200, ['data' => $resultData]);
    	} else {
    		response("Detail not found", 400);
    	}
        
	}	



	//common image upload function
	function upload_image($file_name, $required = false)
	{
		$this->load->library('upload');
		$this->upload->initialize(img_file_cinfig());

		if(!$this->upload->do_upload($file_name,FALSE))
		{
		    if($_FILES[$file_name]['error'] != 4 || $required)
		    {
		        return ["status" => false, "data" => $file_name." ".$this->upload->display_errors()]; 
			}

		} else {
		    $image = $this->upload->data();
		    return ["status" => true, "data" => $image["file_name"]]; 
		}
		return '';
	}

	/*public function getDoctoProfile()
	{
		$this->form_validation->set_rules('user_id', 'user Id', 'required');
		
		if ($this->form_validation->run() == FALSE)
        {
        	response("Parameters are missing", 400);
        } else {
        	$qry = "select u.*, (select count(*) from patient_profile pf where pf.user_id = u.id) as patient_count  from users u where u.id = '".$this->input->post('user_id')."' and u.active = '1' and u.is_verified = '1'";
			$rtnData = $this->db->query($qry)->result_array();
			if (!empty($rtnData)) {
				response('Data listed successful', 200, ['data' => $rtnData]);
			} else {
				response('Data not fount', 400);
			}
			
		}

		
	}*/

	

	
}
