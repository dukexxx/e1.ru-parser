<?
/************************************
*****Парсер e1.ru Недвижимости*******
**********dukexxx copy 2013**********
*************icq 556288**************
************************************/
class parce_e1 {

public $tip;
public $proxy;
public $debug;
public $pages = 10;
public $res = '';
public $good_words = array ("собственник",
							"собственик",
							"от собственника",
							"от собственика",
							"не агентство",
							"Не агенство",
							"комиссий нет",
							"комиссии нет",
							"без комиссий",
							"без комиссии",
							"без комиссионных"
							);						
public $bad_words = array  ("по факту заселения",
							"выезд агента",
							"агент с выездом",
							"работаем с выездом",
							"работа с выездом",
							"работаю с выездом",
							"сдается через агенство",
							"сдается через агентство",
							"оплата по факту заключения",
							"агенства 50%",
							"агентства 50%",
							"агентство 50%",
							"агенство 50%",
							"агенства 30%",
							"агентства 30%",
							"агентство 30%",
							"агенство 30%",
							"агенства 40%",
							"агентства 40%",
							"агентство 40%",
							"агенство 40%",
							"оплата при заселении",
							"работает только с выездом",
							"оплата при заселении",
							"агентские 40%",
							"агенские 40%",
							"агентские 30%",
							"агенские 30%",
							"агентские 50%",
							"агенские 50%",
							"комиссия 50%",
							"комиссия 40%",
							"комиссия 30%",
							"агенту 50%",
							"агенту 40%",
							"агенту 30%",
							"работаю по факту",
							"агент.выезд",
							"агента 30%",
							"после заселения",
							"эксклюзивный вариант",
							"услуги посредника",
							"при заключении договора",
							"услуги по факту",
							"агент 30%",
                            "агента 40%"
							);	
	
	public function get_rasdel($p) {	
	    
        $this->tip = ($this->tip)?($this->tip):(1);  
	
        if ($p == 0) {
            $url_razdel = 'http://www.e1.ru/business/realty/index.php?ot=4&bt='.$this->tip.'&nb=&rq=&sb=8&ob=2';
        }
        else {
            $url_razdel = 'http://www.e1.ru/business/realty/index.php?ot=4&bt='.$this->tip.'&nb=&rq=&sb=8&ob=2&p='.$p;
        }
        
	return $url_razdel;
	} 	

	public function clear_cookie() {
	   $c_file = "e1.txt"; 
	   $res = fopen($c_file, "w"); 
	   fclose($res); 	
	}
	
	private function in_number ($value) 
	{
		$number="";
		for ($i=0; $i<strlen($value); $i++) {
			if (ctype_digit($value[$i]))
			$number .= $value[$i];
		}
		return $number;
	}

