<?php
class Usermodel extends CI_Model {
	public function __construct()
	{
		$this->load->database();
	}


	public function get_id($name, $table = "users")
	{
		$query = $this->db->get_where($table, array("username" => $name));
		return $query->row()->id;
	}


	public function get_name($id, $table = "users")
	{
		$query = $this->db->get_where($table, array("id" => $id));
		if($query->num_rows() == 1) {
			return $query->row()->username;
		}
		return false;
	}

	public function get_books()
	{
		$query = $this->db->get_where("notebooks", array("id" => $this->session->userdata("id")));
		
		return array(
			"result" => $query->result_array(),
			"first_row" => $query->first_row(),
			"num_rows" => $query->num_rows()
		);
	}

	public function get_notes($booknum)
	{
		$query = $this->db->get_where("reminder", array(
			"id" => $this->session->userdata("id"),
			"booknum" => $booknum,
			"recycled" => 0
		));
		return $query->result_array();
	}

	public function get_tags($booknum)
	{
		$query = $this->db->get_where("tags", array(
			"id" => $this->session->userdata("id"),
			"booknum" => $booknum,
			"recycled" => 0
		));
		return $query->result_array();
	}

	public function get_alltags()
	{
		$query = $this->db->get_where("tags", array(
			"id" => $this->session->userdata("id"),
			"recycled" => 0
		));
		return $query->result_array();
    }


	public function check_login($name = FALSE, $table = "users")
{
	$query = $this->db->get_where($table, array('username' => $name));
	if ($query->num_rows() == 1) {
		return true;
	}
	return FALSE;
}


	public function check_pass($pass = FALSE, $name, $table = "users")
{
	$query_name = $this->db->get_where($table, array("username" => $name));
	if ($query_name->num_rows() == 1) {
		$row = $query_name->row(); 
		if(crypt($pass, $row->hash) == $row->hash) {
			return true;
		}
	}
	
	return FALSE;
}


	public function name_exist($name = FALSE)
{
	$query = $this->db->get_where('users', array('username' => $name));
	if ($query->num_rows() == 1) {
		return TRUE;
	}
	return FALSE;
}


	public function email_exist($email = FALSE)
{
	$query = $this->db->get_where('users', array('email' => $email));
	if ($query->num_rows() == 1) {
		return TRUE;
	}
	return FALSE;
}


	public function register_db()
	{
		$data = array(
			'name' => $this->input->post('name'),
			'email' => $this->input->post('email'),
			'hash' => crypt($this->input->post('pass'))
		);
	
		return $this->db->insert('users', $data);
	}

	public function url_contains($value)
	{
		return strpos($_SERVER["PHP_SELF"], $value) !== false;
	}

	public function set_pass($email, $pass)
	{
		$this->db->where('email', $email);
        $this->db->update('users', array(
           'hash' => crypt($pass)
        ));
	}

	public function mailgun($to, $subject, $msg)
	{
        date_default_timezone_set(TIMEZONE);
        $mg_version = 'api.mailgun.net/v2/';
        $mg_domain = MGDOMAIN;
        $mg_message_url = "https://".$mg_version.$mg_domain."/messages";

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => false,
            CURLOPT_HEADER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERPWD => 'api:' . MGAPIKEY,
            CURLOPT_POST => true,
            CURLOPT_URL => $mg_message_url,
            CURLOPT_POSTFIELDS => array(
                'from'      => 'Memento Inc. <no-reply@memento.shazvi.com>',
                'to'        => $to,
                'subject'   => $subject,
                'html'    => $msg
            )
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($result,TRUE);
        print_r($res);
	}

    public function set_note($noteid, $note)
    {
        $this->db->where( array('number' => $noteid, "id" => $this->session->userdata("id")));
        return $this->db->update('reminder', array(
            'note' => $note
        ));
    }

    public function set_conf($noteid, $conf)
    {
        $this->db->where( array('number' => $noteid, "id" => $this->session->userdata("id")));
        return $this->db->update('reminder', array(
            'email' => $conf
        ));
    }

    public function set_priority($noteid, $priority)
    {
        $this->db->where( array('number' => $noteid, "id" => $this->session->userdata("id")));
        return $this->db->update('reminder', array(
            'priority' => $priority
        ));
    }

    public function set_duedate($noteid, $duedate)
    {
        $this->db->where( array('number' => $noteid, "id" => $this->session->userdata("id")));
        return $this->db->update('reminder', array(
            'duedate' => $duedate.":00"
        ));
    }

    public function set_tags($noteid, $tags, $booknum)
    {
        $this->db->delete('tags', array(
            'id' => $this->session->userdata("id"),
            "number" => $noteid
        ));

        $tagsarr = explode(',', $tags);
        $i = 1;

        foreach ($tagsarr as $tag) {
            if ($tag !== "") {
                $this->db->insert('tags', array(
                    'tags' => trim($tag) ,
                    'number' => $noteid,
                    'id' => $this->session->userdata("id"),
                    "ordery" => $i,
                    "booknum" => $booknum,
                    "recycled" => 0
                ));
                ++$i;
            }
        }
    }
}