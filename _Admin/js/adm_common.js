function nullMsg(obj, msg) {
	alert(msg);
	obj.focus();
}

function comm_getPaging(action, page, total_page, page_cnt) {
	var html = "";
	var ajax_data = {
		'page' : page,
		'total_page' : total_page,
		'action' : action,
		'page_cnt' : page_cnt,
	};
	$.ajax({
		url : '/page/common/paging.html',
		data : ajax_data,
		type : 'POST',
		dataType : 'html',
		contentType: 'application/x-www-form-urlencoded; charset=utf-8',
		success: function (response) {
			$("#pagination").html(response);
		},
		failure: function(msg) {
		}
	});
}

function valueCheck(key, msg) {
	if($("#"+key).val() == "") {
		alert(msg+" 입력해 주세요");
		$("#"+key).focus();
		return false;
	}
	return true;
}

var roundXL = function (num, digits) {
	digits = Math.pow(10, digits);
	return Math.round(num * digits) / digits;
};

var ceilXL = function (num, digits) {
	digits = Math.pow(10, digits);
	return Math.ceil(num * digits) / digits;
};

var floorXL = function (num, digits) {
	digits = Math.pow(10, digits);
	return Math.floor(num * digits) / digits;
};

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
