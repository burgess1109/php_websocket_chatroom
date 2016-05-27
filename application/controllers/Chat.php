<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
    }

    /**
     * 登入頁
     */
    public function index()
    {
        if(!$this->session->login_status) $this->js_alert('尚未登入',site_url().'/login');
        if(empty($this->session->username)) $this->js_alert('名稱錯誤',site_url().'/login');

        $data['username'] = $this->session->username ;
        $data['user_colour'] = $this->session->user_colour ;

        $this->load->view('chat/index',$data);
    }
}
