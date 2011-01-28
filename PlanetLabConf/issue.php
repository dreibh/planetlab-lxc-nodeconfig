<?php
//
// /etc/issue
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2004-2006 The Trustees of Princeton University
//

// For PLC_NAME, etc.
include('plc_config.php');

$PLC_NAME = PLC_NAME;
$PLC_WWW_HOST = PLC_WWW_HOST;
$PLC_WWW_PORT = PLC_WWW_PORT;
$PLC_MAIL_SUPPORT_ADDRESS = PLC_MAIL_SUPPORT_ADDRESS;

if ($PLC_WWW_PORT == 443) {
  $PLC_WWW_URL = "https://$PLC_WWW_HOST/";
} elseif ($PLC_WWW_PORT != 80) {
  $PLC_WWW_URL = "http://$PLC_WWW_HOST:$PLC_WWW_PORT/";
} else {
  $PLC_WWW_URL="http://$PLC_WWW_HOST/";
}

echo <<<EOF
$PLC_NAME Node: \\n
Kernel \\r on an \\m
$PLC_WWW_URL

This machine is a node in the $PLC_NAME distributed network. If you
require assistance in administering this node, please contact
${PLC_MAIL_SUPPORT_ADDRESS}.

Console login to this node is restricted to site_admin.


EOF;

?>
