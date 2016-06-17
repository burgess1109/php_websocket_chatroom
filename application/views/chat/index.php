<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OOXX.Talk</title>
    <!-- 最新編譯和最佳化的 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url().'css/style.default.css';?>" type="text/css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src=" http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script>
</head>
<body>
<div class="bodywrapper">
    <div class="centercontent">
        <div class="pageheader notab">
                <h1 class="pagetitle">OOXX.Talk</h1>
                <span class="pagedesc">這是一個簡單的聊天室</span>
        </div><!--pageheader-->

        <div id="contentwrapper" class="contentwrapper withrightpanel">

            <div class="subcontent chatcontent">

                <div id="chatmessage" class="chatmessage radius2">
                    <div id="chatmessageinner"></div><!--chatmessageinner-->
                </div><!--chatmessage-->
                <br>
                <span id="welcome_str"></span>
                <div class="messagebox radius2">
                    <span class="inputbox" style="width:70%;float: left;">
                        <input type="text" id="msgbox" name="msgbox"  />
                    </span>
                    <button id="emotion" class="btn btn-success" style="float: left;">插入表情</button>
                    <button id="send-btn" class="btn btn-warning" style="float: left;margin-left: 20px;">送出</button>
                    <button class="btn btn-danger" id="leave-btn" style="float: left;margin-left: 20px;">登出/離開</button>
                </div>

            </div><!--subcontent-->

        </div><!--contentwrapper-->

        <div class="rightpanel">
            <div class="rightpanelinner">
                <div class="widgetbox uncollapsible">
                    <div class="title"><h4>線上使用者</h4></div>
                    <div class="widgetcontent nopadding">
                        <!--
                                                        <div class="chatsearch">
                                                            <input type="text" name="" value="Search" />
                                                        </div>
                                                        -->
                        <ul class="contactlist">
                        </ul>
                    </div><!--widgetcontent-->
                </div><!--widgetbox-->
            </div><!--rightpanelinner-->
        </div><!--rightpanel-->

    </div><!-- centercontent -->
</div>

