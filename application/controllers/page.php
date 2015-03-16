<?php 
class Page extends CI_Controller {
    public function __construct ( ) {
        parent::__construct ( );
        $this->load->library ( 'masterpage' );
        $this->load->model('usermodel');

        if (!$this->usermodel->url_contains("login") && !$this->usermodel->url_contains("create") && !$this->usermodel->url_contains("fail")&& !$this->usermodel->url_contains("about")&& !$this->usermodel->url_contains("forgot")&& !$this->usermodel->url_contains("success")&& !$this->usermodel->url_contains("demo"))
        {
            $logged_in = $this->session->userdata('id');
            if($logged_in === FALSE) {
                redirect("page/login");
            }
        }

        $this->masterpage->setMasterPage ( 'mp_default' );
    }

    public function index( ) {
        
        $bookrows = $this->usermodel->get_books();
        $book = array("bookname", "booknum");

        $userbook = $this->session->userdata("book");
        if( !empty($userbook) )
        {
            foreach($bookrows["result"] as $key){
                if($key["booknum"] == $this->session->userdata("book")) $book=$key;
            }

            $rows = $this->usermodel->get_notes($this->session->userdata("book"));

            $tags1 = $this->usermodel->get_tags($this->session->userdata("book"));
        }
        elseif ($bookrows["num_rows"] > 0)
        {
            $book = $bookrows["first_row"];
            $rows = $this->usermodel->get_notes($bookrows["first_row"]->booknum);
            $tags1 = $this->usermodel->get_tags($bookrows["first_row"]->booknum);
        }

        $positions = array();
        $tags = "";
        if ($bookrows["num_rows"] > 0) {
            foreach ($rows as $row){
                if ($rows !== false){
                    $positions[] = array(
                        "number" => $row["number"],
                        "note" => str_replace("\n", "<br/>", $row["note"]),
                        "dateset" => $row["dateset"],
                        "duedate" => substr($row["duedate"], 0, -3),
                        "email" => $row["email"],
                        "priority" => $row["priority"]
                    );
                }
            }
            //Sort tags
            function cmp($a, $b){
                return $b['ordery'] - $a['ordery'];
            }
            usort($tags1, "cmp");
            $tags = array_reverse($tags1);

            // tags dropdown
            $var = $this->usermodel->get_alltags();
            foreach ($var as $r) {
                $rowstags[] = $r["tags"];
            }
            $rowstags = array_unique($rowstags);
            foreach ($rowstags as $q) {
                $tagsarray[] = trim($q);
            }
            /*foreach($rows1 as $row1) {
                $tagsarray[] = (object) array("id" => $row1, "text" => $row1);
            }*/
        }

        $data = array(
            "title" => "Reminders",
            "positions" => $positions,
            "tags" => $tags,
            "tagsarray" => $tagsarray,
            "book" => $book,
            "bookrow" => $bookrows["result"]
        );

        // render reminder;
        
        $this->masterpage->addContentPage ( "home", 'content', $data );
        $this->masterpage->show ( );
        /*echo $this->security->get_csrf_token_name();
        echo "<br> \n";
        echo $this->security->get_csrf_hash();*/
    }


    public function login()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
    
        $data['title'] = 'Login';
        
    
        $this->form_validation->set_rules('name', 'Email', 'required');
        $this->form_validation->set_rules('pass', 'Password', 'required');
    
