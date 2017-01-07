<?php
Class HttpRequest
{
    public $Ajax;
    public $Alwaus302;
    public $Referer;

    private $connecttimeout; //Время ожидания подключения
    private $timeout;        //Время ожидания получения данных
    private $ArrayCookies;   //Массив Cookie
    private $LineCookies;    //Cookie выстроенные в строку
    private $inbuf;          //Ответ сервера
    private $proxy;          //Прокси
	private $lastUrl;        //Содержит последний URL по которому был переход

//--------------------------------------------------------------------
//Конструктор   
    function __construct ($proxy = NULL) {
        $this->connecttimeout = 60;
        $this->timeout        = 60;
        $this->LineCookies    = "";
        $this->ArrayCookies   = array();
        $this->Ajax           = false;
        $this->Alwaus302      = true;
        //Установка прокси
        $this->proxy = $proxy;
        $this->Referer = NULL;
    }
//--------------------------------------------------------------------
//Диструктор
    function __destruct(){
        $this->ArrayCookies = NULL;
    }
//--------------------------------------------------------------------   
//Очистка Cookies
    public function ClearCookies(){
        $this->LineCookies  = "";
        $this->ArrayCookies = NULL;
        $this->ArrayCookies = array();
    }
//--------------------------------------------------------------------
//Вернуть массив прокси
    public function GetArrayCookies(){
        return $this->ArrayCookies;
    }
//--------------------------------------------------------------------
//Установить прокси
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }
//--------------------------------------------------------------------
//Выключить прокси
    public function unsetProxy()
    {
        $this->proxy = NULL;
    }
//---------------------------------------------------------------------
//Тест прокси
    public function testProxy($url, $value=NULL)
    {
        $inb = $this->Get($url);
        if($value == NULL)
        {
            if(strlen($inb) > 10) return true;
            else                  return false;
        }
        else
        {
            if(strstr($inb, $value)) return true;
            else                     return false;
        }
    }
//--------------------------------------------------------------------
    public function Get() {
	    
        //Получаем все аргументы функции
        $args = func_get_args();
		$this->lastUrl = $args[0];
        //Строим строку Cookie
        $this->WriteCookies($args[0]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:10.0.2) Gecko/20120216 Firefox/10.0.2 SeaMonkey/2.7.2');
        curl_setopt($ch, CURLOPT_URL, $args[0]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_COOKIE, $this->LineCookies);
        //REFERER
        if($this->Referer != NULL)
        {
            curl_setopt($ch, CURLOPT_REFERER, $this->Referer);
            $this->Referer = NULL;
        }
        //Прокси
        if($this->proxy !== NULL)
        {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }

        $this->inbuf = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        //Считываем Cookie
        $this->ReadCookies($args[0]);

        //Проверяем 301, 302 переходов
        if (($http_code == 301 || $http_code == 302) && $this->Alwaus302 == true)
        {
            return $this->Redirect302();
        }
        return $this->inbuf;
    }
//--------------------------------------------------------------------
    public function Post() {
        //Получаем все аргументы функции
        $args = func_get_args();
		$this->lastUrl = $args[0];
        //Строим строку Cookie
        $this->WriteCookies($args[0]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:10.0.2) Gecko/20120216 Firefox/10.0.2 SeaMonkey/2.7.2');
        curl_setopt($ch, CURLOPT_URL, $args[0]);
        curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args[1]);
        curl_setopt($ch, CURLOPT_COOKIE, $this->LineCookies);
        //REFERER
        if($this->Referer != NULL)
        {
            curl_setopt($ch, CURLOPT_REFERER, $this->Referer);
            $this->Referer = NULL;
        }
        //Прокси
        if($this->proxy !== NULL)
        {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }
        if($this->Ajax == true) { $this->Ajax =false; curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest")); }

        $this->inbuf = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        //Считываем Cookie
        $this->ReadCookies($args[0]);

        //Проверяем 302 переход
        if (($http_code == 301 || $http_code == 302) && $this->Alwaus302 == true)
        {
            return $this->Redirect302();
        }

        return $this->inbuf;
    }
