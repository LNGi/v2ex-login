<?php

	 $v2ex = new V2EX();

	 $v2ex->login('用户名','密码');

     //如果需要配置代理ip，使用如下
     //$v2ex->setProxy("代理ip", 代理端口)->login('用户名','密码');



    function __autoload($className){  //自动引入类库
         include './class/'.$className.'.class.php';
     }
?>