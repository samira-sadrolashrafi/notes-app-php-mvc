<?php

require_once '../bootstrap.php';

$auth = new AuthController();


$auth->login();


require_once APPROOT . '/views/layouts/header.php';


?>



<?php

require_once APPROOT . '/views/layouts/footer.php';

?>