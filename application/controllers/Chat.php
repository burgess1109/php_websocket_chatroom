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

        $data['socket_url'] = "ws://localhost:9000/web_socket/Chat-Using-WebSocket-and-PHP-Socket-master/CI_talk/php_websocket_chatroom/server.php";//socket server 路徑指向
        $data['username'] = $this->session->username ;
        $data['user_colour'] = $this->session->user_colour ;

        $this->load->view('chat/index',$data);
    }
}
