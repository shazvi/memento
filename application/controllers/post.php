<?php
class Post extends CI_Controller {
    public function __construct ( ) {
        parent::__construct ( );
        $this->load->model('usermodel');

        if (!$this->usermodel->url_contains("login") && !$this->usermodel->url_contains("create") && !$this->usermodel->url_contains("fail")&& !$this->usermodel->url_contains("about")&& !$this->usermodel->url_contains("forgot")&& !$this->usermodel->url_contains("success"))
        {
            $logged_in = $this->session->userdata('id');
            if($logged_in === FALSE) {
                header('HTTP 400 Bad Request', true, 400);
                echo "You need to login first.";
            }
        }
    }

    public function editnote()
    {
        $pk = $this->input->post('pk');
        $value = $this->input->post('value');

        if(!empty($value)) {
            if(!$this->usermodel->set_note($pk, $value)){
                header('HTTP 500 Server Error', true, 500);
                echo "An unexpected error occured.";
            }
        } else {
            header('HTTP 400 Bad Request', true, 400);
            echo "This field is required!";
        }
    }

    public function editconf(){
        $pk = $this->input->post('pk');
        $value = $this->input->post('value');

        if(!$this->usermodel->set_conf($pk, $value)){
            header('HTTP 500 Server Error', true, 500);
            echo "An unexpected error occured.";
        }
    }

    public function editduedate(){
        $pk = $this->input->post('pk');
        $value = $this->input->post('value');

        if(!$this->usermodel->set_duedate($pk, $value)){
            header('HTTP 500 Server Error', true, 500);
            echo "An unexpected error occured.";
        } else {
            echo json_encode(array(
                "value" => $value
            ));
        }
    }

    public function editpriority(){
        $pk = $this->input->post('pk');
        $value = $this->input->post('value');

        if(!$this->usermodel->set_priority($pk, $value)){
            header('HTTP 500 Server Error', true, 500);
            echo "An unexpected error occured.";
        } else {
            echo json_encode(array(
                'value' => ucfirst($value),
                "label" => ($value == "high")?"important":(($value == "medium")?"warning":"success"),
                "hidden" => ($value == "high")?"a":(($value == "medium")?"b":"c"),
            ));
        }
    }

    public function edittags() {
        $noteid = $this->input->post('noteid');
        $tags = $this->input->post('tags');
        $booknum = $this->input->post('booknum');

        $this->usermodel->set_tags($noteid, $tags, $booknum);
    }
}
?>