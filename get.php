<?php require('library/inc.php');
if (isset($_GET['url'])) {
	echo json_encode(getLinkPublicVideo($_GET['url']));
}