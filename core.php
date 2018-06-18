<?php

class Core
{

	public function loadView($view_name, array $z = array(), $store = false) {

		$z['route_vars'] = unserialize(ROUTE_VARS);

	    extract($z);
	    
	    ob_start();

	    require VIEWS_PATH.'/'.$view_name.'.php';
	    
	    if ($store)
	    	return ob_get_clean();
	    else
	    	ob_end_flush();
	}

	public function domainValidName($domain_name) {
		
		return preg_match('/^[-a-z0-9]+\.[a-z]{2,16}(|\.[a-z]{2,6})$/', strtolower($domain_name));
		 
	}

	public function blogArticleNameValidate($str) {
		
		return !preg_match('/[^a-z0-9\-\:\|_]/s', strtolower($str));
		 
	}

	public function show404($domain = '', $addToDB = false)
	{ 

		//if ($addToDB === true)
	    	//$this -> send_search_request($domain);

	    //header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
	    //echo '<h1>Not Found</h1> <p>The requested URL was not found on this server.</p>';
	    //file_put_contents('/home/admin/web/engine.files/public_html/404errors.txt', $domain."\n", FILE_APPEND | LOCK_EX);
	      
	    header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: /");       
	} 

	public function send_search_request($domain)
	{
	    $time = date('h-m').' ';
	    $secret_word = 'ffsdf342r45r';
	    $password = substr(md5($time.$secret_word),rand(0,26),5);

	    $url = 'http://app.netho.me/search.php?domain='.$domain.'&p='.$password.'&option=404';
	    exec('curl --referer http://netho.me "'.$url.'" > /dev/null &', $output, $return_var);
	}

	public function get_geo($domain_ip, $reader)
	{
	    //echo ' GETTING GEO <br>';
	    $domain_data = array();

	    try {
	        $record = $reader->city($domain_ip);

	        $domain_data['country_code'] = (string)$record->country->isoCode; // 'US'
	        $domain_data['country_name'] = (string)$record->country->name; // 'United States'
	        $domain_data['state_name'] = (string)$record->mostSpecificSubdivision->name; // 'Minnesota'
	        $domain_data['state_code'] = (string)$record->mostSpecificSubdivision->isoCode; // 'MN'
	        $domain_data['city'] = (string)$record->city->name; // 'Minneapolis'
	        $domain_data['postal_code'] = (string)$record->postal->code; // '55455'
	        $domain_data['latitude'] = (string)$record->location->latitude; // 44.9733
	        $domain_data['longitude'] = (string)$record->location->longitude; // -93.2323
	    } catch (Exception $e) {
	        
	    }

	    return $domain_data;

	}

	public function time_elapsed_string($datetime, $full = false) 
	{

	    $now = new DateTime;
	    $ago = new DateTime($datetime);
	    $diff = $now->diff($ago);

	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;

	    $string = array(
	        'y' => 'year',
	        'm' => 'month',
	        'w' => 'week',
	        'd' => 'day',
	        'h' => 'hour',
	        'i' => 'minute',
	        's' => 'second',
	    );
	    foreach ($string as $k => &$v) {
	        if ($diff->$k) {
	            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
	        } else {
	            unset($string[$k]);
	        }
	    }

	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' ago' : 'just now';
	}




