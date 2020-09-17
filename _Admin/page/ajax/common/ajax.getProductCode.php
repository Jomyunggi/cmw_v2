<?php
@include_once 'Inc/inc.include.php';
@include_once COMMON_CLASS . '/class.service.php';
$M_SERVICE = new M_SERVICE;

$level = $M_FUNC->M_Filter(POST, 'level');
$m_id = $M_FUNC->M_Filter(POST, 'm_id');

$code = '';
$parent = 0;
$product_list = array();

if($level > 1 && $m_id > 0) {
	$productInfo = $M_SERVICE->getProductInfoByIdx($m_id);
	if($productInfo->size() > 0) {
		$code = $productInfo->get('pCode');
		$parent = intval($code);
	} 
}

$row = $M_SERVICE->getProductCode($level, $parent);

for($i=0;$i<$row->size();$i++) {
	$row->next();
	$product_list[$i]['idx'] = $row->get('idx');
	$product_list[$i]['code'] = $code . $row->get('pCode');
	$product_list[$i]['name'] = $pName = $row->get('pName');
}

echo json_encode($product_list);

exit();
?>