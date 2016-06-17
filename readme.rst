###################
OOXX.Talk PHP Socket 聊天室 (OOXX.Talk PHP Socket Chatroom)
###################

OOXX.Talk 為 PHP Socket 簡易聊天室範例，以 CodeIgniter 3.0 為框架製作，提供簡單的登入、聊天及發送表情符號功能


**************************
修改記錄 (Changelog and New Features)
**************************

您可以從連結查詢修改記錄  `user
guide change log <https://github.com/burgess1109/php_websocket_chatroom/changelog.rst>`_.
You can find a list of all changes for each release in the `user
guide change log <https://github.com/burgess1109/php_websocket_chatroom/changelog.rst>`_.

*******************
需求及說明(Requirements & Explanation)
*******************

-  PHP 版本 5.2.4 或者更高版本。(PHP version 5.2.4 or newer is recommended.)
-  需要php_sockets擴展套件。 (php_sockets extension needed.)
-  檔案已含CodeIgniter 3.0.6及qqFace表情插件。 (CodeIgniter 3.0.6 & qqFace plugin included.)


************
使用說明
************

-  OOXX.Talk 使用 Sanwebe.com `PHP Socket 範例 <https://www.sanwebe.com/2013/05/chat-using-websocket-php-socket>`_ 為基底擴充，下載後需先執行根目錄下的 server.php 檔案 (參考指令: php -q server.php)，再開啟網頁
-  server.php 可指定Socket的 host 及 port
-  controller/Chat.php @index 的 $data['socket_url'] 需指向 server.php 的路徑
-  登入密碼預設 123@456 (controller/Login.php @check )
-  表情插件使用 `qqFace <http://www.helloweba.com/view-blog-202.html>`_
-  本範例為PHP框架+PHP Socket簡單實作，程式內容僅供參考，歡迎高手大大們指教


*********
參考來源(Resources)
*********

-  `Sanwebe PHP Socket<https://www.sanwebe.com/2013/05/chat-using-websocket-php-socket>`_
-  `CodeIgniter <https://codeigniter.org.tw/>`_
-  `qqFace <http://www.helloweba.com/view-blog-202.html>`_
-  `chinaz 模板 <http://sc.chinaz.com/>`_