	public function formTitle($z)
	{

		$title_postfix = '';

		//$z['name'] = $z['name'].' ('.$this->lat2rus($z['name']).')';

		if (isset($z['h2']))
		{	
			
			if (stristr($z['h2'], $z['name']))
       			$z['h2'] = str_replace(array($z['name'],'www.'.$z['name']), '', $z['h2']);

			$z['h2'] = $this->sanitizeString($z['h2']);
			$z['h2'] = preg_replace('#\&.+;#', '', $z['h2']);
			$z['h2'] = preg_replace('#\s+#', ' ', $z['h2']);

			$str = explode( "\n", wordwrap($z['h2'], 60));
       		$z['h2'] = $str[0];

       		if (stristr($z['h2'], $z['name']))
				$z['h2'] = str_ireplace($z['name'], '', $z['h2']);

			$z['title'] = str_replace(array(':',';','.',',','!','?','@','"',"'"), '', $z['h2']);

		}elseif (isset($z['h1']))
		{	
			
			if (stristr($z['h1'], $z['name']))
       			$z['h1'] = str_replace(array($z['name'],'www.'.$z['name']), '', $z['h1']);

			$z['h1'] = $this->sanitizeString($z['h1']);
			$z['h1'] = preg_replace('#\&.+;#', '', $z['h1']);
			$z['h1'] = preg_replace('#\s+#', ' ', $z['h1']);

			$str = explode( "\n", wordwrap($z['h1'], 60));
       		$z['h1'] = $str[0];

       		if (stristr($z['h1'], $z['name']))
				$z['h1'] = str_ireplace($z['name'], '', $z['h1']);

			$z['title'] = str_replace(array(':',';','.',',','!','?','@','"',"'"), '', $z['h1']);

		}elseif (isset($z['description']))
		{	
			
			if (stristr($z['description'], $z['name']))
       			$z['description'] = str_replace(array($z['name'],'www.'.$z['name']), '', $z['description']);

			$z['description'] = $this->sanitizeString($z['description']);
			$z['description'] = preg_replace('#\&.+;#', '', $z['description']);
			$z['description'] = preg_replace('#\s+#', ' ', $z['description']);

			$str = explode( "\n", wordwrap($z['description'], 60));
       		$z['description'] = $str[0];

       		if (stristr($z['description'], $z['name']))
				$z['description'] = str_ireplace($z['name'], '', $z['description']);

			$z['title'] = str_replace(array(':',';','.',',','!','?','@','"',"'"), '', $z['description']);

		}elseif (isset($z['title']))
		{	
			
			if (stristr($z['title'], $z['name']))
       			$z['title'] = str_replace(array($z['name'],'www.'.$z['name']), '', $z['title']);

			$z['title'] = $this->sanitizeString($z['title']);
			$z['title'] = preg_replace('#\&.+;#', '', $z['title']);
			$z['title'] = preg_replace('#\s+#', ' ', $z['title']);

			$str = explode( "\n", wordwrap($z['title'], 60));
       		$z['title'] = $str[0];

       		if (stristr($z['title'], $z['name']))
				$z['title'] = str_ireplace($z['name'], '', $z['title']);

			$z['title'] = str_replace(array(':',';','.',',','!','?','@','"',"'"), '', $z['title']);

		}

		$www_name = $z['name'];

		$ty = $this->getTypos($z['name']);

		if ($z['alexa_rank'] > 0 or $z['sweb_traffic_volume'] > 0)
			$title_postfix = str_replace('{DOMAIN}', $www_name, TITLE_POSTFIX_TRAFFIC).'. Рейтинг: '.$z['alexa_rank'];
		else
			$title_postfix = str_replace('{DOMAIN}', $www_name, TITLE_POSTFIX_DEFAULT).' ('.$ty[3].')';

		if (isset($z['title']) and strlen($z['title'])>2)
		{
			$title = $z['name'].' отзывы - '.$z['title'];

			if (preg_match('/[а-яА-Я]/u', $z['title']))
			{

				$rusname = $this->lat2rus(preg_replace('/\..+/u', '', $z['name']));
				if (!stristr(mb_strtolower($title), mb_strtolower($rusname)))
					$title.=' '.$rusname;
			}
		}
		else
			$title = $z['name'].' отзывы. '.ucfirst($title_postfix);
			//$title = @str_replace('{DOMAIN}', ucfirst($z['name']), DOMAIN_PAGE_TITLE.$title_postfix);

		if ($z['show_full_version'] === true)
		    $title = (
		    			isset($z['title']) 
		    			? str_replace(array('{DOMAIN}','{TITLE}'), array($z['name'], $z['title']), FULL_REVIEW_TITLE_V1) 
		    			: str_replace('{DOMAIN}',$z['name'],FULL_REVIEW_TITLE_V2)
		    		 );

		return ucfirst($title);
	}
	public function lat2rus ($str)
	{

        
		$lat = [
            'a','b','c','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
            'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
            'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
            'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya',
            'x','w'
        ];

        $cyr = [
            'а','б','к','в','г','д','е','ё','ж','з','и','и','к','л','м','н','о','п',
            'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
            'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
            'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я', 'кс','в'
        ];
        
        
        return str_replace($lat, $cyr, $str);
	}
	public function cleanstr($str)
	{
		return  preg_replace('/[^а-яА-ЯЁёa-zA-Z0-9_\s\.\-:\?\!\,\'\"\-\|\&]/u', '', $str);
	}

