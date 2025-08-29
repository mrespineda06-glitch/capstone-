<?php
file_put_contents("webhook_log.txt", date("Y-m-d H:i:s") . " - Webhook received!" . PHP_EOL, FILE_APPEND);
http_response_code(200);
echo "OK";
