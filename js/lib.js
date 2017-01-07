$(document).ready(function(){
    $('img#imgGetCampaignsList').hide();
	$('img#imgCreateOrUpdateCampaign').hide();
	$('img#imgCreateOrUpdateBanners').hide();
	$('img#imgGetClientsUnits').hide();
});
//-------------------------------------------------------
if (window.location.hash != ""){
    var uri = window.location.hash.replace(new RegExp("#","g"),"");
	if(uri.indexOf('access_token=') + 1)
	    window.location.href = "?" + uri;
}
//-------------------------------------------------------
function getClientsUnits(){
    $('img#imgGetClientsUnits').show();
	$('#btnGetClientsUnits').hide();
	$('#infoBody').html('');
	var loginList = $('#loginList').val();
    var param = {'method':'GetClientsUnits','loginList':loginList}
    $.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
				$('#infoBody').html(data.responseText);
				$('img#imgGetClientsUnits').hide();
				$('#btnGetClientsUnits').show();
			}
	});
	return false;
}
//-------------------------------------------------------
function getCampaignsList(){
    $('img#imgGetCampaignsList').show();
	$('#btnGetCampaignsList').hide();
	$('#infoBody').html('');
    var param = {'method':'GetCampaignsList'}
    $.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
				$('#infoBody').html(data.responseText);
				$('img#imgGetCampaignsList').hide();
				$('#btnGetCampaignsList').show();
			}
	});
	return false;
}
//-------------------------------------------------------
//Разрешить показы
function resumeCampaign(id_company){
    if (!confirm('Точно разрешить показы компании?')) return false;
	var param = {'method':'ResumeCampaign','id_company':id_company}
	$.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
				$('#infoAlertBox').html(data.responseText);
				$('#exampleModal1').arcticmodal();
			}
	});	
    return false;
}
//-------------------------------------------------------
//Остановить показы
function stopCampaign(id_company){
    if (!confirm('Точно остановить показы компании?')) return false;
	var param = {'method':'StopCampaign','id_company':id_company}
	$.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
			    $('#infoAlertBox').html(data.responseText);
				$('#exampleModal1').arcticmodal();
			}
	});	
    return false;
}
//-------------------------------------------------------
//Удалить компанию
function removeCampaign(id_company){
    if (!confirm('Точно удалить компанию?')) return false;
    var param = {'method':'DeleteCampaign','id_company':id_company}
	$.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
			    getCampaignsList();
			}
	});	
    return false;	
}
//-------------------------------------------------------
//Создать или обновить компанию
function showFormCreateOrUpdateCampaign(id_company){
    $('img#imgCreateOrUpdateCampaign').show();
	$('#btnCreateOrUpdateCampaign').hide();
	$('#infoBody').html('');
    var param = {'method':'showFormCreateOrUpdateCampaign','id_company':id_company}
    $.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
				$('#infoBody').html(data.responseText);
				$('img#imgCreateOrUpdateCampaign').hide();
				$('#btnCreateOrUpdateCampaign').show();
			}
	});
	return false;
}
//-------------------------------------------------------
//Создать обявления
function showFormCreateOrUpdateBanners(){
    $('img#imgCreateOrUpdateBanners').show();
	$('#btnCreateOrUpdateBanners').hide();
	$('#infoBody').html('');
    var param = {'method':'showFormCreateOrUpdateBanners'}
    $.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
				$('#infoBody').html(data.responseText);
				$('img#imgCreateOrUpdateBanners').hide();
				$('#btnCreateOrUpdateBanners').show();
			}
	});
	return false;
}
//-------------------------------------------------------
//Получить цели
function getTsel(val, id_company){
    $("img#imgGetTsel_"+val).show();
	$("#getTsel_"+val).hide();
	if(id_company == 0) id_company ="";
    var param = {'method':'GetStatGoals','id_company':id_company,'val':val}
    $.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
				$('#infoAlertBox').html(data.responseText);
				$("img#imgGetTsel_"+val).hide();
				$('#exampleModal1').arcticmodal();
				$("#getTsel_"+val).show();
			}
	});
	return false;
}
//-------------------------------------------------------
function setGoal(goalID, val){
    if(val == 1)
	    $('#GoalID').val(goalID);
	else   
	    $('#ContextGoalID').val(goalID);

	$('#exampleModal1').arcticmodal("close");	
}
//-------------------------------------------------------
function getRubrics(){
    $("img#imgBtn_b_rubric_catalog").show();
	$("#btn_b_rubric_catalog").hide();
    var param = {'method':'GetRubrics'}
    $.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
				$('#infoAlertBox').html(data.responseText);
				$("img#imgBtn_b_rubric_catalog").hide();
				$('#exampleModal1').arcticmodal();
				$("#btn_b_rubric_catalog").show();
			}
	});
	return false;
}
//-------------------------------------------------------
function getRegions(){
    $("img#imgGetRegions").show();
	$("#btnGetRegions").hide();
    var param = {'method':'GetRegions'}
    $.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
				$('#infoAlertBox').html(data.responseText);
				$("img#imgGetRegions").hide();
				$('#exampleModal1').arcticmodal();
				$("#btnGetRegions").show();
			}
	});
	return false;
}
//-------------------------------------------------------
function getTimeZone(){
    $("img#imgGetTimeZone").show();
	$("#btnGetTimeZone").hide();
    var param = {'method':'GetTimeZones'}
    $.ajax({
            url:'yad/yad.php',
            dataType:'json',
			cache: false, 
	        type:'POST',
            data:param,
            complete:function(data){
				$('#infoAlertBox').html(data.responseText);
				$("img#imgGetTimeZone").hide();
				$('#exampleModal1').arcticmodal();
				$("#btnGetTimeZone").show();
			}
	});
	return false;
}
//-------------------------------------------------------
function setRubric(val){
    $('#b_rubric_catalog').val(val);
	$('#exampleModal1').arcticmodal("close");
}
//-------------------------------------------------------
function setRegion(val){
    var regions = $('#b_regions').val();
	if(regions) $('#b_regions').val(regions + ',' + val); 
	else $('#b_regions').val(val);
	$('#exampleModal1').arcticmodal("close");
}
//-------------------------------------------------------
function unSetRegion(val){
    var regions = $('#b_regions').val();
	if(regions) $('#b_regions').val(regions + ',-' + val); 
	else $('#b_regions').val('-',val);
	$('#exampleModal1').arcticmodal("close");
}
//-------------------------------------------------------
function setTimeZone(valTimeZone){
    $('#TimeZone').val(valTimeZone);
	$('#exampleModal1').arcticmodal("close");
}
//-------------------------------------------------------
function hideContextStrategy(){
				    $('#blockContextMaxPrice').hide();
			        $('#lblockContextMaxPrice').hide();
					$('#blockContextAveragePrice').hide();
			        $('#lblockContextAveragePrice').hide();
					$('#blockContextWeeklySumLimit').hide();
			        $('#lblockContextWeeklySumLimit').hide();
					$('#blockContextClicksPerWeek').hide();
			        $('#lblockContextClicksPerWeek').hide();
					$('#blockContextGoalID').hide();
			        $('#lblockContextGoalID').hide();
					$('#blockAdditionalMetrikaCounters').hide();
			        $('#lblockAdditionalMetrikaCounters').hide();
}
//-------------------------------------------------------
function hideDiscontHours(){
				    $('#blockDiscountHour_0').hide();
			        $('#blockDiscountHour_1').hide();
			        $('#blockDiscountHour_2').hide();
			        $('#blockDiscountHour_3').hide();
			        $('#blockDiscountHour_4').hide();
			        $('#blockDiscountHour_5').hide();
			        $('#blockDiscountHour_6').hide();
			        $('#blockDiscountHour_7').hide();
			        $('#blockDiscountHour_8').hide();
			        $('#blockDiscountHour_9').hide();
			        $('#blockDiscountHour_10').hide();
			        $('#blockDiscountHour_11').hide();
			        $('#blockDiscountHour_12').hide();
			        $('#blockDiscountHour_13').hide();
			        $('#blockDiscountHour_14').hide();
			        $('#blockDiscountHour_15').hide();
			        $('#blockDiscountHour_16').hide();
			        $('#blockDiscountHour_17').hide();
			        $('#blockDiscountHour_18').hide();
			        $('#blockDiscountHour_19').hide();
			        $('#blockDiscountHour_20').hide();
			        $('#blockDiscountHour_21').hide();
			        $('#blockDiscountHour_22').hide();
			        $('#blockDiscountHour_23').hide();
				}
//-------------------------------------------------------
function hideStrategy(){
				    $('#blockMaxPrice').hide();
			        $('#lblockMaxPrice').hide();
					$('#blockAveragePrice').hide();
			        $('#lblockAveragePrice').hide();
					$('#blockWeeklySumLimit').hide();
			        $('#lblockWeeklySumLimit').hide();
					$('#blockClicksPerWeek').hide();
			        $('#lblockClicksPerWeek').hide();
				    $('#blockGoalID').hide();
			        $('#lblockGoalID').hide();
					$('#blockAdditionalMetrikaCounters').hide();
			        $('#lblockAdditionalMetrikaCounters').hide();
}	
//-------------------------------------------------------				
    