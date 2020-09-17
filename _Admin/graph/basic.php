<script src="../js/jquery-1.12.3.min.js"></script>
<script>
	function getGraphData_serverCnt(){
		var ajax_url = "serverCntByMonthly.php";
		var ajax_data = { idx : '' };

		$.ajax({
			type : "POST",
			url : ajax_url,
			data : ajax_data,
			dataType : "json",
			success : function(response){
				$.each(response, function (no, entry){
					draw_Graph("LineGraph", no, entry);
				});
				setTimeout(getGraphData_price(), 3000);
			}
		});
	}

	function getGraphData_price(){
		var ajax_url = "UsageAmountByMonthly.php";
		var ajax_data = { idx : '' };

		$.ajax({
			type : "POST",
			url : ajax_url,
			data : ajax_data,
			dataType : "json",
			success : function(response){
				$.each(response, function (no, entry){
					draw_Graph("BarGraph", no, entry); 
				});
			}
		});
	}

	function draw_Graph(url, key, value){
		var ajax_url = url + ".php";
		if(url == 'LineGraph'){
			var ajax_data = { 
				img_width : "600",
				img_height : "164",
				key : key,
				value : value
			};
		} else {
			var ajax_data = { 
				img_width : "600",
				img_height : "214",
				key : key,
				value : value
			};

		}

		$.ajax({
			type : "POST",
			url : ajax_url,
			data : ajax_data,
			dataType : "html",
			success : function(response){
				
			}
		});
	}

	getGraphData_serverCnt();
</script>