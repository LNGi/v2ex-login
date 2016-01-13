<?php

    $v2ex = new V2EX();
    //登陆
    $v2ex->login('账号','密码');
	//需要代理的配置代理
	//$v2ex->setProxy("127.0.0.1", 8888)->login('账号','密码');
    //执行签到
    $v2ex->signin();


    function __autoload($className){  //自动引入类库
        include './class/'.$className.'.class.php';
    }
?>