	public function cleanstr_descr($str)
	{
		return  preg_replace('/[^а-яА-ЯЁёa-zA-Z0-9_\s\.\-:\?\!\,\'\"\-\|\&]/u', '', $str);
	}

	public function formDescription($array = array()) 
	{


		/*

		 [title] => Welcome adventureoutaspen.org - BlueHost.com
	    [description] => 
	    [h1] => Affordable, ReliableWeb Hosting Solutions
	    [h2] => 
	    [h3] => 

	    */

	    $str = '';

		if (isset($array['description']))
			$str.=$array['description'];

		if (isset($array['h1']))
			$str.=' '.$array['h1'];

		if (isset($array['h2']))
			$str.=' '.$array['h2'];

		if (isset($array['h3']))
			$str.=' '.$array['h3'];

		if (strlen($str)>=10)
			return 'www.'.$array['name'].' - '.$str;
		else
			return 'www.'.$array['name'].DESCRIPTION_NO_DESCRIPTION;
		
		

		$result_string = '';

		if (isset($array['load_time']))
			$result_string .= ucfirst(str_replace(array('{LOADTIME}','{DOMAIN}'), array(round($array['load_time'],2), $array['name']), LOAD_TIME.' '));
		

		if (isset($array['sweb_traffic_volume'])) {
			$array['sweb_traffic_volume'] = round($array['sweb_traffic_volume']*0.79,0);

			if (isset($array['alexa_rank'])) {
				if ($array['alexa_rank']<10000)
					$result_string .= ucfirst(str_replace('{TRAFVOLUME}', $array['sweb_traffic_volume'], HIGHTRAFFIC.' '));

				elseif ($array['alexa_rank']<100000)
					$result_string .= ucfirst(str_replace('{TRAFVOLUME}', $array['sweb_traffic_volume'], MIDTRAFFIC.' '));

				elseif ($array['alexa_rank']>100000)
					$result_string .= ucfirst(str_replace('{TRAFVOLUME}', $array['sweb_traffic_volume'], LOWTRAFFIC.' '));

			}
		} else {

			if (isset($array['alexa_rank'])) {
				$result_string .= str_replace('{ALEXA}', $array['alexa_rank'], ALEXATEXT.' ');
			}
		}

		if (isset($array['country_name']) and strlen($array['country_name'])>0)
			$result_string .= ucfirst(str_replace(array('{DOMAIN}', '{SERVER_COUNTRY}'), array($array['name'], $array['country_name']), SERVER_COUNTRY.' '));

		if (isset($array['city']) and strlen($array['city'])>0)
			$result_string .= ucfirst(str_replace(array('{DOMAIN}', '{SERVER_CITY}'), array($array['name'], $array['city']), SERVER_CITY.' '));

		if (isset($array['register_date']))
			$result_string .= ucfirst(str_replace(array('{DOMAIN}', '{REGISTERED_AGO}'), array($array['name'], $this->time_elapsed_string($array['register_date'])), REGISTERED_AGO.' '));

		return $result_string;
	}