        if ($this->form_validation->run() === FALSE)
        {
            
            $this->masterpage->addContentPage ( 'login', 'content', $data );
            $this->masterpage->show ( );
            
        }
        else
        {
            $name = $this->usermodel->check_login($this->input->post('name'));
            $pass = $this->usermodel->check_pass($this->input->post('pass'), $this->input->post('name'));
            if( ($name===true) && ($pass === true) ) {
                $this->session->set_userdata("id", $this->usermodel->get_id($this->input->post("name")));
                //echo $name." , ".$pass;
                redirect("/");
            } else {
                $data["msg"] = "Incorrect Username or password";
                $this->fail($data);
            }
        }
    }


    public function demo()
    {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip_address = getenv("HTTP_X_FORWARDED_FOR");
        } else {
            $ip_address = getenv("REMOTE_ADDR");
        }
        $adrs = (array) json_decode(file_get_contents("http://ipinfo.io/".$ip_address."/json"));
        $uas = (array) json_decode(file_get_contents("http://useragentstring.com/?uas=".urlencode($_SERVER['HTTP_USER_AGENT'])."&getJSON=all"));
        $htmlnote =
            "<html><body>
                User logged into John.
                <p>IP Address(simplified):<br>".implode("<br>", array_filter($adrs))."</p>
                        <p>User agent(simplified):<br>".implode("<br>", array_filter($uas))."</p>
                        <p>IP Address: ".$ip_address."</p>
                        <p>User agent: ".$_SERVER['HTTP_USER_AGENT']."</p>
                    </body></html>";
        $this->usermodel->mailgun("shazvi@outlook.com","Someone logged into John's user!",$htmlnote);

        $this->session->set_userdata("id", JOHNID);
        redirect("/");
    }


    public function create()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('captcha');
    
        $data['title'] = 'Register';
        
    
        $this->form_validation->set_rules('name', 'Name', 'required|is_unique[users.username]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('pass', 'Password', 'required|matches[conf]');
        $this->form_validation->set_rules('conf', 'Password Confirmation', 'required');
        $this->form_validation->set_rules('captcha', 'Captcha', 'required|callback__captcha_check[]');
    
        if ($this->form_validation->run() === FALSE)
        {            
            $vals = array(
                'img_path'  => './captcha/',
                'img_url'   => base_url().'captcha/'
                );
            
            $cap = create_captcha($vals);

            $datam = array(
                'captcha_time'  => $cap['time'],
                'ip_address'    => $this->input->ip_address(),
                'word'  => $cap['word']
            );
            
            $query = $this->db->insert_string('captcha', $datam);
            $this->db->query($query);
            $data["image"] = $cap["image"];
            
            $this->masterpage->addContentPage ( 'create', 'content', $data );
            $this->masterpage->show ( );
    
        }
        else
        {
            $name_exist = $this->usermodel->name_exist($this->input->post('name'));
            $email_exist = $this->usermodel->email_exist($this->input->post('email'));
            if ($name_exist) {
                $data = array("msg" => "Name already exists.");
                $this->fail($data);
            } elseif ($email_exist) {
                $data = array("msg" => "Email already exists.");
                $this->fail($data);
            } else {
                // First, delete old captchas from db
                $expiration = time()-7200; // Two hour limit
                $this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);

                //Delete old captchas from dir
                $files = glob("./captcha/", GLOB_BRACE);
                foreach($files as $file){
                    if(filemtime($file) < $expiration){
                        unlink($file);
                    }
                }
                
                // Then see if a captcha exists:
                $sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
                $binds = array($_POST['captcha'], $this->input->ip_address(), $expiration);
                $query = $this->db->query($sql, $binds);
                $row = $query->row();
                
                if ($row->count == 0)
                {
                    $data = array("msg" => "You must submit the word that appears in the image");
                    $this->fail($data);
                } else {
                    $this->usermodel->register_db();
                    $this->session->set_userdata("id", $this->usermodel->get_id($this->input->post("name")));
                    redirect("/");
                }

                
            }
        }
    }


    public function fail($data = array())
    {
        
        $this->masterpage->addContentPage ( 'fail', 'content', $data );
        $this->masterpage->show ( );
    }


    public function logout()
    {
        $this->session->sess_destroy();
        redirect("/");
    }

    public function about($data = array())
    {
        $this->masterpage->addContentPage ( 'about', 'content', $data );
        $this->masterpage->show ( );
    }

    public function forgot($data='')
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('string');
    
        $data['title'] = 'Forgot Password';
        
    
        $this->form_validation->set_rules('email', 'Email', 'required');
    
        if ($this->form_validation->run() === FALSE)
        {
            
            $this->masterpage->addContentPage ( 'forgot', 'content', $data );
            $this->masterpage->show ( );
            
        }
        else
        {
            $email_exist = $this->usermodel->email_exist($this->input->post('email'));
            if ($email_exist) {
                $newpass = random_string('alnum', 6);

                $this->usermodel->set_pass($newpass);

                $html = 
                '<html><body> '.
                    '<p>Your password has been reset to: </p>'.
                    '<p> ' . $newpass .'</p>'.
                    'Go to <a href="'. base_url().'">Memento</a>' .
                '</body></html>';

                $this->usermodel->mail($this->input->post('email'), "Memento Password Reset", $html);
                $data["msg"] = "Password has been reset.";
                $this->success($data);
            } else {
                $data["msg"] = "Email doesn't exist.";
                $this->fail($data);
            }
        }
    }

    public function success($data = array())
    {
        $this->masterpage->addContentPage ( 'success', 'content', $data );
        $this->masterpage->show ( );
    }
}
?>