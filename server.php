<?php
$host = 'localhost'; //host
$port = '9000'; //port
$null = NULL; //null var

//Create TCP/IP sream socket (建立socket)
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//reuseable port (設定socket SO_REUSEADDR:允許重新使用本地地址)
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

//bind socket to specified host (把socket绑定在一个IP地址和端口上 socket_bind(來源socket, IP, port))
socket_bind($socket, 0, $port);

//listen to port(監聽socket)
socket_listen($socket);

//create & add listning socket to the list
$clients = array($socket);

//name list 進入聊天室人員名單
$join_list = array();

//start endless loop, so that our script doesn't stop
while (true) {
	//manage multipal connections
	$changed = $clients;
	//returns the socket resources in $changed array 多線程(接受多個socket連接)
	socket_select($changed, $null, $null, 0, 10);
	
	//check for new socket
	if (in_array($socket, $changed)) {
		$socket_new = socket_accept($socket); //accpet new socket (接受新的socket，一旦成功建立socket连接，将会返回一个新的socket资源)
		$clients[] = $socket_new; //add socket to client array
		$join_list[] = ''; //名單
		$header = socket_read($socket_new, 1024); //read data sent by the socket
		perform_handshaking($header, $socket_new, $host, $port); //perform websocket handshake (寫數據至socket, 從 HTTP 協定升級為 WebSocket 協定)
		
		socket_getpeername($socket_new, $ip); //get ip address of connected socket
		$response = mask(json_encode(array('type'=>'system','info'=>'enter', 'message'=>$ip.' 已連線'))); //prepare json data (mask:包裹資料成為二進制字串)
		send_message($response); //notify all users about new connection (發送至每個socket)
		
		//make room for new socket
		$found_socket = array_search($socket, $changed);//搜索socket在changed陣列的index
		unset($changed[$found_socket]);
	}
	
	//loop through all connected sockets
	foreach ($changed as $changed_socket) {	
		
		//check for any incomming data
		while(socket_recv($changed_socket, $buf, 1024, 0) >= 1) //讀取socket數據並存在$buf
		{
			$received_text = unmask($buf); //unmask data 解密
			$tst_msg = json_decode($received_text); //json decode 
			if($tst_msg->type=='join_name'){
				//名稱輸入
				$join_name = $tst_msg->join_name; //sender name
				$user_color = $tst_msg->color; //color
                $sex = $tst_msg->sex; //性別
				$head = $tst_msg->head; //頭像

				//找key值
				$key = array_keys($clients,$changed_socket);
				$join_list[$key[0]]['join_name']=$join_name;
				$join_list[$key[0]]['color']=$user_color;
				$join_list[$key[0]]['sex']=$sex;
				$join_list[$key[0]]['head']=$head;

				//prepare data to be sent to client (mask 加密轉換)
				$response_text = mask(json_encode(array('type'=>'join_name', 'join_name'=>$join_name,'color'=>$user_color,
                    'join_list'=>$join_list)));
			}else{
				//訊息輸入
				$user_name = $tst_msg->name; //sender name
				$user_message = $tst_msg->message; //message text
				$user_color = $tst_msg->color; //color
				//prepare data to be sent to client (mask 加密轉換)
				$response_text = mask(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color)));
			}
			
			send_message($response_text); //send data 發佈至各socket
			break 2; //exist this loop
		}
		
		$buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
		if ($buf === false) { // check disconnected client 若socket無連線
			//移除名單
			//找key值
			$key = array_keys($clients,$changed_socket);
		
			// remove client for $clients array 從socket clients陣列移除
			$found_socket = array_search($changed_socket, $clients);
			socket_getpeername($changed_socket, $ip);//取得來源socket IP 及 post
			unset($clients[$found_socket]);
			
			//notify all users about disconnected connection
			$leave_name = $join_list[$key[0]]['join_name'];
			unset($join_list[$key[0]]);
			$response = mask(json_encode(array('type'=>'system', 'info'=>'leave','message'=>$ip.'--'.$leave_name.' 離線','join_list'=>$join_list)));
			
			send_message($response);
		}
	}
}
// close the listening socket
socket_close($socket);

function send_message($msg)
{
	global $clients;
	foreach($clients as $changed_socket)
	{
		@socket_write($changed_socket,$msg,strlen($msg));
	}
	return true;
}


//Unmask incoming framed message
function unmask($text) {
	$length = ord($text[1]) & 127; //ord() 函数返回字符串的首个字符的 ASCII 值。
	if($length == 126) {
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	}
	elseif($length == 127) {
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	}
	else {
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}

//Encode message for transfer to client.
function mask($text)
{
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);
	
	if($length <= 125)
		$header = pack('CC', $b1, $length);//pack 包裹資料成為二進制字串
	elseif($length > 125 && $length < 65536)
		$header = pack('CCn', $b1, 126, $length);
	elseif($length >= 65536)
		$header = pack('CCNN', $b1, 127, $length);
	return $header.$text;
}

//handshake new client. (從 HTTP 協定升級為 WebSocket 協定)
function perform_handshaking($receved_header,$client_conn, $host, $port)
{
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
	foreach($lines as $line)
	{
		$line = chop($line);
		if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
		{
			$headers[$matches[1]] = $matches[2];
		}
	}

	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	//hand shaking header
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: $host\r\n" .
	"WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn,$upgrade,strlen($upgrade));//寫數據至socket
}