	public function validateForAdsense($domain_info) 
	{

		if (!is_array($domain_info))
			return false;

		$show_adsense_code = true;

		// more strict for checking as WORDS in title, descr and h tags
		$content_check_words = 'poker|fuck|blackjack|casino|slave|nudity|nude|teens|spank|jizz|busty|rape|bangbros|bang-bros|szex|sex|porn|xxx|bukkake|ass|pussy|tits|fisting|hentai|lesbian|milf|anal|ebony|gay|threesome|dick|squirt|creampie|bondage|shemale|orgy|masturbation|bbw|penetration|handjob|hardcore|blowjob|cumshots|fetish|pornstar|pizde|porno|pizda|ţâţe|порно|сиськи|член|жопа|проститутки|индивидуалки|хуй|пизда|минет|сиська|выебать|трахать|казино|покер|tetas|pechos|culo|seins|topless|naked';

		// less strict and will be checked as a sequence of lettters, not stand-alone words {removed from this list: ass}
		$domain_name_check_words = 'slave|teens|spank|jizz|busty|rape|nudity|nude|prostitutki|bangbros|bang-bros|szex|sex|porn|xxx|pussy|tits|fisting|hentai|lesbian|milf|anal|gay|threesome|dick|squirt|creampie|bondage|shemale|orgy|masturbation|bbw|penetration|handjob|hardcore|blowjob|bukkake|cumshots|fetish|pornstar|topless|naked';

		/*

		$website = $_SERVER['HTTP_HOST'];
		$no_www_website = @str_replace('www.', '', $website);

		// list of websites where it is ok to show adsense
		$show_adsense_id = array('google.com'); 

		if (!in_array($no_www_website, $show_adsense_id))
			$show_adsense_code = 0;

		*/

		// checking text content

		if ($show_adsense_code == true)
		{

			$str = '';

			if (isset($domain_info['title'])) 
				$str .= $domain_info['title'];

			if (isset($domain_info['description'])) 
				$str .= ' '.$domain_info['description'];

			if (isset($domain_info['h1'])) 
				$str .= ' '.$domain_info['h1'];

			if (isset($domain_info['h2'])) 
				$str .= ' '.$domain_info['h2']; 

			if (isset($domain_info['h3'])) 
				$str .= ' '.$domain_info['h3'];

			if (preg_match('/\b('.$content_check_words.')\b/is', $str) or preg_match('/('.$domain_name_check_words.')/is', $domain_info['name']))
				$show_adsense_code = false;

		}

		return $show_adsense_code;
	}

	
	public function tagReplacer($str, $z, $findAlso = array(), $replaceAlso = array()) 
	{

		return @str_replace( array_merge(array('{PROJECT_NAME}','{DOMAIN}','{IP}'), $findAlso), array_merge(array(PROJECT_NAME, $z['name'], $z['ip']), $replaceAlso), $str); // @ is here because usually not all elements of the search/replace arrays will be given
	}


	public function toolsTagReplacer($tools_arr, $z)
	{

		foreach ($tools_arr as $key => $value) {

			$tools_arr[$key] = str_replace(array('{PROJECT_NAME}','{DOMAIN}','{IP}'), array(PROJECT_NAME, $z['name'], $z['ip']), $tools_arr[$key]);
		}

		return $tools_arr;
	}

