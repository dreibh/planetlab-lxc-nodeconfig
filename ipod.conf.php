<?php
//
// /etc/ipod.conf for production nodes
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2004-2006 The Trustees of Princeton University
//
// $Id$
//

include('plc_config.php');

// Allow only the API server to send a reboot packet
$IP_SUBNET = PLC_API_IPOD_SUBNET;
$IP_MASK   = PLC_API_IPOD_MASK;

echo <<<EOF
# IP range that we respond to reboot requests from
IP_SUBNET=$IP_SUBNET
IP_MASK=$IP_MASK

EOF;

?>
