<?
session_start();
$_SESSION[client_id] = "cbc588c02426435dbb452d59c6bd3a63";
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Управление Яндекс директом - YaD</title>
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/dark.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/simple.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/jquery.arcticmodal-0.3.css" rel="stylesheet" type="text/css" media="all" />
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/ajaxupload.js" type="text/javascript"></script>
<script src="js/jquery.form.js" type="text/javascript"></script>
<script src="js/jquery.validate.js" type="text/javascript"></script>
<script src="js/xmlHttpRequest.js" type="text/javascript"></script>
<script src="js/lib.js" type="text/javascript"></script>
<script src="js/jquery.arcticmodal-0.3.min.js" type="text/javascript"></script> 
<script type="text/javascript">
function start(){
    window.location.href = "https://oauth.yandex.ru/authorize?response_type=token&client_id=<?=$_SESSION[client_id]?>&display=&state="
}
</script>
</head>
<body>
<div class="g-hidden">
	<div class="box-modal" id="exampleModal1">
		<div class="box-modal_close arcticmodal-close">закрыть</div>
		<div id="infoAlertBox"></div>
	</div>
</div>
<br /><br /><br />
<h1 style="margin-top:-40px;">Управление <b style="color:black;"><span style="color:red;">Я</span>ндекс</b> директом <span style="color:#CCCCCC;">&lt;</span><span style="color:red;">Ya</span><span style="color:#333333;">D</span><span style="color:#CCCCCC;">&gt;</span></h1>
<hr style="position:relative; z-index:1;" />

<?  if(isset($_GET['access_token'])) { 
         $_SESSION[token] = $_GET['access_token']; 
		 header('Location: /yad');
    }
?>
    
<?if(empty($_SESSION[token])):?>
    <input type="button" value="Начать работу" onclick="start();" />
<?else:?>
    <div class="block">
	      <a href="/yad?logout=1" title="Выход">Выход</a><br /><br />
	     <center><img id="imgGetCampaignsList" src="images/loading.gif" alt="" /></center>
	     <input style="width:100%;" id="btnGetCampaignsList" type="button" value="Получить список компаний" onclick="getCampaignsList();" />
	     <hr />
		 <center><img id="imgCreateOrUpdateCampaign" src="images/loading.gif" alt="" /></center>
	     <input style="width:100%;" id="btnCreateOrUpdateCampaign" type="button" value="Создать новую кампанию" onclick="showFormCreateOrUpdateCampaign(0);" />
         <hr />
		 <center><img id="imgCreateOrUpdateBanners" src="images/loading.gif" alt="" /></center>
	     <input style="width:100%;" id="btnCreateOrUpdateBanners" type="button" value="Создать объявления" onclick="showFormCreateOrUpdateBanners();" />
         <hr />
		 <center>
		 <label>Логины через запятую</label>
		 <input type="text" name="loginList" id="loginList" value="" /><br />
		 <img id="imgGetClientsUnits" src="images/loading.gif" alt="" /></center>
		 <input style="width:100%;" id="btnGetClientsUnits" type="button" value="Узнать количество баллов" onclick="getClientsUnits();" />
 
	</div>
	<div class="block_2">
	    <div id="infoBody"></div>
	</div>
	<div style="clear:both;"></div>
<?endif;?>
</body>
</html>

<?
   if( isset($_GET['logout']) && $_GET['logout']=1 ){
       // Очистить данные сессии для текщуего сценария
       $_SESSION = array();
       // Удалить Cokkie, соответсвующую SID
       unset($_COOKIE[session_name()]);
       // Уничтожить хранилище сессии
       session_destroy();
       header('Location: /yad');
   }
?>