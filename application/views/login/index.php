<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>聊天登入</title>
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

<body class="loginpage">
<div class="loginbox">
    <div class="loginboxinner">

        <div class="logo">
            <h1 class="logo">OOXX.<span>Talk</span></h1>
        </div><!--logo-->

        <br clear="all" /><br />

        <div class="nopassword">
            <div class="loginf">
                <div class="thumb"><img alt="" src="<?php echo base_url().'images/thumbs/avatar1.png';?>" /></div>
                <div class="userlogged">
                    <span></span>
                </div>
            </div><!--loginf-->
        </div><!--nopassword-->

        <form id="login" action="<?php echo site_url().'/login/check';?>" method="post">

            <div class="username">
                <div class="usernameinner">
                    <input type="text" name="user" id="user" placeholder="名稱"/>
                </div>
            </div>

            <div class="password">
                <div class="passwordinner">
                    <input type="password" name="passwd" id="passwd" placeholder="密碼"/>
                </div>
            </div>

            <button id="login_btn">登入</button>

        </form>

    </div><!--loginboxinner-->
</div><!--loginbox-->
<script>
    jQuery(function($) {
        $('#login_btn').click(function(){
            var check = new check_form();

            try{
                if(check.check_user() == false) throw(check.error_msg);
                if(check.check_passwd() == false) throw(check.error_msg);
            }catch(error_msg){
                $('.nopassword').show();
                $('.userlogged').html('<span>'+error_msg+'</span>');
                alert(error_msg);
                return false;
            }

            $('#login').submit();
        });

        var check_form = function(){
            var username = $("input[name='user']").val();
            var password = $("input[name='passwd']").val();
            this.error_msg = '';

            //名稱驗證
            this.check_user = function(){
                if(username.trim() == '' || username==null || typeof(username) == "undefined"){
                    this.error_msg = '未輸入名稱';
                    return false;
                }else if(username.length < 4){
                    this.error_msg = '名稱至少4個字';
                    return false;
                }else{
                    return true;
                }
            };

            //密碼驗證
            this.check_passwd = function(){
                if(password.trim() == '' || password==null || typeof(password) == "undefined"){
                    this.error_msg = '未輸入密碼';
                    return false;
                }else if(password.length < 4){
                    this.error_msg = '密碼至少4個字';
                    return false;
                }else{
                    return true;
                }
            };
        }
    });
</script>

</body>
</html>
