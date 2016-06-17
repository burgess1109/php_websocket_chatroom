<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OOXX.Talk登入</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url().'css/style.default.css';?>" type="text/css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src=" http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script></head>

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

            <div class="sex">
                <div class="sexinner">
                    <p>請選擇性別:</p>
                    <input type="radio" name="sex" value="boy"/>男 &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="sex" value="girl"/>女 &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="sex" value="other"/>其他 &nbsp;&nbsp;&nbsp;
                </div>
            </div>

            <div class="head">
                <div class="headinner">
                    <p>請選擇頭像:</p>
                    <?php
                        foreach($head_arr as $key => $val){
                            echo '<div id="'.$key.'" class="head_pic">';
                            foreach($val as $k => $v) {
                                $head_value=$key.'_'.($k+1);
                                echo '<input type="radio" name="head" value="'.$head_value.'"/>';
                                echo '<img src="'.base_url().'images/thumbs/head/'.$v.'"/>';
                                echo '&nbsp;&nbsp;&nbsp;';
                                if($k%4 == 4) echo '<br>';
                            }
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>

            <button id="login_btn">登入</button>
        </form>

    </div><!--loginboxinner-->
</div><!--loginbox-->
<script>
    $(document).ready(function(){
        //性別頭像 JS
        $("input[name='sex']").click(function(){
            var sex = $("input[name='sex']:checked").val();
            $('.head_pic').hide();
            $('#'+sex).show();
        });

        $('#login_btn').click(function(){
            var check = new check_form();

            try{
                if(check.check_user() == false) throw(check.error_msg);
                if(check.check_passwd() == false) throw(check.error_msg);
                if(check.check_sex() == false) throw(check.error_msg);
                if(check.check_head() == false) throw(check.error_msg);
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
            var sex = $("input[name='sex']:checked").val();
            var head = $("input[name='head']:checked").val();
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

            //性別驗證
            this.check_sex= function(){
                if(typeof(sex) == "undefined"){
                    this.error_msg = '未選擇性別';
                    return false;
                }else if(sex.trim() == '' || sex==null ){
                    this.error_msg = '未選擇性別';
                    return false;
                }else{
                    return true;
                }
            };

            //頭像驗證
            this.check_head= function(){
                if(typeof(head) == "undefined"){
                    this.error_msg = '未選擇頭像';
                    return false;
                }else if(head.trim() == '' || head==null ){
                    this.error_msg = '未選擇頭像';
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
