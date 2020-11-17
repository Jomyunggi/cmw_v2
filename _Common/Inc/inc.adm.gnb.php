        <!-- Navigation -->
		<nav class="navbar navbar-default navbar-static-top" role="navigation" style="background-color:#2F4050;">
            <!-- 헤더 탑 -->
            <div class="navbar-header" style="margin-left:25px; margin-top:3px;">
				<a class="navbar-brand" href="/" style="padding:0px;"><img src="<?php echo IMG_DIR; ?>/dreamhouse2.jpg"></a>
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <p style="color:white;"><a href="?M0101V&m_id=<?php echo $_SESSION['U_idx'];	?>" style="color:white;"><?php echo $_SESSION['accountID']." (".$_SESSION['accountName'].") 님 환영합니다."; ?> </a></p>
                </li>
                <li>
                    <a href="/page/common/loginProc.php?action=logout"><span class="btn btn-danger btn-xs mtb-10">로그아웃</span></a>
                </li>
            </ul>
            <!--/* 헤더 탑 끝 -->

            <!-- 좌측 메뉴 -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
					<?php
						$className1 = "";
						$className2 = "";
						$className3 = "";
						$className4 = "";
						switch(substr($MENU_ID, 0, 3)){
							case "M01" : $className1 = "active"; break;
							case "M02" : $className2 = "active"; break;
							case "M03" : $className3 = "active"; break;
							case "M04" : $className4 = "active"; break;
							break;
						}

					?>
                        <li>
							<a href="?M0101" class="<?php echo $className1; ?>"><i class="fa fa-user fa-fw"></i> 
							관리자<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level" id="menu1" style="background-color:#FCFCFC;">
								<li><a href="?M0101"><i class="fa fa-caret-right" aria-hidden="true"></i> 게시판</a></li>
								<li><a href="?M0102"><i class="fa fa-caret-right" aria-hidden="true"></i> 상품</a></li>
								<li><a href="?M0103"><i class="fa fa-caret-right" aria-hidden="true"></i> 거래처</a></li>
								<!--li><a href="?M0104"><i class="fa fa-caret-right" aria-hidden="true"></i> 거래처단가</a></li-->
							</ul>
						</li>                      
						
						<li>
                            <a href="?M0201" class="<?php echo $className2; ?>"><i class="fa fa-film fa-fw"></i> 
							오프라인<span class="fa arrow"></span></a>

							<ul class="nav nav-second-level" id="menu2" style="background-color:#FCFCFC;">
                                <li><a href="?M0201"><i class="fa fa-caret-right" aria-hidden="true"></i> 매입</a></li>
								<li><a href="?M0202"><i class="fa fa-caret-right" aria-hidden="true"></i> 매출</a></li>
                            </ul>
                        </li>

						<li>
                            <a href="?M0301" class="<?php echo $className3; ?>"><i class="fa fa-check fa-fw"></i> 
							온라인<span class="fa arrow"></span></a>

							<ul class="nav nav-second-level" id="menu3" style="background-color:#FCFCFC;">
								<li><a href="?M0301"><i class="fa fa-caret-right" aria-hidden="true"></i> 매출</a></li>
								<li><a href="?M0302"><i class="fa fa-caret-right" aria-hidden="true"></i> 택배</a></li>
                            </ul>
                        </li>
						<li>
                            <a href="?M0401" class="<?php echo $className4; ?>"><i class="fa fa-check fa-fw"></i> 
							가격책정<span class="fa arrow"></span></a>

							<ul class="nav nav-second-level" id="menu4" style="background-color:#FCFCFC;">
								<li><a href="?M0401"><i class="fa fa-caret-right" aria-hidden="true"></i> 상품별 택배비</a></li>
								<li><a href="?M0402"><i class="fa fa-caret-right" aria-hidden="true"></i> 마진별 가격</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>