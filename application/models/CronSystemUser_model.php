<?php
class CronSystemUser_model extends CI_Model {
	
	public function CronSystemUser_model(){
		parent::__construct();
		$this->CI = get_instance();

		$this->db_api 	= $this->load->database('api', true);
	}

	public function getSystemUser($today){
		$this->db_api->select('system_user.user_id, system_user.member_id');
		$this->db_api->from('system_user');
		$this->db_api->where('system_user.expired_on <= ', $today);
		$this->db_api->where('system_user.member_user_status', 0);
		$result = $this->db_api->get()->result_array();
		return $result;
	}

	public function deleteSystemUser($data){
		$this->db_api->where('system_user.user_id', $data['user_id']);
		$this->db_api->where('system_user.member_id', $data['member_id']);
		if($this->db_api->delete('system_user')){
			return true;
		} else {
			return false;
		}
	}

	public function updateCoreMember($data){
		$this->db->where('core_member.member_id', $data['member_id']);
		if($this->db->update('core_member', $data)){
			return true;
		} else {
			return false;
		}
	}
}