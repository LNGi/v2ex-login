<?php

/**
 *
 */
class V2EX
{
    private $userName;  //用户名
    private $passwd;    //密码
    // private $ch;        //curl handle
    private $cookie;    //cookie
    private $html;      //html
    private $once;      //once值

    //登陆页面（获取cookie，获取once）
    private static $API_LOGINPAGE    = "http://www.v2ex.com/signin";

    //登陆POST地址
    private static $API_LOGIN        = "http://www.v2ex.com/signin";

    //签到页面（获取once）
    private static $API_SIGNINPAGE   = "http://www.v2ex.com/signin";

    //提交签到地址
    private static $API_SIGNIN       = "http://www.v2ex.com/mission/daily/redeem?once=";


    private static $header = array(
        'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Content-Type: application/x-www-form-urlencoded',
        'Referer: http://www.v2ex.com/signin',
        'Origin: http://www.v2ex.com'
    );

    private static $optionsCommon = array(
        CURLOPT_AUTOREFERER => 1, //开启重定向
        CURLOPT_FOLLOWLOCATION => 1, //是否抓取跳转后的页面
        CURLOPT_HEADER => 1, //启用时会将头文件的信息作为数据流输出。
        CURLOPT_RETURNTRANSFER => 1,   //将 curl_exec() 获取的信息以文件流的形式返回，而不是直接输出。
    );


    function __construct($proxy = "", $port = "")
    {
        //$this->get($url);
    }

    /**
     * 设置代理
     * @param $proxy
     * @param $port
     */
    public function setProxy($proxy, $port)
    {
        $proxyArray = array(CURLOPT_PROXY => $proxy, CURLOPT_PROXYPORT => $port);
        //使用array_merge()会导致数组键名重新索引。
        self::$optionsCommon = self::$optionsCommon + $proxyArray;
        return $this;
    }

    public function login($userName, $passwd)
    {
        $this->userName = $userName;
        $this->passwd = $passwd;

        //第一次请求登陆页面，为了获取cookie和once
        $this->get(self::$API_LOGINPAGE);

        //获取登陆参数：cookie
        $this->getCookie($this->html);

        //获取登陆参数：once
        $this->getOnce($this->html);

        //开始post提交登陆
        $this->post(self::$API_LOGIN,
            array('u' => $this->userName,
                'p' => $this->passwd,
                'once' => $this->once,
                'next' => '/'),
            self::$optionsCommon
        );

        //获取登陆之后的cookie
        $this->getCookie($this->html);

        echo $this->html;


    }

    public function signin()
    {
        $this->get(self::$API_SIGNINPAGE, $this->cookie);

        $this->getCookie($this->html);

        $this->getOnce($this->html);

        $siginURL = self::$API_SIGNIN . $this->once;

        //签到
        $this->get($siginURL, $this->cookie);

        echo $this->html;




    }

    public function get($url, $cookie = null, $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::$header);
        curl_setopt_array($ch, self::$optionsCommon);
        if ($cookie !== null) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        $this->html = curl_exec($ch);
        curl_close($ch);
        if ($this->html === false) {
            exit("GET请求失败！");
        }
        return $this->html;
    }

    public function post($url, $field = array(), $options)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::$header);
        curl_setopt_array($ch, self::$optionsCommon);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($field));
        curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
        $this->html = curl_exec($ch);
        curl_close($ch);
        if ($this->html === false) {
            exit("POST失败！");
        }
        return $this->html;
    }

    /**
     * 获取登陆页面和签到
     * 页面中的校验码once
     */
    private function getOnce($html)
    {
        if (preg_match("/value=\"(\d+).*once/", $html, $matches)) {

        } elseif (preg_match("/once=(\d+)/", $html, $matches)) {

        } else {
            exit("获取once失败");
        }

        $this->once = $matches[1];
        //file_put_contents("once.txt", $this->once);
        return $this->once;
    }


    /**
     * 正则取cookie
     */
    private function getCookie($html)
    {
        if (preg_match_all("/set\-cookie:([^\r\n]*)/i", $html, $matches)) {
            foreach ($matches[1] as $value) {
                $this->cookie .= $value;
            }
            file_put_contents("cookie.txt", $this->cookie);
        }

        return $this->cookie;
    }


    /**
     * 判断是否登陆成功
     * @return bool
     */
    //    private function isLogined()
    //    {
    //        return true;
    //    }


}


?>