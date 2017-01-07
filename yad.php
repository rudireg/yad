<?php
session_start();
$method = $_POST['method'];
if(empty($method)) {echo 'method is empty'; return;}
require_once "http_request.php";

//----------------------------------------------------------
switch ($method){
    case 'GetCampaignsList':
	    GetCampaignsList();
	    break;	
	case 'GetClientsUnits':
        GetClientsUnits();
        break;	
	case 'showFormCreateOrUpdateCampaign':
	    $id_company = $_POST['id_company'];
	    showFormCreateOrUpdateCampaign($id_company);
	    break;	
	case 'showFormCreateOrUpdateBanners':
        showFormCreateOrUpdateBanners();
        break;	
	case 'ResumeCampaign':
        $id_company = $_POST['id_company'];
		resumeCampaign($id_company);
        break;	
	case 'StopCampaign':	
	    $id_company = $_POST['id_company'];
		stopCampaign($id_company);
	    break;
	case 'DeleteCampaign':
        $id_company = $_POST['id_company'];
		deleteCampaign($id_company);
        break;	
	case 'CreateOrUpdateCampaign':
        CreateOrUpdateCampaign();
        break;	
	case 'CreateOrUpdateBanners':
        CreateOrUpdateBanners();
	    break;	
	case 'GetStatGoals':
        $id_company = $_POST['id_company'];
		$val        = $_POST['val'];
		GetStatGoals($id_company, $val);
        break;	
	case 'GetTimeZones':
		GetTimeZones();
        break;	
    case 'GetRegions':	
        GetRegions();
        break;  
	case 'GetRubrics':
        GetRubrics();
        break;	
	default:
	    echo "Unknown method";
}
//----------------------------------------------------------
# перекодировка строковых данных в UTF-8
function utf8($struct) {
    foreach ($struct as $key => $value) {
        if (is_array($value)) {
            $struct[$key] = utf8($value);
        }
        elseif (is_string($value)) {
            $struct[$key] = utf8_encode($value);
        }
    }
    return $struct;
}
//----------------------------------------------------------
//Удаляем заголовок из тела ответа сервера
function takeBody($inb){
    $pos = strpos($inb, '{');
	if($pos > 0)
	    return substr($inb, $pos-1);
	else
        echo $inb;	
}
//----------------------------------------------------------
//Разрешить показы компании
function resumeCampaign($id_company) {
    $HTTP = new HttpRequest();
	$method = 'ResumeCampaign';  
	$request = array(
        'locale'    => 'ru',
        'method'    => $method,
	//	'login'     => "europartspro",
		'application_id' => $_SESSION[client_id],
		'token'     => $_SESSION[token],
		'param'     => array('CampaignID' => $id_company)
    );
	$request = json_encode($request);
	$inb = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr = json_decode(takeBody($inb));
	
	if(isset($arr->error_code)) {
	    $showText = '<center>
		            <h1 style="color:red;">'.$arr->error_str.'</h1>
	                <h2 style="color:green;">'.$arr->error_detail.'</h2>
				    <h3 style="color:gray;">Код ошибки: '.$arr->error_code.'</h3>
					</center>';
	}
    else 
		$showText = '<h1 style="color:green;">Для компании номер # '.$id_company.' показы разрешены.</h1>';
	
	echo $showText; 
}
//----------------------------------------------------------
//Остановить показы компании
function stopCampaign($id_company) {
    $HTTP = new HttpRequest();
	$method = 'StopCampaign';  
	$request = array(
        'locale'    => 'ru',
        'method'    => $method,
	//	'login'     => "europartspro",
		'application_id' => $_SESSION[client_id],
		'token'     => $_SESSION[token],
		'param'     => array('CampaignID' => $id_company)
    );
	$request = json_encode($request);
	$inb = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr = json_decode(takeBody($inb));
	
	if(isset($arr->error_code)) {
	    $showText = '<center>
		            <h1 style="color:red;">'.$arr->error_str.'</h1>
	                <h2 style="color:green;">'.$arr->error_detail.'</h2>
				    <h3 style="color:gray;">Код ошибки: '.$arr->error_code.'</h3>
					</center>';
	}
    else 
		$showText = '<h1 style="color:green;">Для компании номер # '.$id_company.' показы остановлены.</h1>';
	
	echo $showText; 
}
//----------------------------------------------------------
//Удалить компанию
function deleteCampaign($id_company) {
    $HTTP = new HttpRequest();
	$method = 'DeleteCampaign';  
	$request = array(
        'locale'    => 'ru',
        'method'    => $method,
	//	'login'     => "europartspro",
		'application_id' => $_SESSION[client_id],
		'token'     => $_SESSION[token],
		'param'     => array('CampaignID' => $id_company)
    );
	$request = json_encode($request);
	$inb = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr = json_decode(takeBody($inb));
	return "";
}
//----------------------------------------------------------
//Получить количество баллов
function GetClientsUnits(){
    $HTTP = new HttpRequest();
	$method = 'GetClientsUnits'; 
	$request = array(
        'locale'    => 'ru',
        'method'    => $method,
	    'param'     => explode(',', $_POST['loginList']),
		'application_id' => $_SESSION[client_id],
		'token'     => $_SESSION[token]
    );
	$request = json_encode($request);
	$inb = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr = json_decode(takeBody($inb));
 
	if(isset($arr->error_code)) {
	    $showText = '<center>
		            <h1 style="color:red;">'.$arr->error_str.'</h1>
	                <h2 style="color:green;">'.$arr->error_detail.'</h2>
				    <h3 style="color:gray;">Код ошибки: '.$arr->error_code.'</h3>
					</center>';
		echo $showText;		
        return;		
	}else{
	    echo '<div class="blockBody">';
	    echo '<center><h2 style="margin-top:0px;">Количество баллов</h2></center>';
	    echo '<table class="table_body" cellpadding="5" cellspacing="5" style="border:1px dotted #333333;">
	          <tr><th>Логин</th><th>Кол. баллов</th></tr>';
	    foreach($arr->data as $v) {
		    echo '<tr>
		          <td style="border:1px dotted #333333;">'.$v->Login.'</td><td style="border:1px dotted #333333;">'.$v->UnitsRest.'</td>
		          </tr>';
	    };
        echo '</table>
	          </div>
			<a href="http://api.yandex.ru/direct/doc/concepts/Restrictions.xml" title="Ограничения на использование" target="_blanc">Ограничения на использование</a>';	
	}
}
//----------------------------------------------------------
//Получить список компаний
function GetCampaignsList(){
    $HTTP = new HttpRequest();
    $method = 'GetCampaignsList';  
    $request = array(
        'locale'    => 'ru',
        'method'    => $method,
	//	'login'     => "europartspro",
		'application_id' => $_SESSION[client_id],
		'token'     => $_SESSION[token]
    );
    $request = json_encode($request);

	$inb = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr = json_decode(takeBody($inb));
	
	echo '<div class="blockBody">';
	echo '<center><h2 style="margin-top:0px;">Список компаний</h2></center>';
	foreach($arr->data as $v) {
	    echo '<table class="table_body" >';
	    echo '<tr><td><span class="soft-text">Имя компании: </span></td><td><span class="value-text">'.$v->Name.'</span></td></tr>';
	    echo '<tr><td><span class="soft-text">Логин: </span></td><td><span class="value-text">'.$v->Login.'</span></td></tr>';
		echo '<tr><td><span class="soft-text">ID компании: </span></td><td><span class="value-text">'.$v->CampaignID.'</span></td></tr>';
	    echo '<tr><td><span class="soft-text">Статус кампании: </span></td><td><span class="value-text">'.$v->Status.'</span></td></tr>';
		echo '<tr><td><span class="soft-text">Состояние архивации кампании: </span></td><td><span class="value-text">'.$v->StatusArchive.'</span></td></tr>';
		echo '<tr><td><span class="soft-text">Начало показа объявлений: </span></td><td><span class="value-text">'.$v->StartDate.'</span></td></tr>';
		echo '<tr><td><span class="soft-text">Кампания активна: </span></td><td><span class="value-text">'.$v->IsActive.'</span></td></tr>';
		echo '<tr><td><span class="soft-text">Статус показов: </span></td><td><span class="value-text">'.$v->StatusShow.'</span></td></tr>';
		echo '<tr><td><span class="soft-text">Текущий баланс кампании: </span></td><td><span class="value-text">'.$v->Rest.'</span></td></tr>';
		echo '<tr><td><span class="soft-text">Всего было зачисленно: </span></td><td><span class="value-text">'.$v->Sum.'</span></td></tr>';
		echo '<tr><td><span class="soft-text">Проверено модератором: </span></td><td><span class="value-text">'.$v->StatusModerate.'</span></td></tr>';
		echo '<tr><td><span class="soft-text">Кол. кликов: </span></td><td><span class="value-text">'.$v->Clicks.'</span></td></tr>';
	    echo '<tr><td><span class="soft-text">Кол. показов: </span></td><td><span class="value-text">'.$v->Shows.'</span></td></tr>';
	    echo '<tr><td><span class="soft-text">Состояние активизации кампании: </span></td><td><span class="value-text">'.$v->StatusActivating.'</span></td></tr>';			
		echo '<tr><td><span class="soft-text">Сумма, доступная для перевода: </span></td><td><span class="value-text">'.$v->SumAvailableForTransfer.'</span></td></tr>';
		echo '<tr><td colspan="2"></td></tr>';
		echo '<tr><td colspan="2"><span onclick="showFormCreateOrUpdateCampaign('.$v->CampaignID.');" style="cursor:pointer;border-bottom:1px dotted #666666">Редактировать</span>
		      &nbsp;&nbsp;&nbsp;<span onclick="resumeCampaign('.$v->CampaignID.');" style="cursor:pointer;border-bottom:1px dotted #666666">Разрешить показы</span>
			  &nbsp;&nbsp;&nbsp;<span onclick="stopCampaign('.$v->CampaignID.');" style="cursor:pointer;border-bottom:1px dotted #666666">Остановить показы</span>
			  &nbsp;&nbsp;&nbsp;<span onclick="removeCampaign('.$v->CampaignID.');" style="cursor:pointer;border-bottom:1px dotted #666666">Удалить</span>
			  </td></tr>';
		echo '</table>';
        echo '<hr />';		
	}
    echo '</div>';
}
//----------------------------------------------------------
//Получить цели
function GetStatGoals($id_company, $val) {
    $id_company = 7295207;
    if($id_company < 1) {
	    echo '<p style="color:red;">Что бы указать цель, впишите номер или в ручную, или создайте компанию, и откройте её заново для редактирования, после этого будет доступны цели.</p>';
		return;
	}
    $HTTP     = new HttpRequest();
    $method   = 'GetStatGoals';  
	$Campaign = array('CampaignID' => $id_company);
    $request  = array(
        'locale'         => 'ru',
        'method'         => $method,
		'param'          => $Campaign,
		'application_id' => $_SESSION[client_id],
		'token'          => $_SESSION[token]
    );
    $request = json_encode($request);

	$inb = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr = json_decode(takeBody($inb));
	
	$body = '<table bgcolor="#CCCCCC" width="400" cellspacing="2" cellpadding="5" border="1">
	         <tr>
			 <th>Имя</th>
			 <th>Код</th>
			 <th>Действие</th>
			 </tr>';
	foreach($arr->data as $v) {
	    $body .= '<tr>
		           <td>'.$v->Name.'</td>
				   <td>'.$v->GoalID.'</td>
				   <td><input type="button" value="Выбрать" onclick="setGoal('.$v->GoalID.','.$val.');"></td>
		          </tr>';
	}
	$body .= '</table>';
	echo $body; 
}
//----------------------------------------------------------
//Получить список рубрик Яндекса
function GetRubrics(){
    $HTTP     = new HttpRequest();
    $method   = 'GetRubrics';  
    $request  = array(
        'locale'         => 'ru',
        'method'         => $method,
		'application_id' => $_SESSION[client_id],
		'token'          => $_SESSION[token]
    );
    $request = json_encode($request);
	$inb = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr = json_decode(takeBody($inb));
	
	$body = '<table bgcolor="#CCCCCC" width="400" cellspacing="2" cellpadding="5" border="1">
	         <tr>
			 <th>ID рубрики</th>
			 <th>ID вышестоящей рубрики</th>
			 <th>URL</th>
			 <th>Полное название</th>
			 <th>Краткое название</th>
			 <th>Действие</th>
			 </tr>';
	foreach($arr->data as $v) {
	    if(strstr($v->Checkable, "Yes"))
			$actn = '<input type="button" value="Выбрать" onclick="setRubric('.$v->RubricID.');" />';
		else
            $actn = '<span style="color:red;">Не активна</span>';
			
	    $body .= '<tr>
		           <td>'.$v->RubricID.'</td>
				   <td>'.$v->ParentID.'</td>
				   <td>'.$v->Url.'</td>
				   <td>'.$v->RubricFullName.'</td>
				   <td>'.$v->RubricName.'</td>
				   <td>'.$actn.'</td>
		          </tr>';
	}
	$body .= '</table>';
	echo $body; 
}
//----------------------------------------------------------
//Получить список регионов
function GetRegions(){
    $HTTP     = new HttpRequest();
    $method   = 'GetRegions';  
    $request  = array(
        'locale'         => 'ru',
        'method'         => $method,
		'application_id' => $_SESSION[client_id],
		'token'          => $_SESSION[token]
    );
    $request = json_encode($request);
	$inb = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr = json_decode(takeBody($inb));
	
	$body = '<table bgcolor="#CCCCCC" width="400" cellspacing="2" cellpadding="5" border="1">
	         <tr>
			 <th>ID региона</th>
			 <th>ID вышестоящего региона</th>
			 <th>Название региона</th>
			 <th>Действия</th>
			 </tr>';
	foreach($arr->data as $v) {
	    $body .= '<tr>
		           <td>'.$v->RegionID.'</td>
				   <td>'.$v->ParentID.'</td>
				   <td>'.$v->RegionName.'</td>
				   <td>
				       <input type="button" value="Вставить" onclick="setRegion(\''.$v->RegionID.'\');">
				       &nbsp;
				       <input type="button" value="Исключить" onclick="unSetRegion(\''.$v->RegionID.'\');">
				   </td>
		          </tr>';
	}
	$body .= '</table>';
	echo $body; 
}
//----------------------------------------------------------
//Получить список зон
function GetTimeZones(){
    $HTTP     = new HttpRequest();
    $method   = 'GetTimeZones';  
    $request  = array(
        'locale'         => 'ru',
        'method'         => $method,
		'application_id' => $_SESSION[client_id],
		'token'          => $_SESSION[token]
    );
    $request = json_encode($request);
	$inb = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr = json_decode(takeBody($inb));
	
	$body = '<table bgcolor="#CCCCCC" width="400" cellspacing="2" cellpadding="5" border="1">
	         <tr>
			 <th>Расположение</th>
			 <th>Разница с GMT в секундах</th>
			 <th>Временная зона</th>
			 <th>Действие</th>
			 </tr>';
	foreach($arr->data as $v) {
	    $body .= '<tr>
		           <td>'.$v->Name.'</td>
				   <td>'.$v->GMTOffset.'</td>
				   <td>'.$v->TimeZone.'</td>
				   <td><input type="button" value="Выбрать" onclick="setTimeZone(\''.$v->TimeZone.'\');"></td>
		          </tr>';
	}
	$body .= '</table>';
	echo $body; 
}
//----------------------------------------------------------
//Создание обявления
function CreateOrUpdateBanners(){
	$files              = $_POST['files'];
	$companyID          = $_POST['companyID'];
	$regions            = $_POST['regions'];
	$isRubric           = $_POST['isRubric'];
	$keyWords_type      = $_POST['keyWords_type'];
	$rubric_catalog     = $_POST['rubric_catalog'];
	$price              = $_POST['price'];
	$contextPrice       = $_POST['contextPrice'];
	$autoBroker         = $_POST['autoBroker'];
	$autoBudgetPriority = $_POST['autoBudgetPriority'];
	$sitelinks          = $_POST['sitelinks'];
	$href_1             = $_POST['href_1'];
	$href_2             = $_POST['href_2'];
	$href_3             = $_POST['href_3'];
	$title_1            = $_POST['title_1'];
	$title_2            = $_POST['title_2'];
	$title_3            = $_POST['title_3'];
	$totalMinusWords    = $_POST['totalMinusWords'];

	$dirFile = 'files/'.$files;
	$fp = fopen($dirFile, "r");
	if(!$fp) {
	    echo '<h3 style="color:red;">Ошибка открытия файла товаров: '.$dirFile.'</h3>';
		return;
	}
	$count =0;
	while (!feof($fp)) {
        $line = fgets($fp);
        if(strlen($line) < 5 || !strstr($line, ';'))
		    continue;
		$count ++;	
    };
	fclose($fp);
	if($count < 1) {
	    echo '<h3 style="color:red;">Ошибка. Файл товаров: '.$dirFile.' не имеет товаров</h3>';
		return;
	}
	if($count > 1000) {
	    echo '<h3 style="color:red;">Ошибка. Файл товаров: '.$dirFile.' имеет более 1000 товаров</h3>';
		return;
	}
	$fp = fopen($dirFile, "r");
	$prod = array();
	$count =0;
	$need = 4;
	while (!feof($fp)) {
        $line = fgets($fp);
		$count ++;
        if(strlen($line) < 5 || !strstr($line, ';'))
		    continue;
		$tmp = explode(';', $line);	
		if(count($tmp) != $need) {
		   echo '<h3 style="color:orange;">Предупреждение. Файл товаров: '.$dirFile.' в строке '.$count.' не содержит '.$need.' блоков</h3>';
		   continue;
		}  
        $prod[] = array('id' => $tmp[0], 'name' => $tmp[2], 'url' => $tmp[1], 'keys' => $tmp[3]);
    };
	fclose($fp);
	
	$Baners = array();
	$PhraseID =1;
	foreach($prod as $pr){		
	    $pattern = "/([^a-zA-Zа-яА-Я0-9\\s-.\"'*?!)(,:]+)/u";
	    $BannerPhraseInfo = array(); 
		if(!strcmp($isRubric, "Yes")) { //если используются не ключевые фразы а название рубрики яндекс-каталога
		    $PhraseText = $rubric_catalog;
		    $BannerPhraseInfo[] = array('PhraseID'       => $PhraseID ++, 
		                            'Phrase'             => $PhraseText,
									'IsRubric'           => $isRubric,
									'Price'              => (float)$price,
									'ContextPrice'       => (float)$contextPrice,
									'AutoBroker'         => $autoBroker,
                                    'AutoBudgetPriority' => $autoBudgetPriority									
								);
		}else{ //Если используются ключевые фразы
		    $tmpKeysArr = explode('###', $pr['keys']);
			foreach($tmpKeysArr as $key){
	            $BannerPhraseInfo[] = array('PhraseID'   => $PhraseID ++, 
		                            'Phrase'             => trim(str_replace('  ',' ',preg_replace($pattern, " ", $key))),
									'IsRubric'           => $isRubric,
									'Price'              => (float)$price,
									'ContextPrice'       => (float)$contextPrice,
									'AutoBroker'         => $autoBroker,
                                    'AutoBudgetPriority' => $autoBudgetPriority									
								);
		    };
		}		
		
	/*	$BannerPhraseInfo[] = array('PhraseID'           => $PhraseID ++, 
		                            'Phrase'             => $pr['name'],
									'IsRubric'           => $isRubric,
									'Price'              => (float)$price,
									'ContextPrice'       => (float)$contextPrice,
									'AutoBroker'         => $autoBroker,
                                    'AutoBudgetPriority' => $autoBudgetPriority									
								); */
		
		$SitelinksArr = array();
		if($sitelinks == "Yes"){
		     $SitelinksArr[] = array('Title'=>$title_1, 'Href'=>$href_1);
			 $SitelinksArr[] = array('Title'=>$title_2, 'Href'=>$href_2);
			 $SitelinksArr[] = array('Title'=>$title_3, 'Href'=>$href_3);
		}else
		     $SitelinksArr = array();
		$MinusKeywordsArr = array();	
		$MinusKeywordsArr =  explode (',', $totalMinusWords);
		$shotTitle = $pr['id'];
		while(strlen($shotTitle) > 33) {
		    $pos = strrpos ($shotTitle, ' ');
			$shotTitle = substr($shotTitle, 0, $pos-1);
		};
		$shotTitle = trim(str_replace('  ',' ',preg_replace($pattern, " ", $shotTitle)));
		$shotText  = $pr['name'];
		while(strlen($shotText) > 75) {
		    $pos = strrpos ($shotText, ' ');
			$shotText = substr($shotText, 0, $pos-1);
		};
		$shotText = preg_replace($pattern, " ", $shotText);
		$shotText = trim(str_replace('  ',' ',preg_replace($pattern, " ", $shotText)));
		
	    $Baners[] = array('BannerID'      => 0,
	                      'CampaignID'    => $companyID,
					      'Title'         => $shotTitle,
						  'Text'          => $shotText,
						  'Href'          => $pr['url'],
						  'Geo'           => $regions,
						  'Phrases'       => $BannerPhraseInfo,
						  'Sitelinks'     => $SitelinksArr,
						  'MinusKeywords' => $MinusKeywordsArr          
	                    );
	};

	$Baners = utf8($Baners);
	$method   = 'CreateOrUpdateBanners'; 
    $request  = array(
        'locale'         => 'ru',
        'method'         => $method,
		'param'          => $Baners,
		'application_id' => $_SESSION[client_id],
		'token'          => $_SESSION[token]
    );
    $request = json_encode($request);
	
	$HTTP    = new HttpRequest();
	$inb     = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr     = json_decode(takeBody($inb));
	
	if(isset($arr->error_code)) {
	    $showText = '<center>
		            <h1 style="color:red;">'.$arr->error_str.'</h1>
	                <h2 style="color:green;">'.$arr->error_detail.'</h2>
				    <h3 style="color:gray;">Код ошибки: '.$arr->error_code.'</h3>
					</center>';
	}
    else {
			    $showText = '<h1 style="color:green;">Объявления созданы.</h1>';
		 }	

	echo $showText; 								
}
//----------------------------------------------------------
//Редактирование или создание новой компании
function CreateOrUpdateCampaign(){
	//Получаем переменные
	$Login                      = $_POST['Login'];
	$CampaignID                 = $_POST['CampaignID'];
	$Name                       = $_POST['Name'];
	$FIO                        = $_POST['FIO'];
	$StartDate                  = $_POST['StartDate'];
	//$Currency                   = $_POST['Currency'];
	$Currency ='';
	$StrategyName               = $_POST['StrategyName'];
	$MaxPrice                   = $_POST['MaxPrice'];
	$AveragePrice               = $_POST['AveragePrice'];
	$WeeklySumLimit             = $_POST['WeeklySumLimit'];
	$ClicksPerWeek              = $_POST['ClicksPerWeek'];
	$GoalID                     = $_POST['GoalID'];
	$ContextStrategyName        = $_POST['ContextStrategyName'];
	$ContextLimit               = $_POST['ContextLimit'];
	$ContextLimitSum            = $_POST['ContextLimitSum'];
	$ContextPricePercent        = $_POST['ContextPricePercent'];
	$ContextMaxPrice            = $_POST['ContextMaxPrice'];
	$ContextAveragePrice        = $_POST['ContextAveragePrice'];
	$ContextWeeklySumLimit      = $_POST['ContextWeeklySumLimit'];
	$ContextClicksPerWeek       = $_POST['ContextClicksPerWeek'];
	$ContextGoalID              = $_POST['ContextGoalID'];
	$AdditionalMetrikaCounters  = $_POST['AdditionalMetrikaCounters'];
	$MetricaSms                 = $_POST['MetricaSms'];
	$ModerateResultSms          = $_POST['ModerateResultSms'];
	$MoneyInSms                 = $_POST['MoneyInSms'];
	$MoneyOutSms                = $_POST['MoneyOutSms'];
	$SmsTimeFrom                = $_POST['SmsTimeFrom'];
	$SmsTimeTo                  = $_POST['SmsTimeTo'];
	$Email                      = $_POST['Email'];
	$WarnPlaceInterval          = $_POST['WarnPlaceInterval'];
	$MoneyWarningValue          = $_POST['MoneyWarningValue'];
	$SendAccNews                = $_POST['SendAccNews'];
	$SendWarn                   = $_POST['SendWarn'];
	$StatusBehavior             = $_POST['StatusBehavior'];
	$ShowOnHolidays             = $_POST['ShowOnHolidays'];
	$HolidayShowFrom            = $_POST['HolidayShowFrom'];
	$HolidayShowTo              = $_POST['HolidayShowTo'];
	$Hours                      = $_POST['Hours'];
	$Days                       = $_POST['Days'];
	$BidCoefs                   = $_POST['BidCoefs'];
	$TimeZone                   = $_POST['TimeZone'];
	$WorkingHolidays            = $_POST['WorkingHolidays'];
	$StatusContextStop          = $_POST['StatusContextStop'];
	$AutoOptimization           = $_POST['AutoOptimization'];
	$DisabledIps                = $_POST['DisabledIps'];
	$StatusMetricaControl       = $_POST['StatusMetricaControl'];
	$DisabledDomains            = $_POST['DisabledDomains'];
	$StatusOpenStat             = $_POST['StatusOpenStat'];
	$ConsiderTimeTarget         = $_POST['ConsiderTimeTarget'];
	$MinusKeywords              = $_POST['MinusKeywords'];
	$AddRelevantPhrases         = $_POST['AddRelevantPhrases'];
	$RelevantPhrasesBudgetLimit = $_POST['RelevantPhrasesBudgetLimit'];
			
	//Строим структуру массива
	$StrategyArr = array('StrategyName'   => $StrategyName,
	                     'MaxPrice'       => (float)$MaxPrice,
						 'AveragePrice'   => (float)$AveragePrice,
						 'WeeklySumLimit' => (float)$WeeklySumLimit,
						 'ClicksPerWeek'  => (float)$ClicksPerWeek,
						 'GoalID'         => (float)$GoalID
						);
	$ContextStrategyArr = array(
	                     'StrategyName'        => $ContextStrategyName,
						 'ContextLimit'        => $ContextLimit,
						 'ContextLimitSum'     => $ContextLimitSum,
                         'ContextPricePercent' => $ContextPricePercent,
						 'MaxPrice'            => (float)$ContextMaxPrice,
						 'AveragePrice'        => (float)$ContextAveragePrice,
						 'WeeklySumLimit'      => (float)$ContextWeeklySumLimit,
						 'ClicksPerWeek'       => (float)$ContextClicksPerWeek,
						 'GoalID'              => (float)$ContextGoalID				 
	                    );	
    $SmsNotificationArr = array(
	                     'MetricaSms'        => $MetricaSms,
						 'ModerateResultSms' => $ModerateResultSms,
						 'MoneyInSms'        => $MoneyInSms,
						 'MoneyOutSms'       => $MoneyOutSms,
						 'SmsTimeFrom'       => $SmsTimeFrom,
						 'SmsTimeTo'         => $SmsTimeTo
	                    );	
    $EmailNotificationArr = array(
	                     'Email'             => $Email,
						 'WarnPlaceInterval' => $WarnPlaceInterval,
						 'MoneyWarningValue' => $MoneyWarningValue,
						 'SendAccNews'       => $SendAccNews,
						 'SendWarn'          => $SendWarn
                        );
	$HoursArr     = split(',', $Hours);		
    $DaysArr      = split(',', $Days);	
    $BidCoefsArr  = split(',', $BidCoefs);		
	$DaysHoursArr = array(
	                    'Hours'    => $HoursArr,
						'Days'     => $DaysArr,
						'BidCoefs' => $BidCoefsArr
	                    );	
    $ArayDaysHours = array();
	$ArayDaysHours[] = $DaysHoursArr;
    $TimeTargetArr = array(
                         'ShowOnHolidays'  => $ShowOnHolidays,
						 'DaysHours'       => $ArayDaysHours,
						 'TimeZone'        => $TimeZone,
						 'WorkingHolidays' => $WorkingHolidays
                        );	
    if(!empty($HolidayShowFrom)) $TimeTargetArr['HolidayShowFrom'] = $HolidayShowFrom;
	if(!empty($HolidayShowTo))   $TimeTargetArr['HolidayShowTo']   = $HolidayShowTo;
	if(!empty($AdditionalMetrikaCounters))
	    $AdditionalMetrikaCountersArr = split(',', $AdditionalMetrikaCounters);
	else
	    $AdditionalMetrikaCountersArr = array();
		
	$Campaign   =  array('Login'                    => $Login,
	                    'CampaignID'                => $CampaignID,
						'Name'                      => $Name,
						'FIO'                       => $FIO,
						'StartDate'                 => $StartDate,
						'Currency'                  => $Currency,
						'Strategy'                  => $StrategyArr,
						'ContextStrategy'           => $ContextStrategyArr,
						'AdditionalMetrikaCounters' => $AdditionalMetrikaCountersArr,
						'SmsNotification'           => $SmsNotificationArr,
						'EmailNotification'         => $EmailNotificationArr,
						'StatusBehavior'            => $StatusBehavior,
						'TimeTarget'                => $TimeTargetArr,
						'StatusContextStop'         => $StatusContextStop,
						'ContextLimit'              => $ContextLimit,
						'ContextLimitSum'           => $ContextLimitSum,
						'ContextPricePercent'       => $ContextPricePercent,
						'AutoOptimization'          => $AutoOptimization,
						'StatusMetricaControl'      => $StatusMetricaControl,
						'DisabledDomains'           => $DisabledDomains,
						'DisabledIps'               => $DisabledIps,
						'StatusOpenStat'            => $StatusOpenStat,
						'ConsiderTimeTarget'        => $ConsiderTimeTarget,
						'MinusKeywords'             => split(',', $MinusKeywords),
						'AddRelevantPhrases'        => $AddRelevantPhrases,
						'RelevantPhrasesBudgetLimit' => $RelevantPhrasesBudgetLimit
	);
	
	$Campaign = utf8($Campaign);
	$method   = 'CreateOrUpdateCampaign'; 
    $request  = array(
        'locale'         => 'ru',
        'method'         => $method,
		'param'          => $Campaign,
		'application_id' => $_SESSION[client_id],
		'token'          => $_SESSION[token]
    );
    $request = json_encode($request);
	$HTTP    = new HttpRequest();
	$inb     = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr     = json_decode(takeBody($inb));

	if(isset($arr->error_code)) {
	    $showText = '<center>
		            <h1 style="color:red;">'.$arr->error_str.'</h1>
	                <h2 style="color:green;">'.$arr->error_detail.'</h2>
				    <h3 style="color:gray;">Код ошибки: '.$arr->error_code.'</h3>
					</center>';
	}
    else {
	        if($CampaignID == 0)
			    $showText = '<h1 style="color:green;">Компания создана. Номер # '.$arr->data.'</h1>';
			else
			    $showText = '<h1 style="color:green;">Компания номер # '.$arr->data.' изменена.</h1>';
		 }	

	echo $showText; 	
}
//----------------------------------------------------------
//Показать форму создания объявлений
function showFormCreateOrUpdateBanners() {
    //Подготовка к выводу формы
	$HTTP = new HttpRequest();
	//Получаем список компаний
    $method = 'GetCampaignsList';  
    $request = array(
        'locale'    => 'ru',
        'method'    => $method,
	//	'login'     => "europartspro",
		'application_id' => $_SESSION[client_id],
		'token'     => $_SESSION[token]
    );
    $request = json_encode($request);
	$inb = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	$arr = json_decode(takeBody($inb));
	$arrCampaign = array();
	foreach($arr->data as $v) {
	    $arrCampaign[$v->CampaignID] = $v->Name;
    };
	
	echo "<script type='text/javascript'>
           $(document).ready(function(){
	           $('#actionFormCreateOrUpdateBanners').validate({
		           rules:{
				       files:{
					       required: true
					   },
					   b_companyID:{
					       required: true
					   },
					   b_regions:{
					       required: true
					   },
					   b_Href_1:{
					       required: true
					   },
					   b_Href_2:{
					       required: true
					   },
					   b_Href_3:{
					       required: true
					   },
					   b_Title_1:{
					       required: true
					   },
					   b_Title_2:{
					       required: true
					   },
					   b_Title_3:{
					       required: true
					   },
					   b_rubric_catalog:{
					       required: true
					   }
				   },
				   messages:{
				        files: {
					        required:'<br />Укажите файл с товарами'
						},
					    b_companyID: {
						    required:'<br />Укажите компанию'
						},
					    b_regions: {
						    required:'<br />Укажите регионы'
						},
						b_Href_1:{
					       required: '<br />Укажите URL'
					    },
					    b_Href_2:{
					       required: '<br />Укажите URL'
					    },
					    b_Href_3:{
					       required: '<br />Укажите URL'
					    },
						b_Title_1:{
					       required: '<br />Укажите текст для URL'
					   },
					   b_Title_2:{
					       required: '<br />Укажите текст для URL'
					   },
					   b_Title_3:{
					       required: '<br />Укажите текст для URL'
					   },
					   b_rubric_catalog:{
					       required: '<br />кажите рубрику Яндекс.Каталога'
					   }
				   },
				   submitHandler: function(form){
				       var files          = $('#files').val();
					   var companyID      = $('#b_companyID option:selected').val();
					   var regions        = $('#b_regions').val();
					   var isRubric       = $('#b_IsRubric option:selected').val();
					   var keyWords_type  = $('#b_keyWords_type option:selected').val();
					   var rubric_catalog = $('#b_rubric_catalog').val();
					   var price          = $('#b_Price').val();
					   var contextPrice   = $('#b_ContextPrice').val();
					   var autoBroker;
					   if($('#b_AutoBroker').is(':checked') == true) autoBroker = 'Yes';
				  	   else autoBroker = 'No';
					   var autoBudgetPriority   = $('#b_AutoBudgetPriority').val();
					   var sitelinks;
					   if($('#b_Sitelinks').is(':checked') == true) sitelinks = 'Yes';
				  	   else sitelinks = 'No';
					   var href_1          = $('#b_Href_1').val();
					   var href_2          = $('#b_Href_2').val();
					   var href_3          = $('#b_Href_3').val();
					   var title_1         = $('#b_Title_1').val();
					   var title_2         = $('#b_Title_2').val();
					   var title_3         = $('#b_Title_3').val();
					   var totalMinusWords = $('#b_totalMinusWords').val();
					   var method          = 'CreateOrUpdateBanners';
					   
					   var params = {'method':method,'files':files,'companyID':companyID,'regions':regions,
					                 'isRubric':isRubric,'keyWords_type':keyWords_type,'rubric_catalog':rubric_catalog,
									 'price':price,'contextPrice':contextPrice,'autoBroker':autoBroker,
									 'autoBudgetPriority':autoBudgetPriority,'sitelinks':sitelinks,'href_1':href_1,
									 'href_2':href_2,'href_3':href_3,'title_1':title_1,'title_2':title_2,
									 'title_3':title_3,'totalMinusWords':totalMinusWords};
									 
						$('#btnCreateBanners').hide();
					    $('img#loadCreateBanner').show();
					    $('#infoCreateBanner').html('');	

                        $.ajax({
                          url:'yad/yad.php',
                          dataType:'json',
			              cache: false, 
	                      type:'POST',
                          data:params,
                          complete:function(data){
						        $('#btnCreateBanners').show();
							    $('img#loadCreateBanner').hide();
								$('#infoCreateBanner').html(data.responseText);
								$('#infoAlertBox').html(data.responseText);
				                $('#exampleModal1').arcticmodal();
						    } 
						}); 						
					    return false;	  			 
				   }
		       });
		   });
	      </script>";
 
    echo '<center><h1>Создание объявлений</h1></center>
	      <div class="blockBody">
		  <form id="actionFormCreateOrUpdateBanners">
	      <table width="100%">
	      <tr>
		  <td><label for="files"><span style="color:red;">*</span> Укажите файл с товарами, не более 1000 товаров, <br /><span class="soft-text">Для одной компании максимум 1000 объявлений</span></label></td>
		  <td>
		  <input id="fileProducts" type="button" value="Обзор" />
		  <img id="load" src="images/loading.gif"/>
		  <br />
          <input id="files" name="files" type="text" value="" />
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td><label for="b_companyID"><span style="color:red;">*</span> Укажите компанию</label></td>
		  <td>
		     <select id="b_companyID" name="b_companyID">
			 <option value="" disabled="disabled" selected="selected" >Укажите компанию</option>';
			 foreach($arrCampaign as $cid => $cname)
			    echo '<option value="'.$cid.'">'.$cname.'</option>';
	echo '   </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td><label for="b_regions"><span style="color:red;">*</span> Идентификаторы регионов, для которых показы объявления включены или выключены. <br /><span class="soft-text">Идентификатор <b>0</b> или пустая строка — показывать во всех регионах</span></label></td>
		  <td><textarea id="b_regions" name="b_regions" /></textarea>&nbsp;&nbsp;<input type="button" id="btnGetRegions" value="Обзор" onclick="getRegions();" /><img id="imgGetRegions" src="images/loading.gif" alt="" /></td>	 
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  </table>
	      <table width="100%" style="background:#CCCCCC;">	  
		  <tr><td colspan="2"><center><h3>Настройка ключевых фраз</h3></center></td></tr>
		  <tr>
		      <td><label><span style="color:red;">*</span> Укажите что использовать,<br />Ключевые слова или <br />идентификатор одной рубрики Яндекс.Каталога</label></td>
		      <td>
			       <select id="b_IsRubric" name="b_IsRubric">
				   <option value="No">Ключевые слова</option>
				   <option value="Yes">Рубрика Яндекс.Каталога</option>
				   </select>
			  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr id="b_keyWords_place">
		  <td><label><span style="color:red;">*</span> Как строить ключ. слова для объявления</label></td>
		  <td><select id="b_keyWords_type" name="b_keyWords_type">
		       <option value="1">Только Код товара</option>
			   <option value="2">Только описание товара</option>
			   <option value="3">Код товара + его описание</option>
		      </select></td>
		  </tr>
		  <tr id="b_rubric_place">
		  <td><label for="b_rubric_catalog"><span style="color:red;">*</span> Укажите рубрику Яндекс.Каталога</label></td>
		  <td><input type="text" value="" id="b_rubric_catalog" name="b_rubric_catalog" /><input type="button" onclick="getRubrics();" value="Обзор" id="btn_b_rubric_catalog" /><img id="imgBtn_b_rubric_catalog" src="images/loading.gif" alt="" /></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		 </table> 
		 <table width="100%"> 
		  <tr>
          <td><label for="b_Price">Ставка на поиске Яндекса (у. е.). Предустановленное ограничение — 50 у. е. <br /><span class="soft-text">Требуется, только если для кампании выбрана стратегия <br />с ручным управлением ставками</span></label></td>
          <td><input type="text" value="" name="b_Price" id="b_Price" /></td>
		  </tr>		
          <tr><td colspan="2"><hr /></td></tr>		  
		  <tr>
		  <td><label for="b_ContextPrice">Ставка в Рекламной сети Яндекса (у. е.). <br />
		             Ставку можно задавать в следующих случаях:<br />
					 <b>1.</b> <span class="soft-text">На поиске используется стратегия IndependentControl. <br />
					 Эту стратегию можно выбрать только через веб-интерфейс Директа.</span><br />
					 <b>2.</b> <span class="soft-text">На поиске используется любая ручная стратегия <br />
					      и фраза отключена за низкий CTR.<br />
                          Для новых фраз данное условие не актуально, <br />
						  поскольку фразы больше не отключаются за низкий CTR.</span><br /> 
					 </label></td>
			<td><input type="text" name="b_ContextPrice" id="b_ContextPrice" /></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
          <tr>
		  <td colspan="2">
		  <input type="checkbox" checked="checked" name="b_AutoBroker" id="b_AutoBroker" />
		  <label for="b_AutoBroker">Включить <a href="http://direct.yandex.ru/help/?id=990424" title="автоброкер" target="_blanc">автоброкер</a></label>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td><label><span style="color:red;">*</span> Приоритет фразы при использовании автоматических стратегий.<br /> 
		  <span class="soft-text">Высокий приоритет обеспечивает на 10–15% больше показов по фразе, <br />чем по фразам со средним приоритетом. <br />Низкий приоритет уменьшает показы на такую же величину. <br />Если в объявлении только одна фраза, параметр не имеет значения.<br />
		  <b>Требуется Для стратегии WeeklyBudget</b></span></label></td>
		  <td>
		       <select id="b_AutoBudgetPriority" name="b_AutoBudgetPriority">
			       <option value="Low">низкий приоритет</option>
				   <option value="Medium" selected="selected">средний приоритет</option>
				   <option value="High">высокий приоритет</option>
			   </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  </table>
		  <table width="100%" style="background:#CCCCCC;">
		  <td colspan="2">
		  <input type="checkbox" name="b_Sitelinks" id="b_Sitelinks" />
		  <label for="b_Sitelinks">Использовать дополнительные 3 ссылки <span class="soft-text">Показываются в спецразмещении</span></label></td>
		  </tr>
		  <tr id="added_url_1">
		  <td><label for="b_Href_1"><span style="color:red;">*</span> Url <span class="soft-text">Без http://</span></label></td>
		  <td><input type="text" id="b_Href_1" name="b_Href_1" value="" /></td>
		  </tr>
		  <tr id="added_title_1">
		  <td><label for="b_Title_1"><span style="color:red;">*</span> Заголовок ссылки</label></td>
		  <td><input type="text" id="b_Title_1" name="b_Title_1" value="" /></td>
		  </tr>
		  <tr id="l_added_1"><td colspan="2"><hr /></td></tr>
		  <tr id="added_url_2">
		  <td><label for="b_Href_2"><span style="color:red;">*</span> Url <span class="soft-text">Без http://</span></label></td>
		  <td><input type="text" id="b_Href_2" name="b_Href_2" value="" /></td>
		  </tr>
		  <tr id="added_title_2">
		  <td><label for="b_Title_2"><span style="color:red;">*</span> Заголовок ссылки</label></td>
		  <td><input type="text" id="b_Title_2" name="b_Title_2" value="" /></td>
		  </tr>
		  <tr id="l_added_2"><td colspan="2"><hr /></td></tr>
		  <tr id="added_url_3">
		  <td><label for="b_Href_3"><span style="color:red;">*</span> Url <span class="soft-text">Без http://</span></label></td>
		  <td><input type="text" id="b_Href_3" name="b_Href_3" value="" /></td>
		  </tr>
		  <tr id="added_title_3">
		  <td><label for="b_Title_3"><span style="color:red;">*</span> Заголовок ссылки</label></td>
		  <td><input type="text" id="b_Title_3" name="b_Title_3" value="" /></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  </table>
		  <table width="100%">
		  <tr>
		  <td><label>Массив минус-слов, общих для всех фраз объявления. <br /><span class="soft-text">Если минус-слово совпадает с ключевым словом во фразе, <br />к данной фразе минус-слово не применяется.</span></label></td>
      	  <td><textarea id="b_totalMinusWords" name="b_totalMinusWords" /></textarea></td>	 
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <td colspan="2">
		  <center>
		  <input id="btnCreateBanners" type="submit" value="Создать" style="padding: 20px; font-size: 20px; display: inline;">
		  <img id="loadCreateBanner" src="images/loading.gif" />
		  <div id="infoCreateBanner"></div>
		  </center>
		  </td>
	      </table>
	      </form>
	     </div>';

    echo "<script type='text/javascript'>
		      $('img#imgGetRegions').hide();
			  $('img#imgBtn_b_rubric_catalog').hide();
			  $('img#load').hide();
			  $('img#loadCreateBanner').hide(); 
			  $('#b_rubric_place').hide();
			  
			  function hide3urls(){
			    $('#added_url_1').hide();
			    $('#added_url_2').hide();
			    $('#added_url_3').hide();
			    $('#added_title_1').hide();
			    $('#added_title_2').hide();
			    $('#added_title_3').hide();
			    $('#l_added_1').hide();
			    $('#l_added_2').hide();
			  }
			  
			  hide3urls();

              $(document).ready(function(){
			       $('#b_IsRubric').change(function(){
					   var isRubric = $('#b_IsRubric :selected').val();
					   if(isRubric.indexOf('No') + 1) {
						   $('#b_keyWords_place').show();
						   $('#b_rubric_place').hide(); 
					   } else {
					       $('#b_keyWords_place').hide();
						   $('#b_rubric_place').show(); 
					   }
				   });  

                    $('#b_Sitelinks').change(function(){
					    if($('#b_Sitelinks').is(':checked') == true) {
						    $('#added_url_1').show();
			                $('#added_url_2').show();
			                $('#added_url_3').show();
			                $('#added_title_1').show();
			                $('#added_title_2').show();
			                $('#added_title_3').show();
			                $('#l_added_1').show();
			                $('#l_added_2').show();
						}else{
						    hide3urls();
						}
					});				   
			  });			  
			   
			$(document).ready(function() {
                var button = $('#fileProducts');
                $.ajax_upload(button, {
                    action : 'upload.php',
                    name : 'myfile',
                    onSubmit : function(file, ext) {
                            $('img#load').show();
                            this.disable();
                        },
                    onComplete : function(file, response) {
                            $('img#load').hide();
                            this.enable();
							$('#files').val(file);
                        }
                    });
        });  
	</script>";  
	      

}
//----------------------------------------------------------
//Показать форму создания или редактирования компании
function showFormCreateOrUpdateCampaign($id_company) {
    //Инициализация переменных
	if($id_company == 0) { //Если это создание новой компании
	     $Login                     = '';
         $Name                      = '';
         $FIO                       = '';
		 $StartDate                 = date("Y-m-d"); 
         $Currency                  = 'USD';
         $StrategyName              = 'ShowsDisabled';
         $MaxPrice                  = '';
         $AveragePrice              = '';
         $WeeklySumLimit            = '';
         $ClicksPerWeek             = '';
         $GoalID                    = '';
         $ContextStrategyName       = 'ShowsDisabled';
         $ContextLimit              = 'Default';
         $ContextLimitSum           = 30;
         $ContextPricePercent       = 10;
         $ContextMaxPrice           = '';
         $ContextAveragePrice       = '';
         $ContextWeeklySumLimit     = '';
         $ContextClicksPerWeek      = '';
         $ContextGoalID             = '';
         $AdditionalMetrikaCounters = '';
         $MetricaSms                = 'No';
         $ModerateResultSms         = 'No';
         $MoneyInSms                = 'No';
         $MoneyOutSms               = 'No';
         $SmsTimeFrom               = '';
         $SmsTimeTo                 = '';
         $Email                     = '';
         $WarnPlaceInterval         = 60;
         $MoneyWarningValue         = 50;
         $SendAccNews               = 'No';
         $SendWarn                  = 'No';
         $StatusBehavior            = 'No';
         $ShowOnHolidays            = 'No';
         $HolidayShowFrom           = '';
         $HolidayShowTo             = '';
         $Hours                     = '';
         $Days                      = '';
         $TimeZone                  = '';
         $WorkingHolidays           = 'No';
         $AutoOptimization          = 'No';
         $StatusMetricaControl      = 'No';
         $DisabledDomains           = '';
         $DisabledIps               = '';
         $StatusOpenStat            = 'No';
         $ConsiderTimeTarget        = 'No';
         $MinusKeywords             = '';
         $AddRelevantPhrases        = 'No';
         $RelevantPhrasesBudgetLimit = 100;
	} else { //Если это редактирование компании, то получаем данные компании
	    $param =  array('CampaignIDS' => array($id_company)
	                   // 'Currency'    => 'USD'
						);
	    $request  = array(
             'locale'         => 'ru',
             'method'         => 'GetCampaignsParams',
		     'param'          => $param,
		     'application_id' => $_SESSION[client_id],
		     'token'          => $_SESSION[token]
        );
        $request = json_encode($request);
	    $HTTP    = new HttpRequest();
	    $inb     = $HTTP->Post("https://api.direct.yandex.ru/live/v4/json/", $request);
	    $arr     = json_decode(takeBody($inb));
		//echo $inb;
		foreach($arr->data as $v) {
			$Login = $v->Login;
            $Name  = $v->Name;
            $FIO  = $v->FIO;
            $StartDate = $v->StartDate;
            $Currency = $v->Currency;
			if(empty($Currency)) $Currency = 'USD';
            $StrategyName = $v->Strategy->StrategyName;
            $MaxPrice = $v->Strategy->MaxPrice;
            $AveragePrice = $v->Strategy->AveragePrice;
            $WeeklySumLimit = $v->Strategy->WeeklySumLimit;
            $ClicksPerWeek = $v->Strategy->ClicksPerWeek;
            $GoalID = $v->Strategy->GoalID;
            $ContextStrategyName = $v->ContextStrategy->StrategyName;
            $ContextLimit = $v->ContextStrategy->ContextLimit;
            $ContextLimitSum = $v->ContextStrategy->ContextLimitSum;
            $ContextPricePercent = $v->ContextStrategy->ContextPricePercent;
            $ContextMaxPrice = $v->ContextStrategy->ContextMaxPrice;
            $ContextAveragePrice = $v->ContextStrategy->ContextAveragePrice;
            $ContextWeeklySumLimit = $v->ContextStrategy->ContextWeeklySumLimit;
            $ContextClicksPerWeek = $v->ContextStrategy->ContextClicksPerWeek;
            $ContextGoalID = $v->ContextStrategy->ContextGoalID;
            $AdditionalMetrikaCounters = $v->ContextStrategy->AdditionalMetrikaCounters;
            $MetricaSms = $v->SmsNotification->MetricaSms;
            $ModerateResultSms = $v->SmsNotification->ModerateResultSms;
            $MoneyInSms = $v->SmsNotification->MoneyInSms;
            $MoneyOutSms = $v->SmsNotification->MoneyOutSms;
            $SmsTimeFrom = $v->SmsNotification->SmsTimeFrom;
            $SmsTimeTo = $v->SmsNotification->SmsTimeTo;
            $Email = $v->EmailNotification->Email;
            $WarnPlaceInterval = $v->EmailNotification->WarnPlaceInterval;
            $MoneyWarningValue = $v->EmailNotification->MoneyWarningValue;
            $SendAccNews = $v->EmailNotification->SendAccNews;
            $SendWarn = $v->EmailNotification->SendWarn;
            $StatusBehavior = $v->StatusBehavior;
            $ShowOnHolidays = $v->TimeTarget->ShowOnHolidays;
            $HolidayShowFrom = $v->TimeTarget->HolidayShowFrom;
            $HolidayShowTo = $v->TimeTarget->HolidayShowTo;
            $Hours    = $v->TimeTarget->DaysHours['0']->Hours;
            $Days     = $v->TimeTarget->DaysHours['0']->Days;
			$BidCoefs = $v->TimeTarget->DaysHours['0']->BidCoefs;
            $TimeZone = $v->TimeTarget->TimeZone;
            $WorkingHolidays = $v->TimeTarget->WorkingHolidays;
            $AutoOptimization = $v->AutoOptimization;
            $StatusMetricaControl = $v->StatusMetricaControl;
            $DisabledDomains = $v->DisabledDomains;
            $DisabledIps = $v->DisabledIps;
            $StatusOpenStat = $v->StatusOpenStat;
            $ConsiderTimeTarget = $v->ConsiderTimeTarget;
            $MinusKeywords = join(",", $v->MinusKeywords);
            $AddRelevantPhrases = $v->AddRelevantPhrases;
            $RelevantPhrasesBudgetLimit = $v->RelevantPhrasesBudgetLimit;
		}
	}
	
    echo "       
	<script>
	$('img#imgGetTsel_1').hide();
	$('img#imgGetTsel_2').hide();
	$('img#imgGetTimeZone').hide();
	$('img#imgBtnFormAction').hide(); 
			  
	$(document).ready(function(){
     $('#actionFormCreateOrUpdateCampaign').validate({
	     rules:{
				    Login: {
					    required: true,
						minlength: 3,
						maxlength: 256
					},
					Name: {
					    required: true,
						minlength: 3,
						maxlength: 256
					},
					FIO: {
					    required: true,
						minlength: 3,
						maxlength: 256
					},
					StrategyName: {
					    required: true
					},
					AveragePrice: {
					    required: true,
						digits: true
					},
					WeeklySumLimit: {
					    required: true,
						digits: true
					},
					ClicksPerWeek: {
					    required: true,
						digits: true
					},
					GoalID: {
					    required: true,
						digits: true
					},
					ContextStrategyName: {
					    required: true
					},
					ContextAveragePrice: {
					    required: true,
						digits: true
					},
					ContextWeeklySumLimit: {
					    required: true,
						digits: true
					},
					ContextClicksPerWeek: {
					    required: true,
						digits: true
					},
					ContextGoalID: {
					    required: true,
						digits: true
					},
					Email: {
					    required: true,
						email: true
					},
					WarnPlaceInterval: {
					    required: true,
						digits: true
					},
					MoneyWarningValue: {
					    required: true,
						digits: true,
						max: 50,
						min:1
					},
					Hours: {
					    required: true
					},
					Days: {
					    required: true
					}
		 },
		 messages:{				
					Login: {
						required: '<br />Введите логин',
						minlength: '<br />Минимум 3 символа',
						maxlength:  '<br />Максимум 256 символов'
					},
					Name: {
						required: '<br />Введите имя',
						minlength: '<br />Минимум 3 символа',
						maxlength:  '<br />Максимум 256 символов'
					},
					FIO: {
						required: '<br />Введите ФИО',
						minlength: '<br />Минимум 3 символа',
						maxlength:  '<br />Максимум 256 символов'
					},
					StrategyName: {
						required: '<br />Укажите стратегию поиска'
					},
					AveragePrice: {
						required: '<br />Укажите среднюю ставку',
						digits: '<br />Только цифры'
					},
					WeeklySumLimit: {
						required: '<br />Укажите максимальный недельный бюджет',
						digits: '<br />Только цифры'
					},
					ClicksPerWeek: {
						required: '<br />Укажите количество кликов в неделю',
						digits: '<br />Только цифры'
					},
					GoalID: {
						required: '<br />Укажите идентификатор цели Яндекс.Метрики',
						digits: '<br />Только цифры'
					},
					ContextStrategyName: {
						required: '<br />Укажите стратегию в Рекламной сети Яндекса'
					},
					ContextAveragePrice: {
						required: '<br />Укажите среднюю ставку',
						digits: '<br />Только цифры'
					},
					ContextWeeklySumLimit: {
						required: '<br />Укажите максимальный недельный бюджет',
						digits: '<br />Только цифры'
					},
					ContextClicksPerWeek: {
						required: '<br />Укажите количество кликов в неделю',
						digits: '<br />Только цифры'
					},
					ContextGoalID: {
						required: '<br />Укажите идентификатор цели Яндекс.Метрики',
						digits: '<br />Только цифры'
					},
					Email: {
						required: '<br />Укажите Email',
						email: '<br />Не верный email адрес'
					},
					WarnPlaceInterval: {
					    required: '<br />Укажите периодичность проверки',
						digits: '<br />Только цифры'
					},
					MoneyWarningValue: {
					    required: '<br />Укажите минимальный баланс',
						digits: '<br />Только цифры',
						max: '<br />Максимум 50',
						min: '<br />Минимум 1'
					},
					Hours: {
					    required: '<br />Укажите интервал времени '
					},
					Days: {
					    required: '<br />Укажите интервал дней '
					} 
         },   
         submitHandler: function(form) {
                    var method                    = 'CreateOrUpdateCampaign';
                    var Login                     = $('#Login').val();
                    var CampaignID                = $('#CampaignID').val();
					var Name                      = $('#Name').val();
					var FIO                       = $('#FIO').val();
					var StartDate                 = $('#StartDate').val();
					var Currency                  = $('#Currency option:selected').val();
					var StrategyName              = $('#StrategyName option:selected').val();
					var MaxPrice                  = $('#MaxPrice').val();
					var AveragePrice              = $('#AveragePrice').val();
					var WeeklySumLimit            = $('#WeeklySumLimit').val();
					var ClicksPerWeek             = $('#ClicksPerWeek').val(); 
					var GoalID                    = $('#GoalID').val(); 
					var ContextStrategyName       = $('#ContextStrategyName option:selected').val();
                    var ContextLimit              = $('#ContextLimit option:selected').val();					
					var ContextLimitSum           = $('#ContextLimitSum option:selected').val();	
                    var ContextPricePercent       = $('#ContextPricePercent option:selected').val();	
                    var ContextMaxPrice           = $('#ContextMaxPrice').val(); 					
                    var ContextAveragePrice       = $('#ContextAveragePrice').val(); 
                    var ContextWeeklySumLimit     = $('#ContextWeeklySumLimit').val();  
                    var ContextClicksPerWeek      = $('#ContextClicksPerWeek').val();  	
                    var ContextGoalID             = $('#ContextGoalID').val();   
                    var AdditionalMetrikaCounters = $('#AdditionalMetrikaCounters').val(); 					
                    var MetricaSms;
		            if($('#MetricaSms').is(':checked') == true) MetricaSms = 'Yes';
					else MetricaSms = 'No';
		            var ModerateResultSms;
		            if($('#ModerateResultSms').is(':checked') == true) ModerateResultSms = 'Yes';
					else ModerateResultSms = 'No';
					var MoneyInSms;
					if($('#MoneyInSms').is(':checked') == true) MoneyInSms = 'Yes';
					else MoneyInSms = 'No';
					var MoneyOutSms;
					if($('#MoneyOutSms').is(':checked') == true) MoneyOutSms = 'Yes';
					else MoneyOutSms = 'No';
					var SmsTimeFrom  = $('#SmsTimeFrom').val(); 
					var SmsTimeTo    = $('#SmsTimeTo').val(); 
					var Email        = $('#Email').val(); 
					var WarnPlaceInterval = $('#WarnPlaceInterval option:selected').val();
					var MoneyWarningValue = $('#MoneyWarningValue').val(); 
					var SendAccNews;
					if($('#SendAccNews').is(':checked') == true) SendAccNews = 'Yes';
					else SendAccNews = 'No';
			        var SendWarn;
					if($('#SendWarn').is(':checked') == true) SendWarn = 'Yes';
					else SendWarn = 'No';
			        var StatusBehavior;
					if($('#StatusBehavior').is(':checked') == true) StatusBehavior = 'Yes';
					else StatusBehavior = 'No';
					var ShowOnHolidays;
					if($('#ShowOnHolidays').is(':checked') == true) ShowOnHolidays = 'Yes';
					else ShowOnHolidays = 'No';
			        var HolidayShowFrom = $('#HolidayShowFrom option:selected').val();
			        var HolidayShowTo   = $('#HolidayShowTo option:selected').val();
					var Hours ='';
					$('#Hours option:selected').each(function(){
					        if(Hours) Hours += ',';
						    Hours += $(this).val();
						});
					var Days = '';
					$('#Days option:selected').each(function(){
					        if(Days) Days += ',';
						    Days += $(this).val();
						});
					var BidCoefs ='';
					var tmp, i; 
					for (i=0; i<=23; i++) {
                        if($('#blockDiscountHour_' + i).is(':hidden') == false){
					        tmp = $('#discountHour_' + i + ' option:selected').val();
							if(BidCoefs) BidCoefs += ',';
					        BidCoefs += tmp; 
					    }
                    };
					var TimeZone = $('#TimeZone').val(); 
					var WorkingHolidays;
					if($('#WorkingHolidays').is(':checked') == true) WorkingHolidays = 'Yes';
					else WorkingHolidays  = 'No';
			        var StatusContextStop = 'No';
				    var AutoOptimization;
					if($('#AutoOptimization').is(':checked') == true) AutoOptimization = 'Yes';
					else AutoOptimization = 'No';
					var StatusMetricaControl;
					if($('#StatusMetricaControl').is(':checked') == true) StatusMetricaControl = 'Yes';
					else StatusMetricaControl = 'No';
					var DisabledDomains    = $('#DisabledDomains').val(); 
					var DisabledIps        = $('#DisabledIps').val(); 
					var StatusOpenStat     = $('#StatusOpenStat option:selected').val();
					var ConsiderTimeTarget = $('#ConsiderTimeTarget option:selected').val();
					var MinusKeywords      = $('#MinusKeywords').val(); 
				    var AddRelevantPhrases = $('#AddRelevantPhrases option:selected').val();
				    var RelevantPhrasesBudgetLimit = $('#RelevantPhrasesBudgetLimit option:selected').val();

                    //Создаем строку запроса
	                var param = {'method':method, 'Login':Login, 'CampaignID':CampaignID, 'Name':Name, 'FIO':FIO, 
					             'StartDate':StartDate, 'Currency':Currency, 'StrategyName':StrategyName, 'MaxPrice':MaxPrice,
								 'AveragePrice':AveragePrice, 'WeeklySumLimit':WeeklySumLimit, 'ClicksPerWeek':ClicksPerWeek,
								 'GoalID':GoalID, 'ContextStrategyName':ContextStrategyName, 'ContextLimit':ContextLimit,
								 'ContextLimitSum':ContextLimitSum, 'ContextPricePercent':ContextPricePercent,
								 'ContextMaxPrice':ContextMaxPrice, 'ContextAveragePrice':ContextAveragePrice,
								 'ContextWeeklySumLimit':ContextWeeklySumLimit, 'ContextClicksPerWeek':ContextClicksPerWeek,
								 'ContextGoalID':ContextGoalID, 'AdditionalMetrikaCounters':AdditionalMetrikaCounters,
								 'MetricaSms':MetricaSms, 'ModerateResultSms':ModerateResultSms, 'MoneyInSms':MoneyInSms,
								 'MoneyOutSms':MoneyOutSms, 'SmsTimeFrom':SmsTimeFrom, 'SmsTimeTo':SmsTimeTo,
								 'Email':Email, 'WarnPlaceInterval':WarnPlaceInterval, 'MoneyWarningValue':MoneyWarningValue,
								 'SendAccNews':SendAccNews, 'SendWarn':SendWarn, 'StatusBehavior':StatusBehavior,
								 'ShowOnHolidays':ShowOnHolidays, 'HolidayShowFrom':HolidayShowFrom, 'HolidayShowTo':HolidayShowTo,
								 'Hours':Hours, 'Days':Days, 'BidCoefs':BidCoefs, 'TimeZone':TimeZone, 'WorkingHolidays':WorkingHolidays,
								 'StatusContextStop':StatusContextStop, 'AutoOptimization':AutoOptimization, 'DisabledIps':DisabledIps,
								 'StatusMetricaControl':StatusMetricaControl, 'DisabledDomains':DisabledDomains,
								 'StatusOpenStat':StatusOpenStat, 'ConsiderTimeTarget':ConsiderTimeTarget, 'MinusKeywords':MinusKeywords,
								 'AddRelevantPhrases':AddRelevantPhrases, 'RelevantPhrasesBudgetLimit':RelevantPhrasesBudgetLimit};
					
	                $('#btnFormAction').hide();
					$('img#imgBtnFormAction').show();
					$('#infoBtnFormAction').html('');
				    $.ajax({
                          url:'yad/yad.php',
                          dataType:'json',
			              cache: false, 
	                      type:'POST',
                          data:param,
                          complete:function(data){
						        $('#btnFormAction').show();
							    $('img#imgBtnFormAction').hide();
								$('#infoBtnFormAction').html(data.responseText);
						    } 
						  });   
					return false;	   
         }		 
	 });
    });
</script>";

echo '<script type="text/javascript">
    
    function  changeStrategyName(val){
	    hideStrategy();
		if(val == "ShowsDisabled") {
			;
		}else if(val == "WeeklyBudget") {
		    $("#blockWeeklySumLimit").show();
			$("#lblockWeeklySumLimit").show();
			$("#blockMaxPrice").show();
			$("#lblockMaxPrice").show();
		}else if(val == "CPAOptimizer") {
			$("#blockWeeklySumLimit").show();
			$("#lblockWeeklySumLimit").show();
			$("#blockGoalID").show();
			$("#lblockGoalID").show();
			$("#blockMaxPrice").show();
			$("#lblockMaxPrice").show();
			$("#blockAdditionalMetrikaCounters").show();
		    $("#lblockAdditionalMetrikaCounters").show();
		}else if(val == "AverageClickPrice") {
			$("#blockAveragePrice").show();
			$("#lblockAveragePrice").show();
			$("#blockWeeklySumLimit").show();
			$("#lblockWeeklySumLimit").show();
		}else if(val == "WeeklyPacketOfClicks") {
			$("#blockClicksPerWeek").show();
			$("#lblockClicksPerWeek").show();
			$("#blockMaxPrice").show();
			$("#lblockMaxPrice").show();
			$("#blockAveragePrice").show();
			$("#lblockAveragePrice").show();
		}
	}
	
	function changeContextStrategy(val){
	    hideContextStrategy();
		if(val == "ShowsDisabled") {
			;
		}else if(val == "WeeklyBudget") {
            $("#blockContextMaxPrice").show();
			$("#lblockContextMaxPrice").show();
			$("#blockContextWeeklySumLimit").show();
			$("#lblockContextWeeklySumLimit").show();
		}else if(val == "CPAOptimizer") {
            $("#blockContextMaxPrice").show();
			$("#lblockContextMaxPrice").show();
			$("#blockContextWeeklySumLimit").show();
			$("#lblockContextWeeklySumLimit").show();
			$("#blockContextGoalID").show();
			$("#lblockContextGoalID").show();
			$("#blockAdditionalMetrikaCounters").show();
			$("#lblockAdditionalMetrikaCounters").show();
		}else if(val == "AverageClickPrice") {
            $("#blockContextAveragePrice").show();
			$("#lblockContextAveragePrice").show();
			$("#blockContextWeeklySumLimit").show();
			$("#lblockContextWeeklySumLimit").show();
		}else if(val == "WeeklyPacketOfClicks") {
            $("#blockContextMaxPrice").show();
			$("#lblockContextMaxPrice").show();
			$("#blockContextAveragePrice").show();
			$("#lblockContextAveragePrice").show();
			$("#blockContextClicksPerWeek").show();
			$("#lblockContextClicksPerWeek").show();
		}
	}

	$("#ContextLimitSum").change(function(){
	    var cls = $("#ContextLimitSum :selected").val();
		if(cls > 0) {
		    var cl = $("#ContextLimit :selected").val();
			if(cl == "Default") {
			    alert("Ограничение бюджета на показ объявлений установлено как не ограниченный. Его статус будет сменен на ограниченный автоматически.");
			    $("#ContextLimit [value=\'Limited\']").attr("selected", "selected");
			}
		}
	});
	
    $("#Hours").change(function(){
		hideDiscontHours();
		var id;
		$("#Hours option:selected").each(function(){
			id = $(this).val();
			$("#blockDiscountHour_" + id).show();
		});
	});
	
	$("#StrategyName").change(function(){
		var val = $("#StrategyName :selected").val();
		changeStrategyName(val);
	});
	
	$("#ContextStrategyName").change(function(){
		var val = $("#ContextStrategyName :selected").val();
		changeContextStrategy(val);
	});
	
	$(document).ready(function(){
		hideStrategy();
		hideContextStrategy();
		hideDiscontHours();';
			
    if($id_company > 0) {	
       echo'
	        $("#Currency").find("option:contains(\''.$Currency.'\')").attr("selected", "selected");
			$("#StrategyName [value=\''.$StrategyName.'\']").attr("selected", "selected");
			$("#ContextStrategyName [value=\''.$ContextStrategyName.'\']").attr("selected", "selected");
			changeStrategyName(\''.$StrategyName.'\');
			changeContextStrategy(\''.$ContextStrategyName.'\');
			$("#ContextLimit [value=\''.$ContextLimit.'\']").attr("selected", "selected");
			$("#ContextLimitSum [value=\''.$ContextLimitSum.'\']").attr("selected", "selected");
			$("#ContextPricePercent [value=\''.$ContextPricePercent.'\']").attr("selected", "selected");
			var tmp = "'.$MetricaSms.'";
			if(tmp.indexOf("Yes") + 1) $("#MetricaSms").attr("checked","checked");
			else                       $("#MetricaSms").removeAttr("checked","checked");	
			tmp = "'.$ModerateResultSms.'";
			if(tmp.indexOf("Yes") + 1) $("#ModerateResultSms").attr("checked","checked");
			else                       $("#ModerateResultSms").removeAttr("checked","checked");	
			tmp = "'.$MoneyInSms.'";
			if(tmp.indexOf("Yes") + 1) $("#MoneyInSms").attr("checked","checked");
			else                       $("#MoneyInSms").removeAttr("checked","checked");	
			tmp = "'.$MoneyOutSms.'";
			if(tmp.indexOf("Yes") + 1) $("#MoneyOutSms").attr("checked","checked");
			else                       $("#MoneyOutSms").removeAttr("checked","checked");	
			$("#WarnPlaceInterval [value=\''.$WarnPlaceInterval.'\']").attr("selected", "selected");
			tmp = "'.$SendAccNews.'";
			if(tmp.indexOf("Yes") + 1) $("#SendAccNews").attr("checked","checked");
			else                       $("#SendAccNews").removeAttr("checked","checked");	
			tmp = "'.$SendWarn.'";
			if(tmp.indexOf("Yes") + 1) $("#SendWarn").attr("checked","checked");
			else                       $("#SendWarn").removeAttr("checked","checked");	
			tmp = "'.$StatusBehavior.'";
			if(tmp.indexOf("Yes") + 1) $("#StatusBehavior").attr("checked","checked");
			else                       $("#StatusBehavior").removeAttr("checked","checked");	
			tmp = "'.$ShowOnHolidays.'";
			if(tmp.indexOf("Yes") + 1) $("#ShowOnHolidays").attr("checked","checked");
			else                       $("#ShowOnHolidays").removeAttr("checked","checked");	
			$("#HolidayShowFrom [value=\''.$HolidayShowFrom.'\']").attr("selected", "selected");
			$("#HolidayShowTo [value=\''.$HolidayShowTo.'\']").attr("selected", "selected");';
			
		$counter =0;	
		foreach($Hours as $Hour){
		   echo '$("#Hours [value=\''.$Hour.'\']").attr("selected", "selected");';
		   echo '$("#blockDiscountHour_'.$Hour.'").show();';
		   echo '$("#discountHour_'.$Hour.' [value=\''.$BidCoefs[$counter++].'\']").attr("selected", "selected");';
		}
		foreach($Days as $Day){
		   echo '$("#Days [value=\''.$Day.'\']").attr("selected", "selected");';
		}
		
		echo 'tmp = "'.$WorkingHolidays.'";
			  if(tmp.indexOf("Yes") + 1) $("#WorkingHolidays").attr("checked","checked");
			  else                       $("#WorkingHolidays").removeAttr("checked","checked");	
			  tmp = "'.$AutoOptimization.'";
			  if(tmp.indexOf("Yes") + 1) $("#AutoOptimization").attr("checked","checked");
			  else                       $("#AutoOptimization").removeAttr("checked","checked");	
			  tmp = "'.$StatusMetricaControl.'";
			  if(tmp.indexOf("Yes") + 1) $("#StatusMetricaControl").attr("checked","checked");
			  else                       $("#StatusMetricaControl").removeAttr("checked","checked");	
			  $("#StatusOpenStat [value=\''.$StatusOpenStat.'\']").attr("selected", "selected");
			  $("#ConsiderTimeTarget [value=\''.$ConsiderTimeTarget.'\']").attr("selected", "selected");
			  $("#AddRelevantPhrases [value=\''.$AddRelevantPhrases.'\']").attr("selected", "selected"); 
			  $("#RelevantPhrasesBudgetLimit [value=\''.$RelevantPhrasesBudgetLimit.'\']").attr("selected", "selected"); 
			';
    }	
        				
echo '});			
</script>';

    if($id_company == 0) 
	    echo '<center><h2 style="margin-top:0px;">Создание новой компании</h2></center>';
	else
        echo '<center><h2 style="margin-top:0px;">Редактирование существующей компании</h2></center>';	
	//Форма создания и редактирования	
	echo '<div class="blockBody">
	      <form id="actionFormCreateOrUpdateCampaign">
		  <table style="width:100%;">';  

	if($id_company != 0) {
        echo '<tr> 
		           <td><label>Идентификатор кампании: <b>'.$id_company.'</b></label></td>
		           <td><input id="CampaignID" disabled="disabled" name="CampaignID" type="text" value="'.$id_company.'" /></td>
		      </tr>
			  <tr><td colspan="2"><hr /></td></tr>';
	}else{
	    echo '<input id="CampaignID" name="CampaignID" type="hidden" value="0" />';
	}
	
	echo '<tr> 
		  <td><label for="Login"><span style="color:red">* </span>Логин владельца кампании</label></td>
		  <td><input id="Login" name="Login" type="text" value="'.$Login.'" /></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label for="Name"><span style="color:red">* </span>Название кампании</label></td>
		  <td><input id="Name" name="Name" type="text" value="'.$Name.'" /></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label for="FIO"><span style="color:red">* </span>Имя и фамилия владельца кампании</label></td>
		  <td><input id="FIO" name="FIO" type="text" value="'.$FIO.'" /></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label>Начало показа объявлений, YYYY-MM-DD</label></td>
		  <td><input id="StartDate" name="StartDate" type="text" value="'.$StartDate.'" /></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label>Валюта</label></td>
		  <td><select name="Currency" id="Currency">
		           <option value="USD">USD</option>
				   <option value="RUB">RUB</option>
				   <option value="EUR">EUR</option>
				   <option value="CHF">CHF</option>
				   <option value="KZT">KZT</option>
				   <option value="TRY">TRY</option>
				   <option value="UAH">UAH</option>
			  </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  </table>
		  <table style="background:#CCCCCC;width:100%;">
		  <tr><td colspan="2"><center><h3>Стратегия поиска яндекса</h3></center></td></tr>
		  <tr> 
		  <td><label for="StrategyName"><span style="color:red">* </span>Стратегия поиска яндекса</label></td>
		  <td>
		      <select name="StrategyName" id="StrategyName">
			      <option value="ShowsDisabled">Выключить показ объявлений на поиске</option>
				  <option value="" disabled="disabled">Стратегии с ручным управлением ставками на поиске</option>
				  <option value="HighestPosition">Наивысшая доступная позиция</option>
				  <option value="LowestCost">Показ в блоке по минимальной цене</option>
				  <option value="LowestCostPremium">Показ в блоке по минимальной цене, показываются в спецразмещении</option>
				  <option value="LowestCostGuarantee">Под результатами поиска (в нижнем блоке по наименьшей цене)</option>
				  <option value="RightBlockHighest">Под результатами поиска (в нижнем блоке на наивысшей позиции)</option>
				  <option value="" disabled="disabled">Автоматические стратегии на поиске</option>
				  <option value="WeeklyBudget">Недельный бюджет: максимум кликов</option>
				  <option value="CPAOptimizer">Недельный бюджет: максимальная конверсия</option>
				  <option value="AverageClickPrice">Средняя цена клика</option>
				  <option value="WeeklyPacketOfClicks">Недельный пакет кликов</option>
			  </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr id="blockMaxPrice"> 
		  <td><label>Максимальная ставка</label></td>
		  <td><input id="MaxPrice" name="MaxPrice" type="text" value="'.$MaxPrice.'" /></td>
		  </tr>
		  <tr id="lblockMaxPrice"><td colspan="2"><hr /></td></tr>
		  <tr id="blockAveragePrice"> 
		  <td><label><span style="color:red">* </span>Средняя ставка</label></td>
		  <td><input id="AveragePrice" name="AveragePrice" type="text" value="'.$AveragePrice.'" /></td>
		  </tr>
		  <tr id="lblockAveragePrice"><td colspan="2"><hr /></td></tr>
		  <tr id="blockWeeklySumLimit"> 
		  <td><label><span style="color:red">* </span>Максимальный недельный бюджет</label></td>
		  <td><input id="WeeklySumLimit" name="WeeklySumLimit" type="text" value="'.$WeeklySumLimit.'" /></td>
		  </tr>
		  <tr id="lblockWeeklySumLimit"><td colspan="2"><hr /></td></tr>
		  <tr id="blockClicksPerWeek"> 
		  <td><label><span style="color:red">* </span>Количество кликов в неделю</label></td>
		  <td><input id="ClicksPerWeek" name="ClicksPerWeek" type="text" value="'.$ClicksPerWeek.'" /></td>
		  </tr>
		  <tr id="lblockClicksPerWeek"><td colspan="2"><hr /></td></tr>
		  <tr id="blockGoalID"> 
		  <td><label><span style="color:red">* </span>Идентификатор <a href="http://help.yandex.ru/metrika/general/goals.xml" title="Цели" target="_blanc">цели</a> Яндекс</label></td>
		  <td><input id="GoalID" name="GoalID" type="text" value="'.$GoalID.'" /><input id="getTsel_1" type="button" value="Обзор" onclick="getTsel(1, 0);" /><img id="imgGetTsel_1" src="images/loading.gif" alt="" /></td>
		  </tr>
		  <tr id="lblockGoalID"><td colspan="2"><hr /></td></tr>
		  </table>
		  <table style="background:#CCCC99;width:100%;">
		  <tr><td colspan="2"><center><h3>Стратегия в рекламной сети Яндекса</h3></center></td></tr>
		  <tr> 
		  <td><label for="ContextStrategyName"><span style="color:red">* </span>Стратегия в рекламной сети</label></td>
		  <td>
		      <select name="ContextStrategyName" id="ContextStrategyName">
			      <option value="ShowsDisabled">Выключить показ объявлений в рекламной сети</option>
				  <option value="" disabled="disabled">Стратегии с ручным управлением ставками</option>
				  <option value="Default">Процент от цены на поиске</option>
				  <option value="MaximumCoverage">Максимальный доступный охват</option>
				  <option value="" disabled="disabled">Автоматические стратегии в рекламной сети</option>
				  <option value="WeeklyBudget">Недельный бюджет: максимум кликов</option>
				  <option value="CPAOptimizer">Недельный бюджет: максимальная конверсия</option>
				  <option value="AverageClickPrice">Средняя цена клика</option>
				  <option value="WeeklyPacketOfClicks">Недельный пакет кликов</option>
			  </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr id="blockContextLimit"> 
		  <td><label>Ограничение бюджета на показ объявлений</label></td>
		  <td><select id="ContextLimit" name="ContextLimit">
		          <option value="Default">Бюджет не ограничен </option>
				  <option value="Limited">Бюджет ограничен</option>
			  </select>
		  </td>
		  </tr>
		  <tr id="lblockContextLimit"><td colspan="2"><hr /></td></tr>
		  <tr id="blockContextLimitSum"> 
		  <td><label>Максимальный процент бюджета</label></td>
		  <td><select id="ContextLimitSum" name="ContextLimitSum">
		          <option value="10">10%</option>
				  <option value="20">20%</option>
				  <option value="30">30%</option>
				  <option value="40">40%</option>
				  <option value="50">50%</option>
				  <option value="60">60%</option>
				  <option value="70">70%</option>
				  <option value="80">80%</option>
				  <option value="90">90%</option>
				  <option value="100">100%</option>
				  <option value="">Бюджет не ограничен</option>
		      </select>
		  </td>
		  </tr>
		  <tr id="lblockContextLimitSum"><td colspan="2"><hr /></td></tr>
		  <tr id="blockContextPricePercent"> 
		  <td><label>Параметр для вычисления цены за клик <br /> на тематических площадках</label></td>
		  <td><select id="ContextPricePercent" name="ContextPricePercent">
		          <option value="10">10%</option>
				  <option value="20">20%</option>
				  <option value="30">30%</option>
				  <option value="40">40%</option>
				  <option value="50">50%</option>
				  <option value="60">60%</option>
				  <option value="70">70%</option>
				  <option value="80">80%</option>
				  <option value="90">90%</option>
				  <option value="100">100%</option>
		      </select>
		  </td>
		  </tr>
		  <tr id="lblockContextPricePercent"><td colspan="2"><hr /></td></tr>
		  <tr id="blockContextMaxPrice"> 
		  <td><label>Максимальная ставка</label></td>
		  <td><input id="ContextMaxPrice" name="ContextMaxPrice" type="text" value="'.$ContextMaxPrice.'" /></td>
		  </tr>
		  <tr id="lblockContextMaxPrice"><td colspan="2"><hr /></td></tr>
		  <tr id="blockContextAveragePrice"> 
		  <td><label for="ContextAveragePrice"><span style="color:red">* </span>Средняя ставка</label></td>
		  <td><input id="ContextAveragePrice" name="ContextAveragePrice" type="text" value="'.$ContextAveragePrice.'" /></td>
		  </tr>
		  <tr id="lblockContextAveragePrice"><td colspan="2"><hr /></td></tr>
		  <tr id="blockContextWeeklySumLimit"> 
		  <td><label for="ContextWeeklySumLimit"><span style="color:red">* </span>Максимальный недельный бюджет</label></td>
		  <td><input id="ContextWeeklySumLimit" name="ContextWeeklySumLimit" type="text" value="'.$ContextWeeklySumLimit.'" /></td>
		  </tr>
		  <tr id="lblockContextWeeklySumLimit"><td colspan="2"><hr /></td></tr>
		  <tr id="blockContextClicksPerWeek"> 
		  <td><label for="ContextClicksPerWeek"><span style="color:red">* </span>Количество кликов в неделю</label></td>
		  <td><input id="ContextClicksPerWeek" name="ContextClicksPerWeek" type="text" value="'.$ContextClicksPerWeek.'" /></td>
		  </tr>
		  <tr id="lblockContextClicksPerWeek"><td colspan="2"><hr /></td></tr>
		  <tr id="blockContextGoalID"> 
		  <td><label for="ContextGoalID"><span style="color:red">* </span>Идентификатор <a href="http://help.yandex.ru/metrika/general/goals.xml" title="Цели" target="_blanc">цели</a> Яндекс</label></td>
		  <td><input id="ContextGoalID" name="ContextGoalID" type="text" value="'.$ContextGoalID.'" /><input id="getTsel_2" type="button" value="Обзор" onclick="getTsel(2, 0);" /><img id="imgGetTsel_2" src="images/loading.gif" alt="" /></td>
		  </tr>
		  <tr id="lblockContextGoalID"><td colspan="2"><hr /></td></tr>
		  <tr id="blockAdditionalMetrikaCounters"> 
		  <td><label>Массив, содержащий идентификаторы <br />счетчиков Яндекс.Метрики.</label></td>
		  <td><textarea id="AdditionalMetrikaCounters" name="AdditionalMetrikaCounters" type="text">'.$AdditionalMetrikaCounters.'</textarea></td>
		  </tr>
		  <tr id="lblockAdditionalMetrikaCounters"><td colspan="2"><hr /></td></tr>
		  </table>
		  <table style="width:100%;background:#CCCCFF;">
		  <tr><td colspan="2"><center><h3>СМС - настройка уведомлений</h3></center></td></tr>
		  <tr>
		  <td colspan="2">
		      <input id="MetricaSms" name="MetricaSms" type="checkbox" value="Yes" />
              <label for="MetricaSms">Сообщать результаты мониторинга сайтов по данным Яндекс.Метрики</label> 
		  </td>
	      </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td colspan="2">
		      <input id="ModerateResultSms" name="ModerateResultSms" type="checkbox" value="Yes" />
              <label for="ModerateResultSms">Сообщать результаты модерации объявлений</label> 
		  </td>
	      </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td colspan="2">
		      <input id="MoneyInSms" name="MoneyInSms" type="checkbox" value="Yes" />
              <label for="MoneyInSms">Сообщать о зачислении средств на баланс кампании</label> 
		  </td>
	      </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td colspan="2">
		      <input id="MoneyOutSms" name="MoneyOutSms" type="checkbox" value="Yes" />
              <label for="MoneyOutSms">Сообщать об исчерпании средств на балансе кампании</label> 
		  </td>
	      </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td>
		  <label>Время, начиная с которого разрешено отправлять sms, <br />в формате HH:MM. Минуты задают кратно 15 (0, 15, 30, 45).</label> 
          </td>
		  <td>
		  <input id="SmsTimeFrom" name="SmsTimeFrom" type="text" value="'.$SmsTimeFrom.'" />
		  </td>
	      </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td>
		  <label>Время, до которого разрешено отправлять sms, <br />в формате HH:MM. Минуты задают кратно 15 (0, 15, 30, 45).</label> 
          </td>
		  <td>
		  <input id="SmsTimeTo" name="SmsTimeTo" type="text" value="'.$SmsTimeTo.'" />
		  </td>
	      </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  </table>
		  <table style="width:100%;background:#CCCC66">
		  <tr><td colspan="2"><center><h3>Настройка Email уведомлений</h3></center></td></tr>
		  <tr>
		  <td><label for="Email"><span style="color:red">* </span>Адрес электронной почты для отправки уведомлений</label></td>
		  <td><input id="Email" type="text" name="Email" value="'.$Email.'" /></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td>
		  <label for="WarnPlaceInterval"><span style="color:red">* </span>Периодичность проверки позиции объявления<br /> — 15, 30 или 60 минут.<br />
                        Уведомление отправляется, если объявление переместилось <br />на более низкую позицию чем та, <br />которую обеспечивала ставка на момент установки.</label>
		  </td>
		  <td>
		      <select id="WarnPlaceInterval" name="WarnPlaceInterval">
			      <option value="15">15 минут</option>
				  <option value="30">30 минут</option>
				  <option value="60">60 минут</option>
			  </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td><label for="MoneyWarningValue"><span style="color:red">* </span>Минимальный баланс, при уменьшении до которого<br /> отправляется уведомление. <br />Задается в процентах от суммы последнего платежа</label></td>
		  <td><input type="text" value="'.$MoneyWarningValue.'" id="MoneyWarningValue" name="MoneyWarningValue" /></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td colspan="2">
		     <input type="checkbox" name="SendAccNews" id="SendAccNews" />
		     <label for="SendAccNews">Сообщать о событиях, связанных с кампанией. Задается для кампаний с тарифом «Беззаботный»</label>
		  </td>
		  </tr>
          <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td colspan="2">
		     <input type="checkbox" name="SendWarn" id="SendWarn" />
		     <label for="SendWarn">Отправлять уведомления по электронной почте</label>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  </table>
		  <table style="width:100%;">
		  <tr> 
		    <td colspan="2">
		       <input id="StatusBehavior" name="StatusBehavior" type="checkbox" value="Yes" />
		       <label for="StatusBehavior">Включить <a href="http://help.yandex.ru/direct/?id=998169" title="поведенческий таргетинг" target="_blanc">поведенческий таргетинг</a></label> 
		    </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  </table>
		  <table style="width:100%;background:#6699CC;">
		  <tr><td colspan="2"><center><h3>Параметры временного таргетинга</h3></center></td></tr>
		  <tr> 
		    <td colspan="2">
		       <input id="ShowOnHolidays" name="ShowOnHolidays" type="checkbox" value="Yes" />
		       <label for="ShowOnHolidays">Показывать объявления в праздничные нерабочие дни</label> 
		    </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		      <td>Час, начиная с которого объявления показываются <br />в праздничные нерабочие дни. <br />Если параметр отсутствует, <br />показы идут по расписанию из параметра показов.</td>
              <td><select id="HolidayShowFrom" name="HolidayShowFrom">
			    <option value="">Из расписания показов</option>
			    <option value="0">c 0 часа</option>
                <option value="1">c 1 часa</option>
                <option value="2">c 2 часов</option>
				<option value="3">c 3 часов</option>
				<option value="4">c 4 часов</option>
				<option value="5">c 5 часов</option>
				<option value="6">c 6 часов</option>
				<option value="7">c 7 часов</option>
				<option value="8">c 8 часов</option>
				<option value="9">c 9 часов</option>
				<option value="10">c 10 часов</option>
				<option value="11">c 11 часов</option>
				<option value="12">c 12 часов</option>
				<option value="13">c 13 часов</option>
				<option value="14">с 14 часов</option>
				<option value="15">с 15 часов</option>
				<option value="16">с 16 часов</option>
				<option value="17">с 17 часов</option>
				<option value="18">с 18 часов</option>
				<option value="19">с 19 часов</option>
				<option value="20">с 20 часов</option>
				<option value="21">с 21 час</option>
				<option value="22">с 22 часа</option>
				<option value="23">с 23 часа</option>		
			  </select></td>		  
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td><label>Час, до которого объявления показываются <br />в праздничные нерабочие дни. <br />Если параметр отсутствует, показы идут <br />по расписанию из параметра показов.</label></td>
		  <td>
		      <select id="HolidayShowTo" name="HolidayShowTo">
			    <option value="">Из расписания показов</option>
			    <option value="0">до 0 часов</option>
                <option value="1">до 1 часов</option>
                <option value="2">до 2 часов</option>
				<option value="3">до 3 часов</option>
				<option value="4">до 4 часов</option>
				<option value="5">до 5 часов</option>
				<option value="6">до 6 часов</option>
				<option value="7">до 7 часов</option>
				<option value="8">до 8 часов</option>
				<option value="9">до 9 часов</option>
				<option value="10">до 10 часов</option>
				<option value="11">до 11 часов</option>
				<option value="12">до 12 часов</option>
				<option value="13">до 13 часов</option>
				<option value="14">до 14 часов</option>
				<option value="15">до 15 часов</option>
				<option value="16">до 16 часов</option>
				<option value="17">до 17 часов</option>
				<option value="18">до 18 часов</option>
				<option value="19">до 19 часов</option>
				<option value="20">до 20 часов</option>
				<option value="21">до 21 часа</option>
				<option value="22">до 22 часов</option>
				<option value="23">до 23 часов</option>		
			  </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><center><h3>Расписание показов</h3></center></td></tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
            <td><label><span style="color:red">* </span>Временная зона <br />Число 0 соответствует интервалу времени <br />с 00:00 по 00:59 включительно, <br />число 1 — с 01:00 по 01:59 включительно <br />и так далее.</label></td> 
		    <td>
			<select multiple="multiple" size="8" id="Hours" name="Hours" >
			     <option value="0">c 0 до 1</option>
				 <option value="1">c 1 до 2</option>
				 <option value="2">c 2 до 3</option>
				 <option value="3">c 3 до 4</option>
				 <option value="4">c 4 до 5</option>
				 <option value="5">c 5 до 6</option>
				 <option value="6">c 6 до 7</option>
				 <option value="7">c 7 до 8</option>
				 <option value="8">c 8 до 9</option>
				 <option value="9">c 9 до 10</option>
				 <option value="10">c 10 до 11</option>
				 <option value="11">c 11 до 12</option>
				 <option value="12">c 12 до 13</option>
				 <option value="13">c 13 до 14</option>
				 <option value="14">c 14 до 15</option>
				 <option value="15">c 15 до 16</option>
				 <option value="16">c 16 до 17</option>
				 <option value="17">c 17 до 18</option>
				 <option value="18">c 18 до 19</option>
				 <option value="19">c 19 до 20</option>
				 <option value="20">c 20 до 21</option>
				 <option value="21">c 21 до 22</option>
				 <option value="22">c 22 до 23</option>
				 <option value="23">c 23 до 0</option>
			</select>
			</td>
          </tr>		
          <tr><td colspan="2"><hr /></td></tr>		  
		  <tr>		
		  <td><label><span style="color:red">* </span>В указанные дни объявления показываются <br />в соответствии со значением временной зоны</label></td> 	
		  <td>
			<select multiple="multiple" size="7" id="Days" name="Days">
				 <option value="1">Понедельник</option>
				 <option value="2">Вторник</option>
				 <option value="3">Среда</option>
				 <option value="4">Четверг</option>
				 <option value="5">Пятница</option>
				 <option value="6">Суббота</option>
				 <option value="7">Воскресенье</option>
			</select>
			</td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>	 
		  <tr>
		  <td><label>Массив коэффициентов, которые <br />уменьшают цены за клик в определенные часы. <br />Применяется для стратегий с ручным управлением ставками. <br />Коэффициенты указывают в процентах от 0 до 100.<br /><span class="soft-text">Поля отображаются в зависимости от <br />выбора промежутков часов временной зоны</span></label></td>
		  <td>';
	for($iHour=0; $iHour<=23; $iHour++){
		echo '<p id="blockDiscountHour_'.$iHour.'"><span class="soft-text">Уменьшить с '.$iHour.' до '.($iHour+1).' часа на: </span>';
		if($iHour <= 9) echo '&nbsp;&nbsp;&nbsp;';      
		echo '<select id="discountHour_'.$iHour.'" name="discountHour_'.$iHour.'">';
		for($iProc=0; $iProc<=100; $iProc+=10){
			echo '<option value="'.$iProc.'">'.$iProc.'%</option>';
		};
		echo '</select></p>';
	};
    echo '</td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>	
		  <tr>
		      <td><label>Временная зона в месте нахождения <br />владельца рекламной кампании. </label></td>
			  <td><input type="text" value="'.$TimeZone.'" id="TimeZone" name="TimeZone" /><input id="btnGetTimeZone" type="button" onclick="getTimeZone();" value="Обзор" /><img id="imgGetTimeZone" src="images/loading.gif" alt="" /></td>
		  </tr>
		  </table>
		  <table style="width:100%;">
		  <tr>
		  <td colspan="2"><input id="WorkingHolidays" name="WorkingHolidays" type="checkbox" />
		  <label for="WorkingHolidays">Менять расписание показов при переносе рабочего дня на субботу или воскресение. <br />Например, если рабочий день перенесен с понедельника на субботу, <br />при значении Yes в рабочую субботу пойдут показы по расписанию понедельника, <br />а в нерабочий понедельник, — по расписанию субботы.</label></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr>
		  <td colspan="2">
		  <input name="AutoOptimization" id="AutoOptimization" type="checkbox" />
		  <label for="AutoOptimization">Включить <a href="http://direct.yandex.ru/help/?id=990425" title="автоматическое уточнение фраз" target="_blanc">автоматическое уточнение фраз</a></label>
		  </td>
		  </tr>
          <tr><td colspan="2"><hr /></td></tr>
          <tr>
		  <td colspan="2">
		  <input name="StatusMetricaControl" id="StatusMetricaControl" type="checkbox" />
		  <label for="StatusMetricaControl">Останавливать показы при недоступности сайта рекламодателя. <br />Недоступность выявляется по результатам мониторинга, проводимого Директом.</label>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label>Список доменных имен в Рекламной сети Яндекса,<br /> на которых не показывать объявления. <br />Домены указывают через запятую, <br />например renother.ru,verbidol.su</label></td>
		  <td><textarea id="DisabledDomains" name="DisabledDomains" type="text">'.$DisabledDomains.'</textarea></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label>Список IP-адресов, <br />которым не нужно показывать объявления. <br />Адреса указывают через запятую, <br />например 127.0.0.1,127.0.0.2</label></td>
		  <td><textarea id="DisabledIps" name="DisabledIps" type="text">'.$DisabledIps.'</textarea></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label>При переходе на сайт рекламодателя <br />добавлять к URL метку в формате <a href="http://direct.yandex.ru/help/?id=990428">OpenStat</a></label></td>
		  <td><select name="StatusOpenStat" id="StatusOpenStat">
		          <option value="No">Нет</option>
				  <option value="Yes">Да</option>
		      </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label>Рассчитывать цены позиций показа <br />без учета ставок в остановленных объявлениях <br />конкурентов (остановлены в соответствии <br />с расписанием)</label></td>
		  <td><select name="ConsiderTimeTarget" id="ConsiderTimeTarget">
		          <option value="No">Нет</option>
				  <option value="Yes">Да</option>
		      </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label>Массив минус-слов, <br />общих для всех объявлений кампании. Писать через запятую.</label></td>
		  <td><textarea id="MinusKeywords" name="MinusKeywords" type="text">'.$MinusKeywords.'</textarea></td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label>Добавлять релевантные фразы к объявлениям</label></td>
		  <td><select name="AddRelevantPhrases" id="AddRelevantPhrases">
		          <option value="No">Нет</option>
				  <option value="Yes">Да</option>
		      </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  <tr> 
		  <td><label>Максимальный бюджет, <br />расходуемый за клики по <a href="http://help.yandex.ru/direct/?id=1112778" title="релевантные фразы" target="_blanc">релевантным фразам</a>. <br />Указывается в процентах <br />от расхода на поиске кратно 10.</label></td>
		  <td><select id="RelevantPhrasesBudgetLimit" name="RelevantPhrasesBudgetLimit">
		       <option value="100">100%(бюджет не ограничен)</option>
			   <option value="90">90%</option>
			   <option value="80">80%</option>
			   <option value="70">70%</option>
			   <option value="60">60%</option>
			   <option value="50">50%</option>
			   <option value="40">40%</option>
			   <option value="30">30%</option>
			   <option value="20">20%</option>
			   <option value="10">10%</option>
		      </select>
		  </td>
		  </tr>
		  <tr><td colspan="2"><hr /></td></tr>
		  
          ';
    
	if($id_company == 0) $btnName = "Создать";
	else                 $btnName = "Редактировать";
	echo '</table>
	      <center>
	      <div id="infoBtnFormAction"></div>
	      <input style="padding:20px;font-size:20px;" id="btnFormAction" type="submit" value="'.$btnName.'" />
		  <img id="imgBtnFormAction" src="images/loading.gif" alt="" />
		  </center>
	      </form>
	      </div>';
}
//----------------------------------------------------------


?>