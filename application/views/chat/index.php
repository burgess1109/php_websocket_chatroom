<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>聊天系統</title>
    <!-- 最新編譯和最佳化的 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url().'css/style.default.css';?>" type="text/css" />
    <script type="text/javascript" src="<?php echo base_url().'js/plugins/jquery-1.7.min.js';?>"></script>
    <script type="text/javascript" src="<?php echo base_url().'js/plugins/jquery-ui-1.8.16.custom.min.js';?>"></script>
    <script type="text/javascript" src="<?php echo base_url().'js/plugins/jquery.cookie.js';?>"></script>
    <script type="text/javascript" src="<?php echo base_url().'js/plugins/jquery.uniform.min.js';?>"></script>
    <script type="text/javascript" src="<?php echo base_url().'js/custom/general.js';?>"></script>
    <script type="text/javascript" src="<?php echo base_url().'js/custom/index.js';?>"></script>
    <!--[if IE 9]>
    <link rel="stylesheet" media="screen" href="<?php echo base_url().'css/style.ie9.css';?>"/>
    <![endif]-->
    <!--[if IE 8]>
    <link rel="stylesheet" media="screen" href="<?php echo base_url().'css/style.ie8.css';?>"/>
    <![endif]-->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url().'js/plugins/css3-mediaqueries.js';?>"></script>
    <![endif]-->
</head>
<body>
<div class="bodywrapper">
    <div class="centercontent">
        <div class="pageheader notab">
                <h1 class="pagetitle">OOXX. Talk</h1>
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
                    <span class="inputbox" style="width:80%;float: left;">
                        <input type="text" id="msgbox" name="msgbox"  />
                    </span>
                    <button id="send-btn" class="btn btn-warning" style="float: left;">送出</button>
                    <button class="btn btn-danger" id="leave-btn" style="float: left;margin-left: 20px;">登出/離開</button>
                </div>

            </div><!--subcontent-->

        </div><!--contentwrapper-->

        <div class="rightpanel">
            <div class="rightpanelinner">
                <div class="widgetbox uncollapsible">
                    <div class="title"><h4>Online Users</h4></div>
                    <div class="widgetcontent nopadding">
                        <div class="chatsearch">
                            <input type="text" name="" value="Search" />
                        </div>
                        <ul class="contactlist">
                            <li class="online new"><a href=""><img src="<?php echo base_url().'images/thumbs/avatar5.png';?>" alt="" /> <span>Hiccup Haddock III</span></a><span class="msgcount">3</span></li>
                            <li><a href=""><img src="<?php echo base_url().'images/thumbs/avatar6.png';?>" alt="" /> <span>Astrid Hofferson</span></a></li>
                            <li class="online"><a href=""><img src="<?php echo base_url().'images/thumbs/avatar7.png';?>" alt="" /> <span>Vector</span></a></li>
                            <li class="online"><a href=""><img src="<?php echo base_url().'images/thumbs/avatar8.png';?>" alt="" /> <span>Puss in Boots</span></a></li>
                            <li class="online new"><a href=""><img src="<?php echo base_url().'images/thumbs/avatar9.png';?>" alt="" /> <span>Humpty Dumpty</span></a><span class="msgcount">1</span></li>
                            <li><a href=""><img src="<?php echo base_url().'images/thumbs/avatar10.png';?>" alt="" /> <span>Shrek</span></a></li>
                            <li><a href=""><img src="<?php echo base_url().'images/thumbs/avatar11.png';?>" alt="" /> <span>Princess Fiona</span></a></li>
                        </ul>
                    </div><!--widgetcontent-->
                </div><!--widgetbox-->
            </div><!--rightpanelinner-->
        </div><!--rightpanel-->

    </div><!-- centercontent -->
</div>

<script language="javascript" type="text/javascript">
    jQuery(function($) {
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
                        color : '<?php echo $user_colour; ?>'
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
                            <?php $img_path=base_url()."images/thumbs/avatar5.png"; ?>
                            var add_html = "<li class='online new'><a href=''><img src='<?php echo $img_path;?>' alt=''><span style='color:#"+join_list[index].color+"'>"+join_list[index].join_name+"</span></a></li>";
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
                        <?php $img_path=base_url()."images/thumbs/avatar5.png"; ?>
                        var add_html = "<li class='online new'><a href=''><img src='<?php echo $img_path;?>' alt=''><span style='color:#"+join_list[index].color+"'>"+join_list[index].join_name+"</span></a></li>";
                        $('.contactlist').append(add_html);
                    }
                }
            }

            $('#msgbox').val(''); //reset text
        };

        websocket.onerror	= function(ev){$('#chatmessage').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");}; //與server連接發生錯誤時
        websocket.onclose 	= function(ev){$('#chatmessage').append("<div class=\"system_msg\">Connection Closed</div>");};  //server被關閉時
    });
</script>
</body>
</html>