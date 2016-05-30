<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

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
        //頭像
        $data['head_arr']['boy']=['boy_1.jpg','boy_2.jpg','boy_3.jpg','boy_4.jpg','boy_5.jpg','boy_6.jpg',
            'boy_7.jpg','boy_8.jpg','boy_9.jpg','boy_10.jpg'];

        $data['head_arr']['girl']=['girl_1.jpg','girl_2.jpg','girl_3.jpg','girl_4.jpg','girl_5.jpg','girl_6.jpg',
            'girl_7.jpg','girl_8.jpg','girl_9.jpg','girl_10.jpg'];

        $data['head_arr']['other']=['other_1.jpg','other_2.jpg','other_3.jpg','other_4.jpg','other_5.jpg','other_6.jpg',
            'other_7.jpg','other_8.jpg','other_9.jpg','other_10.jpg'];

        $this->load->view('login/index',$data);
    }

    /**
     * 登入驗證
     */
    public function check(){
        echo '資料驗證中......';
        $username = $this->input->post('user');
        $password = $this->input->post('passwd');
        $sex = $this->input->post('sex');
        $head = $this->input->post('head');

        if(empty($username)) $this->js_alert('未輸入名稱');
        if(mb_strlen($username)<4) $this->js_alert('名稱至少4個字');
        if(empty($password)) $this->js_alert('未輸入密碼');
        if(mb_strlen($password)<4) $this->js_alert('密碼至少4個字');
        if($password != '123@456') $this->js_alert('密碼錯誤');
        if(empty($sex)) $this->js_alert('未選擇性別');
        if(empty($head)) $this->js_alert('未選擇頭像');

        //顏色
        $colours = array('007AFF','FF7000','FF7000','15E25F','CFC700','CFC700','CF1100','CF00BE','F00');
        $index= array_rand($colours);
        $user_colour = $colours[$index];

        //使用者名稱存成SESSION
        $session_data=array(
            'username'  => $username,
            'login_status' => true,
            'user_colour' => $user_colour,
            'sex' => $sex,
            'head' => $head,
        );
        $this->session->set_userdata($session_data);

        header("location:".site_url()."/chat");
    }

    /**
     * 登入驗證
     */
    public function logout(){
        echo '登出中......';
        $session_data=array('username','login_status','user_colour');


        $this->session->unset_userdata($session_data);
        if(empty($this->session->username) && empty($this->session->login_status)){
            $this->js_alert('登出成功',site_url().'/login');
        }

    }
}