	public function get($url)
	{
	//clear_cookie();
    if ($this->debug) { print 'Парсим страницу: '.$url.'<br/>'; }
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $url);
    if ($this->proxy) {
	   curl_setopt ($ch, CURLOPT_PROXY, $this->proxy);  
    }   
	curl_setopt ($ch, CURLOPT_VERBOSE, 2); 
	curl_setopt ($ch, CURLOPT_ENCODING, 0); 
	curl_setopt ($ch, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.50'); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt ($ch, CURLOPT_HEADER, 1);
	curl_setopt ($ch, CURLINFO_HEADER_OUT, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt ($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/e1kv.txt'); 
	//curl_setopt ($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/e1kv.txt'); 
	$respost = curl_exec($ch);
	$respost = iconv('cp1251', 'UTF-8', $respost);
	curl_close($ch);
    if (empty($respost)) { print 'Неработающий прокси или проблемы с соединением<br/>'; }
	return $respost; 
	}
	
	public function get_urls($content) {
	$this->res = '';
	
		preg_match_all("#<td><nobr><a href='(.*?)['|]#i", $content, $res_urls);
		
		foreach ($res_urls[1] as $item_url) {
			$this->res[] = 'http://www.e1.ru/business/realty/'.$item_url; 		
		}
		
	return $this->res;
	}
	
	public function get_phone($content) {
	$this->res = '';
		preg_match("/<td>Телефон:<\/td>\s*<td>([^<]*)<\/td>/is", $content, $telsin); 
		$this->res = $this->in_number($telsin[1]);
	return $this->res;	
	}
	
	public function get_params($content) {
	$this->res = Array();
		preg_match("/<td>Дата публикации:<\/td>\s*<td>\s*<b>([^<]*)</is",$content,$data); 
		$this->res['data'] = trim($data[1]);
		preg_match("/<td>Цена:<\/td>\s*<td>\s*<b>([^<]*)</is",$content,$price);
		$this->res['price'] = $this->in_number($price[1]);
		preg_match("/<td>Количество смежных комнат:<\/td>\s*<td>\s*<b>([^<]*)</is",$content,$countsmr);
		$this->res['countsmr'] = trim($countsmr[1]);
		preg_match("/<td width=\"45%\">Кол-во комнат в квартире:<\/td>\s*<td>\s*<b>([^<]*)</is",$content,$countrooms);
		$this->res['countrooms'] = trim($countrooms[1]);
		preg_match("/<td>Общая площадь, кв.м.<\/td>\s*<td>\s*<b>([^<]*)</is",$content,$size);
		$this->res['size'] = trim($size[1]);
		preg_match("/<td>Срок сдачи:<\/td>\s*<td>\s*<b>([^<]*)</is",$content,$deadline);
		$this->res['deadline'] = trim($deadline[1]);
		preg_match("/<td>Коммунальные платежи:<\/td>\s*<td>\s*<b>([^<]*)</is",$content,$ky);
		$this->res['ky'] = trim($ky[1]);
		preg_match("/<td width=\"45%\">Этаж:<br>\s*<\/td>\s*<td>\s*<b>([^<]*)</is",$content,$floor);
		$this->res['floor'] = trim($floor[1]);
		preg_match("/<td>Город:<\/td>\s*<td>\s*<b>([^<]*)</is",$content,$city);
		$this->res['city'] = trim($city[1]);
		preg_match("/<td>Район:<\/td>\s*<td><b>\s*([^<]*)</is",$content,$region);
		$this->res['region'] = trim($region[1]);
		preg_match("/<td>Адрес:<\/td>\s*<td><b>\s*([^<]*)</is",$content,$address);
		$this->res['address'] = trim($address[1]);
		preg_match("/<td height=\"21\">Мебель:<\/td>\s*<td height=\"21\">\s*<b>([^<]*)</is",$content,$mebel);
		$this->res['mebel'] = trim($mebel[1]);
		preg_match('#<td bgcolor="\#bababa"><b>Дополнительные сведения:<\/b><\/td>\s*<\/tr>\s*<tr bgcolor="\#EFEFEF" valign="top">\s*<td width="50%">\s*<p>([^<]*)</#i',$content,$info);
		$this->res['info'] = trim($info[1]);
        $this->res['phone'] = $this->get_phone($content);
		$this->res['agent'] = '<font color=red>'.$this->check_agent($this->res['info']).'</font>';
	    if ($this->debug) { print_r($this->res); }   
	return $this->res;
	}	
	
	protected function check_agent($info) {
		mb_internal_encoding('UTF-8');
		setlocale(LC_ALL, 'ru_RU.UTF-8');
		$info = mb_strtolower($info);
		$agent = '';
		if ($this->debug) { print 'Информация в которой проверяю: '.$info.'<br>'; }
		
		foreach ($this->bad_words as $word) {
			if ($this->debug) { print 'Слово которое проверяю: '.$word.'<br>'; }
			$cnt = substr_count($info, $word);
			if ($this->debug) { print 'Результат проверки: '.$cnt.'<br>'; }
			if ($cnt > 0) {
			$agent = 'да';
			break;			
			}
		}
			
		if ($agent!='да') {
		foreach ($this->good_words as $word) {
			if ($this->debug) { print 'Слово которое проверяю: '.$word.'<br>'; }
			$cnt = substr_count($info, $word);
			if ($this->debug) { print 'Результат проверки: '.$cnt.'<br>'; }
			if ($cnt > 0) {
			$agent = 'собственник';
			break;			
			}
		}
		}
		$agent = ($agent=='')?('?'):($agent);	
	return $agent;
	}	
	
}


$e1 = new parce_e1; 
//настройки
$pages = 3;     //колличество страниц которое будет пройдено
$e1->tip = 1;   //тип (1 - квартиры, 2 - комнаты)
$e1->proxy = '85.223.220.198:80'; // прокси для соединения , если парсить напрямую то false
$e1->debug = 0; //режим отладки (0, 1) 
//конец настроек

for ($p=0; $p<($pages); $p++) {

$mainurl = $e1->get_rasdel($p);
$maincontent = $e1->get($mainurl);
$urls = $e1->get_urls($maincontent);

	foreach ($urls as $link) {
	    print 'Ссылка на страницу: <a href="'.$link.'">'.$link.'</a><br/>';   
		$incont = $e1->get($link);
		//$phone = $e1->get_phone($incont);  //при необходимости можно получить телефон отдельно
		$params = $e1->get_params($incont);  //получаем параметры объявления
		/*
		или другие действия 
		*/		
		foreach ($params as $param => $value) {
		print '<b>'.$param.'</b> '.$value.'<br>';
		}
		print '<hr>';
	}

}
?>