//--------------------------------------------------------------------
//Копирование одних кук с одного доменного пространства в другое доменное пространство
//Первый аргумент-домен источник куков
//ВТорой домен-назначение куков
    public  function CopyCookiesFromDomainToDomain(){
        $args = func_get_args();
        $domainSource = strtolower($args[0]);
        $domainDest   = strtolower($args[1]);
        if(strlen($domainSource) < 1 || strlen($domainDest) < 1) return;
        if(count($this->ArrayCookies[$domainSource]) < 1) return;
        foreach($this->ArrayCookies[$domainSource] as $k=>$v)
        {
            $this->ArrayCookies[$domainDest][$k] = $v;
        }
    }
//--------------------------------------------------------------------
//Спарсить домен с строки куки
    private function GetDomainFromCookieLine(){
        $args = func_get_args();
        $buff = strtolower($args[0]);
        if(!strstr($buff, "domain=")) return "";
        if(strstr($buff, "domain=."))
        {
            $buff = strstr($buff, "domain=.");
            $offset ="domain=.";
        }
        else
        {
            $buff = strstr($buff, "domain=");
            $offset ="domain=";
        }
        $posEnd = strpos($buff, ";");
        if($posEnd == 0)
        {
            //$posEnd = strpos($buff, "\r\n");
            $posEnd = strlen($buff);
        }
        return substr($buff, strlen($offset), $posEnd - strlen($offset));
    }
//--------------------------------------------------------------------
//Взять домен с URL
    private function GetDomainFromURL(){
        $args = func_get_args();
        $buff = strtolower($args[0]);
        if(!strstr($buff, ".")) return"";
        if(strstr($buff, "http://")) $buff = substr($buff, strlen("http://"));
        if(strstr($buff, "www."))    $buff = substr($buff, strlen("www."));
        $posEnd = strpos($buff, "/");
        if($posEnd > 0)
            return substr($buff, 0, $posEnd);
        else
            return $buff;
    }
//--------------------------------------------------------------------  
//Функция чтения cookie
    private function ReadCookies() {
        $args = func_get_args();
        $url  = strtolower($args[0]);
        $buf = $this->inbuf;

        while(strstr($buf, "Set-Cookie: "))
        {
            $buf = strstr($buf, "Set-Cookie: ");
            $posEnd = strpos($buf, "\r\n");
            $LineCookies = substr($buf, strlen("Set-Cookie: "), $posEnd - strlen("Set-Cookie: "));
            //Определяем домен
            $domain = $this->GetDomainFromCookieLine($LineCookies);
            if(!strstr($domain, ".")) $domain = $this->GetDomainFromURL($url);
            //Разбиваем куки на составные части
            $tok = strtok($LineCookies, ";");
            while ($tok !== false)
            {
                if(strstr($tok, "="))
                {
                    $item = explode("=", $tok);
                    $item[0] = trim($item[0], " ");
                    $this->ArrayCookies[$domain][$item[0]] = $item[1];
                }
                $tok = strtok(";");
            }
            //Делаем смещение
            $buf = strstr($buf, "et-Cookie: ");
        }
    }
//--------------------------------------------------------------------
//Функция записи cookie
    private function WriteCookies() {
        $args = func_get_args();
        $url  = strtolower($args[0]);
        //Определяем домен
        $domain = $this->GetDomainFromURL($url);
        $this->LineCookies ="";

        if(count($this->ArrayCookies[$domain]) < 1) return;
        foreach ($this->ArrayCookies[$domain] as $k=>$v)
        {
            $this->LineCookies = $this->LineCookies."$k=$v; ";
        }
        //Убираем в конце "; "
        if(strlen($this->LineCookies) > 3)
            $this->LineCookies = substr($this->LineCookies, 0, -2);
    }
//--------------------------------------------------------------------
//302 Moved Temporarily
    private function Redirect302() {
        //Получаем домен из текста 302
        $domain = $this->GetDomainFromCookieLine($this->inbuf);
		if(strlen($domain) < 1) {
		    $domain = $this->GetDomainFromURL($this->lastUrl);
			if(strlen($domain) < 1)
			    return "";
		}
        //Парсим Location
        $buff = strstr($this->inbuf, "Location: ");
        if(strlen($buff) < 1) return "";
        $posEnd = strpos($buff, "\r\n");
        $len = strlen("Location: ");
        $location = substr($buff, $len, $posEnd - $len);
		if(!strstr($location, "http://"))
            $url = "http://$domain"."$location"; 
        else
		    $url = $location;
		
        return $this->Get($url);
    }
//--------------------------------------------------------------------

}
?>