$(function() {

    $('#side-menu').metisMenu();

});

$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }

	$('#menu_2 depth_3').click(function(){
		var num = $('#menu_2').addClass('in');
	});

	$('#menu_1 li').click(function(){
		var num2 = $("#menu").removeClass('active');
		var num = $('#menu_2').removeClass('in');
	});

	//salesTeam 페이지 tr로 href 걸기
	$('.table').delegate('tr', 'click', function(){
		 if( typeof ($(this).data('href')) != 'undefined' ) {
			window.location = $(this).data('href');
			return false;
		} 
	});

	//salesTeam 페이지 tr로 href 걸기
	$('.table').delegate('td', 'click', function(){
		 if( typeof ($(this).data('href')) != 'undefined' ) {
			window.location = $(this).data('href');
			return false;
		} 
	});
});

 /* popup */
function openPopup(sUrl, nWidth, nHeight, sOption){ 
    var tmp = '';
    tmp += 'width=' + nWidth + ',height=' + nHeight + ',top=' + (screen.availHeight - nHeight-50)/2 + ', left=' + (screen.availWidth - nWidth-10)/2 + ' ,';
    tmp += 'status=no, menubar=no, scrollbars=no, resizable=no, toolbar=no';
    window.open(sUrl, '', tmp);
}

function onlyNumber(obj) {
	var val = obj.value.replace(/,/g, '');

	for(i=0; i<val.length; i++){
		var ch = val.charAt(i);
		if(!(ch >='0' && ch <='9')){
			alert("숫자만 입력 가능합니다");
			// obj.value = obj.value.slice(0,-1);
			obj.value = '';
			obj.focus();
			return;
		}
	}
}

function IDCheck(){
	var frm = document.frm_join;
	var accountID = frm.userID.value;
	
	var num	 = accountID.search(/[0-9]/g);
	var eng	 = accountID.search(/[a-z]/g);
	var eng2 = accountID.search(/[A-Z]/g);
	var spe  = accountID.search(/\W|\s/g);

	if (accountID.length < 6 || accountID.length > 20) {
		html = "아이디는 6~20자의 영문 소문자, 숫자만 사용 가능합니다.";
		alert(html);
		$("#idCheck").val(0);
		return false;
	}

	if(accountID.search(/\s/) != -1 ){
		html = "아이디는 6~20자의 영문 소문자, 숫자만 사용 가능합니다.";
		alert(html);
		$("#idCheck").val(0);
		return false;
	} else if(eng2 >= 0 || spe > 0 ){
		html = "아이디는 6~20자의 영문 소문자, 숫자만 사용 가능합니다.";
		alert(html);
		$("#idCheck").val(0);
		return false;
	} else if(accountID.search(/_/) != -1){
		html = "아이디는 6~20자의 영문 소문자, 숫자만 사용 가능합니다.";
		alert(html);
		$("#idCheck").val(0);
		return false;
	}

	return true;
}