	public function get($url, $referer = false, $proxy = false, $loginpassw = false)
	{
	    
	    //$proxy = false;

	    $ch = curl_init();
	    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
	    if ($referer !== false) curl_setopt($ch, CURLOPT_REFERER, $referer);
	    curl_setopt( $ch, CURLOPT_URL, $url );
	    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	    curl_setopt( $ch, CURLOPT_ENCODING, "" );
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
	    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

	    if ($proxy !== false)
	    {
	    	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 20); 
	    	curl_setopt( $ch, CURLOPT_TIMEOUT, 30); 
	    	curl_setopt($ch, CURLOPT_PROXY, $proxy);
	        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

	        if ($loginpassw !== false) 
	                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $loginpassw);
	    }
	    else 
	    {
	    	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10); 
	   		curl_setopt( $ch, CURLOPT_TIMEOUT, 20); 
	    }
	 
	    $content = curl_exec( $ch );
	    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    $curl_info = curl_getinfo($ch);
	    $error = curl_error($ch);

	    if ($error) // ошибка curl, например таймаут
	    {
	        //echo 'CURL error occurred during the request: ' . $error;
	        //echo "\n";
	        return false;
	    } elseif ($http_code<200 || $http_code>=300) // код возврата не 200
	    {
	        //echo 'HTTP error ' . $http_code. ' occurred during the request';
	        //echo "\n";
	        //var_dump(curl_getinfo( $ch )); // там все заголовки и другая отладочная информация
	        return false;
	    } else
	    {   
	        $result['content'] = $content;
	        $result['curl_info'] = $curl_info;
	        
	        return ($result);
	    }

	}

	public function sanitizeString($var)
	{
		$var = stripslashes($var);
		$var = strip_tags($var);
		$var = htmlentities($var);
		return $var;
	}

	public function mb_ucfirst($string, $encoding)
	{
	    $strlen = mb_strlen($string, $encoding);
	    $firstChar = mb_substr($string, 0, 1, $encoding);
	    $then = mb_substr($string, 1, $strlen - 1, $encoding);
	    return mb_strtoupper($firstChar, $encoding) . mb_strtolower($then);
	}

	public function getTypos($str) {
  
		$typosArr = array();

		$strArr = str_split($str);

	      //Proximity of keys on keyboard
		$arr_prox = array();
		$arr_prox['a'] = array('q', 'w', 'z', 'x');
		$arr_prox['b'] = array('v', 'f', 'g', 'h', 'n');
		$arr_prox['c'] = array('x', 's', 'd', 'f', 'v');
		$arr_prox['d'] = array('x', 's', 'w', 'e', 'r', 'f', 'v', 'c');
		$arr_prox['e'] = array('w', 's', 'd', 'f', 'r');
		$arr_prox['f'] = array('c', 'd', 'e', 'r', 't', 'g', 'b', 'v');
		$arr_prox['g'] = array('r', 'f', 'v', 't', 'b', 'y', 'h', 'n');
		$arr_prox['h'] = array('b', 'g', 't', 'y', 'u', 'j', 'm', 'n');
		$arr_prox['i'] = array('u', 'j', 'k', 'l', 'o');
		$arr_prox['j'] = array('n', 'h', 'y', 'u', 'i', 'k', 'm');
		$arr_prox['k'] = array('u', 'j', 'm', 'l', 'o');
		$arr_prox['l'] = array('p', 'o', 'i', 'k', 'm');
		$arr_prox['m'] = array('n', 'h', 'j', 'k', 'l');
		$arr_prox['n'] = array('b', 'g', 'h', 'j', 'm');
		$arr_prox['o'] = array('i', 'k', 'l', 'p');
		$arr_prox['p'] = array('o', 'l');
		$arr_prox['q'] = array('q');
		$arr_prox['r'] = array('e', 'd', 'f', 'g', 't');
		$arr_prox['s'] = array('q', 'w', 'e', 'z', 'x', 'c');
		$arr_prox['t'] = array('r', 'f', 'g', 'h', 'y');
		$arr_prox['u'] = array('y', 'h', 'j', 'k', 'i');
		$arr_prox['v'] = array('', 'c', 'd', 'f', 'g', 'b');    
		$arr_prox['w'] = array('q', 'a', 's', 'd', 'e');
		$arr_prox['x'] = array('z', 'a', 's', 'd', 'c');
		$arr_prox['y'] = array('t', 'g', 'h', 'j', 'u');
		$arr_prox['z'] = array('x', 's', 'a');
		$arr_prox['1'] = array('q', 'w');
		$arr_prox['2'] = array('q', 'w', 'e');
		$arr_prox['3'] = array('w', 'e', 'r');
		$arr_prox['4'] = array('e', 'r', 't');
		$arr_prox['5'] = array('r', 't', 'y');
		$arr_prox['6'] = array('t', 'y', 'u');
		$arr_prox['7'] = array('y', 'u', 'i');
		$arr_prox['8'] = array('u', 'i', 'o');
		$arr_prox['9'] = array('i', 'o', 'p');
		$arr_prox['0'] = array('o', 'p');
		$arr_prox['.'] = array('.');
		$arr_prox['-'] = array('-');

		foreach($strArr as $key=>$value){
			$temp = $strArr;
			foreach ($arr_prox[$value] as $proximity){
				$temp[$key] = $proximity;
				$typosArr[] = join("", $temp);
			}
		}   

	      return $typosArr;
	}
	public $countriesRegions = array
		(
			'Afghanistan'=>'Southern Asia',
			'Armenia'=>'Western Asia',
			'Azerbaijan'=>'Western Asia',
			'Bahrain'=>'Western Asia',
			'Bangladesh'=>'Southern Asia',
			'Bhutan'=>'Southern Asia',
			'Brunei Darussalam'=>'South-Eastern Asia',
			'Cambodia'=>'South-Eastern Asia',
			'China'=>'Eastern Asia',
			'Hong Kong'=>'Eastern Asia',
			'China, Hong Kong SAR'=>'Eastern Asia',
			'China, Macao SAR'=>'Eastern Asia',
			'Cyprus'=>'Western Asia',
			'Dem. People\'s Republic of Korea'=>'Eastern Asia',
			'Georgia'=>'Western Asia',
			'India'=>'Southern Asia',
			'Indonesia'=>'South-Eastern Asia',
			'Iran (Islamic Republic of)'=>'Southern Asia',
			'Iraq'=>'Western Asia',
			'Israel'=>'Western Asia',
			'Japan'=>'Eastern Asia',
			'Jordan'=>'Western Asia',
			'Kazakhstan'=>'Central Asia',
			'Kuwait'=>'Western Asia',
			'Kyrgyzstan'=>'Central Asia',
			'Lao People\'s Democratic Republic'=>'South-Eastern Asia',
			'Lebanon'=>'Western Asia',
			'Malaysia'=>'South-Eastern Asia',
			'Maldives'=>'Southern Asia',
			'Mongolia'=>'Eastern Asia',
			'Myanmar'=>'South-Eastern Asia',
			'Nepal'=>'Southern Asia',
			'Oman'=>'Western Asia',
			'Pakistan'=>'Southern Asia',
			'Philippines'=>'South-Eastern Asia',
			'Qatar'=>'Western Asia',
			'Republic of Korea'=>'Eastern Asia',
			'Saudi Arabia'=>'Western Asia',
			'Singapore'=>'South-Eastern Asia',
			'Sri Lanka'=>'Southern Asia',
			'State of Palestine'=>'Western Asia',
			'Syrian Arab Republic'=>'Western Asia',
			'Tajikistan'=>'Central Asia',
			'Thailand'=>'South-Eastern Asia',
			'Timor-Leste'=>'South-Eastern Asia',
			'Turkey'=>'Western Asia',
			'Turkmenistan'=>'Central Asia',
			'United Arab Emirates'=>'Western Asia',
			'Uzbekistan'=>'Central Asia',
			'Viet Nam'=>'South-Eastern Asia',
			'Yemen'=>'Western Asia',
			'Algeria'=>'Northern Africa',
			'Angola'=>'Middle Africa',
			'Benin'=>'Western Africa',
			'Botswana'=>'Southern Africa',
			'Burkina Faso'=>'Western Africa',
			'Burundi'=>'Eastern Africa',
			'Cabo Verde'=>'Western Africa',
			'Cameroon'=>'Middle Africa',
			'Central African Republic'=>'Middle Africa',
			'Chad'=>'Middle Africa',
			'Comoros'=>'Eastern Africa',
			'Congo'=>'Middle Africa',
			'Cote d\'Ivoire'=>'Western Africa',
			'Democratic Republic of the Congo'=>'Middle Africa',
			'Djibouti'=>'Eastern Africa',
			'Egypt'=>'Northern Africa',
			'Equatorial Guinea'=>'Middle Africa',
			'Eritrea'=>'Eastern Africa',
			'Ethiopia'=>'Eastern Africa',
			'Gabon'=>'Middle Africa',
			'Gambia'=>'Western Africa',
			'Ghana'=>'Western Africa',
			'Guinea'=>'Western Africa',
			'Guinea-Bissau'=>'Western Africa',
			'Kenya'=>'Eastern Africa',
			'Lesotho'=>'Southern Africa',
			'Liberia'=>'Western Africa',
			'Libya'=>'Northern Africa',
			'Madagascar'=>'Eastern Africa',
			'Malawi'=>'Eastern Africa',
			'Mali'=>'Western Africa',
			'Mauritania'=>'Western Africa',
			'Mauritius'=>'Eastern Africa',
			'Mayotte'=>'Eastern Africa',
			'Morocco'=>'Northern Africa',
			'Mozambique'=>'Eastern Africa',
			'Namibia'=>'Southern Africa',
			'Niger'=>'Western Africa',
			'Nigeria'=>'Western Africa',
			'Rwanda'=>'Eastern Africa',
			'Réunion'=>'Eastern Africa',
			'Saint Helena'=>'Western Africa',
			'Sao Tome and Principe'=>'Middle Africa',
			'Senegal'=>'Western Africa',
			'Seychelles'=>'Eastern Africa',
			'Sierra Leone'=>'Western Africa',
			'Somalia'=>'Eastern Africa',
			'South Africa'=>'Southern Africa',
			'South Sudan'=>'Eastern Africa',
			'Sudan'=>'Northern Africa',
			'Swaziland'=>'Southern Africa',
			'Togo'=>'Western Africa',
			'Tunisia'=>'Northern Africa',
			'Uganda'=>'Eastern Africa',
			'United Republic of Tanzania'=>'Eastern Africa',
			'Western Sahara'=>'Northern Africa',
			'Zambia'=>'Eastern Africa',
			'Zimbabwe'=>'Eastern Africa',
			'Albania'=>'Southern Europe',
			'Andorra'=>'Southern Europe',
			'Austria'=>'Western Europe',
			'Belarus'=>'Eastern Europe',
			'Belgium'=>'Western Europe',
			'Bosnia and Herzegovina'=>'Southern Europe',
			'Bulgaria'=>'Eastern Europe',
			'Channel Islands'=>'Northern Europe',
			'Croatia'=>'Southern Europe',
			'Czech Republic'=>'Eastern Europe',
			'Denmark'=>'Northern Europe',
			'Estonia'=>'Northern Europe',
			'Faeroe Islands'=>'Northern Europe',
			'Finland'=>'Northern Europe',
			'France'=>'Western Europe',
			'Germany'=>'Western Europe',
			'Gibraltar'=>'Southern Europe',
			'Greece'=>'Southern Europe',
			'Guernsey'=>'Northern Europe',
			'Holy See'=>'Southern Europe',
			'Hungary'=>'Eastern Europe',
			'Iceland'=>'Northern Europe',
			'Ireland'=>'Northern Europe',
			'Isle of Man'=>'Northern Europe',
			'Italy'=>'Southern Europe',
			'Jersey'=>'Northern Europe',
			'Latvia'=>'Northern Europe',
			'Liechtenstein'=>'Western Europe',
			'Lithuania'=>'Northern Europe',
			'Luxembourg'=>'Western Europe',
			'Malta'=>'Southern Europe',
			'Monaco'=>'Western Europe',
			'Montenegro'=>'Southern Europe',
			'Netherlands'=>'Western Europe',
			'Norway'=>'Northern Europe',
			'Poland'=>'Eastern Europe',
			'Portugal'=>'Southern Europe',
			'Republic of Moldova'=>'Eastern Europe',
			'Romania'=>'Eastern Europe',
			'Russian Federation'=>'Eastern Europe',
			'Russia'=>'Eastern Europe',
			'San Marino'=>'Southern Europe',
			'Sark'=>'Northern Europe',
			'Serbia'=>'Southern Europe',
			'Slovakia'=>'Eastern Europe',
			'Slovenia'=>'Southern Europe',
			'Spain'=>'Southern Europe',
			'Svalbard and Jan Mayen Islands'=>'Northern Europe',
			'Sweden'=>'Northern Europe',
			'Switzerland'=>'Western Europe',
			'The former Yugoslav Republic of Macedonia'=>'Southern Europe',
			'Ukraine'=>'Eastern Europe',
			'United Kingdom of Great Britain'=>'Northern Europe',
			'Åland Islands'=>'Northern Europe',
			'Anguilla'=>'Caribbean',
			'Antigua and Barbuda'=>'Caribbean',
			'Aruba'=>'Caribbean',
			'Bahamas'=>'Caribbean',
			'Barbados'=>'Caribbean',
			'Belize'=>'Central America',
			'Bermuda'=>'Northern America',
			'Bonaire, Saint Eustatius and Saba'=>'Caribbean',
			'British Virgin Islands'=>'Caribbean',
			'Canada'=>'Northern America',
			'Cayman Islands'=>'Caribbean',
			'Costa Rica'=>'Central America',
			'Cuba'=>'Caribbean',
			'Curaçao'=>'Caribbean',
			'Dominica'=>'Caribbean',
			'Dominican Republic'=>'Caribbean',
			'El Salvador'=>'Central America',
			'Greenland'=>'Northern America',
			'Grenada'=>'Caribbean',
			'Guadeloupe'=>'Caribbean',
			'Guatemala'=>'Central America',
			'Haiti'=>'Caribbean',
			'Honduras'=>'Central America',
			'Jamaica'=>'Caribbean',
			'Martinique'=>'Caribbean',
			'Mexico'=>'Central America',
			'Montserrat'=>'Caribbean',
			'Nicaragua'=>'Central America',
			'Panama'=>'Central America',
			'Puerto Rico'=>'Caribbean',
			'Saint Kitts and Nevis'=>'Caribbean',
			'Saint Lucia'=>'Caribbean',
			'Saint Martin (French part)'=>'Caribbean',
			'Saint Pierre and Miquelon'=>'Northern America',
			'Saint Vincent and the Grenadines'=>'Caribbean',
			'Saint-Barthélemy'=>'Caribbean',
			'Sint Maarten (Dutch part)'=>'Caribbean',
			'Trinidad and Tobago'=>'Caribbean',
			'Turks and Caicos Islands'=>'Caribbean',
			'United States of America'=>'Northern America',
			'United States'=>'Northern America',
			'US'=>'Northern America',
			'USA'=>'Northern America',
			'United States Virgin Islands'=>'Caribbean',
			'Argentina'=>'South America',
			'Bolivia (Plurinational State of)'=>'South America',
			'Brazil'=>'South America',
			'Chile'=>'South America',
			'Colombia'=>'South America',
			'Ecuador'=>'South America',
			'Falkland Islands (Malvinas)'=>'South America',
			'French Guiana'=>'South America',
			'Guyana'=>'South America',
			'Paraguay'=>'South America',
			'Peru'=>'South America',
			'Suriname'=>'South America',
			'Uruguay'=>'South America',
			'Venezuela (Bolivarian Republic of)'=>'South America',
			'American Samoa'=>'Polynesia',
			'Australia'=>'Australia and New Zealand',
			'Cook Islands'=>'Polynesia',
			'Fiji'=>'Melanesia',
			'French Polynesia'=>'Polynesia',
			'Guam'=>'Micronesia',
			'Kiribati'=>'Micronesia',
			'Marshall Islands'=>'Micronesia',
			'Micronesia (Federated States of)'=>'Micronesia',
			'Nauru'=>'Micronesia',
			'New Caledonia'=>'Melanesia',
			'New Zealand'=>'Australia and New Zealand',
			'Niue'=>'Polynesia',
			'Norfolk Island'=>'Australia and New Zealand',
			'Northern Mariana Islands'=>'Micronesia',
			'Palau'=>'Micronesia',
			'Papua New Guinea'=>'Melanesia',
			'Pitcairn'=>'Polynesia',
			'Samoa'=>'Polynesia',
			'Solomon Islands'=>'Melanesia',
			'Tokelau'=>'Polynesia',
			'Tonga'=>'Polynesia',
			'Tuvalu'=>'Polynesia',
			'Vanuatu'=>'Melanesia',
			'Wallis and Futuna Islands'=>'Polynesia'
		);


}