<!--  使用 QQFace 表情符號 JS-->
<script type="text/javascript" src="<?php echo base_url().'js/jquery.qqFace.js';?>"></script>
<script language="javascript" type="text/javascript">
    $(document).ready(function(){
        //create a new WebSocket object.(建立socket物件)
        var wsUri = "<?php echo $socket_url;?>";
        websocket = new WebSocket(wsUri);
        websocket.onopen = function(ev) { // connection is open (socket連接時觸發的事件)
            if(ev.isTrusted && ev.type=='open'){
                //確認socket連結是 open 狀態
                //取得名稱
                var name = '<?php echo $username;?>';
                if(name.trim()=='' || name.trim()==null || name.trim()==[] || typeof(name) =='undefined'){
                    alert('尚未登入');
                    window.location = "<?php echo site_url().'/index.php/login'?>";
                    return false;
                }else{
                    $('#chatmessage').append("<div class=\"system_msg\">連結中......</div>"); //notify user
                    $("#welcome_str").html('歡迎 <b>'+name+' </b>, 請於下方輸入留言:');
                    //prepare json data
                    var msg = {
                        type : 'join_name',
                        join_name: name,
                        color : '<?php echo $user_colour; ?>',
                        sex : '<?php echo $sex;?>',
                        head : '<?php echo $head;?>',
                    };
                    //convert and send data to server (連接傳送數據)
                    websocket.send(JSON.stringify(msg));
                }
            }
        }

        $('#send-btn').click(function(){ //use clicks message send button
            message_send();
        });

        $('#msgbox').keypress(function(event){ //按下Enter 自動送出訊息
            if(event.keyCode==13){
                message_send();
            }
        });

        function message_send(){
            var mymessage = $('#msgbox').val(); //get message text
            var myname = '<?php echo $username;?>'; //get user name

            if(myname == ""){ //empty name?
                alert('尚未登入');
                window.location = "<?php echo site_url().'/login'?>";
                return false;
            }
            if(mymessage == ""){ //emtpy message?
                alert("未輸入留言");
                return false;
            }

            //prepare json data
            var msg = {
                type : 'usermsg',
                message: mymessage,
                name: myname,
                color : '<?php echo $user_colour; ?>'
            };
            //convert and send data to server (連接傳送數據)
            websocket.send(JSON.stringify(msg));
        }

        $('#leave-btn').click(function(){
            websocket.close();
            $('#chatmessage').append("<div class=\"system_msg\">您已離線...</div>");

            window.location = "<?php echo site_url().'/login/logout'?>";
        });

        //#### Message received from server? (view端接收server數據時觸發事件)
        websocket.onmessage = function(ev) {
            var msg = JSON.parse(ev.data); //PHP sends Json data
            var type = msg.type; //message type
            var ucolor = msg.color; //color
            if(type == 'usermsg')
            {
                var uname = msg.name; //user name
                var umsg = msg.message; //message text
                var umsg=replace_em(umsg);//QQ表情 字串轉換
                if(uname && umsg){
                    $('#chatmessage').append("<div><span class=\"user_name\" style='color:#"+ucolor+"'>"+uname+"</span> : <span class=\"user_message\">"+umsg+"</span></div>");
                }
            }
            if(type == 'system')
            {
                //更新名單
                if(msg.info == 'enter'){
                    var umsg = msg.message; //message text
                    //$('#chatmessage').append("<div class=\"system_msg\">"+umsg+"</div>");
                }

                //更新名單
                if(msg.info == 'leave'){
                    var umsg = msg.message; //message text
                    $('#chatmessage').append("<div class=\"system_msg\">"+umsg+"</div>");

                    var join_list = msg.join_list; //join list
                    $('.contactlist').empty();
                    for(var index in join_list) {
                        if(join_list[index].join_name){
                            if(join_list[index].head == ''){
                                var img_path = '<?php echo base_url()."images/thumbs/head/unknown.png"; ?>';
                            }else{
                                var img_path = '<?php echo base_url()."images/thumbs/head/"; ?>'+join_list[index].head+'.jpg';
                            }
                            var add_html = "<li class='online new'><a href=''><img src='"+img_path+"' alt=''><span style='color:#"+join_list[index].color+"'>"+join_list[index].join_name+"</span></a></li>";
                            $('.contactlist').append(add_html);
                        }
                    }
                }
            }

            if(type == 'join_name')
            {
                var join_name = msg.join_name; //join name
                var join_list = msg.join_list; //join list

                $('#chatmessage').append("<div class=\"system_msg\">"+join_name+"連線成功</div>");

                //更新名單
                $('.contactlist').empty();
                for(var index in join_list) {
                    if(join_list[index].join_name){
                        if(join_list[index].head == ''){
                            var img_path = '<?php echo base_url()."images/thumbs/head/unknown.png"; ?>';
                        }else{
                            var img_path = '<?php echo base_url()."images/thumbs/head/"; ?>'+join_list[index].head+'.jpg';
                        }
                        var add_html = "<li class='online new'><a href=''><img src='"+img_path+"' alt=''><span style='color:#"+join_list[index].color+"'>"+join_list[index].join_name+"</span></a></li>";
                        $('.contactlist').append(add_html);
                    }
                }
            }

            $('#msgbox').val(''); //reset text
        };

        websocket.onerror	= function(ev){$('#chatmessage').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");}; //與server連接發生錯誤時
        websocket.onclose 	= function(ev){$('#chatmessage').append("<div class=\"system_msg\">Connection Closed</div>");};  //server被關閉時


        <!--  QQFace 表情符號 -->
        <!--  設定qqFace  參數 -->
        $('#emotion').qqFace({
            id : 'facebox', //表情盒子的ID
            assign:'msgbox', //對話輸入input控件ID
            path:'<?php echo base_url().'images/face/';?>'	//表情存放的路径
        });

        //查看结果(表情符號轉換)
        function replace_em(str){
            str = str.replace(/\</g,'&lt;');
            str = str.replace(/\>/g,'&gt;');
            str = str.replace(/\n/g,'<br/>');
            str = str.replace(/\[em_([0-9]*)\]/g,'<img src="<?php echo base_url().'images/face';?>/$1.gif" border="0" />');
            return str;
        }

    });
</script>
</body>
</html>