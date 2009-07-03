<?php
//
// Print a subset of the variables from the PLC configuration store in
// various formats (Perl, Python, PHP, sh)
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2006 The Trustees of Princeton University
//
// $Id$
//

// Try the new plc_config.php file first
include 'plc_config.php';
require_once 'plc_api.php';
global $adm;

if (isset($_REQUEST['perl'])) {
  $shebang = '#!/usr/bin/perl';
  $format = "our $%s=%s;\n";
  $end = '';
} elseif (isset($_REQUEST['python'])) {
  $shebang = '#!/usr/bin/python';
  $format = "%s=%s\n";
  $end = '';
} elseif (isset($_REQUEST['php'])) {
  $shebang = '<?php';
  $format = "define('%s', %s);\n";
  $end = '?>';
} else {
  $shebang = '#!/bin/sh';
  $format = "%s=%s\n";
  $end = '';
}

echo $shebang . "\n";
$limit_view = True;

if (isset($_REQUEST['node_id']) ) {
	$node_id = intval($_REQUEST['node_id']);
} else {
	$node_id = 0;
}


if ($node_id) {
	$nodes = $adm->GetNodes($node_id);
    if (!empty($nodes)) {
        $node = $nodes[0];
        $tags = $adm->GetNodeTags(array('node_id' => $node_id,
										'tagname' => 'infrastructure'));
        if (!empty($tags)) {
            $tag = $tags[0];
            if ( intval($tag['value']) == 1 ) {
				$interfaces = $adm->GetInterfaces(array('ip' => $_SERVER['REMOTE_ADDR']));
				if (!empty($interfaces)) {
					$nodes = $adm->GetNodes(array($interfaces[0]['node_id']));
					if (!empty($nodes)) {
						$node = $nodes[0];
						if ( $node['node_id'] == $node_id )
						{
							# NOTE: only provide complete view if 
							#     node exists
							#   node has infrastrucure tag
							#   infrastructure tag value == 1
							# Check that the requestor is the node.
                			$limit_view = False;
						}
				  	}
				}
            }
        }
    }
}

if ( $limit_view ) {
    $plc_constants = array('PLC_API_HOST', 'PLC_API_PATH', 'PLC_API_PORT',
               'PLC_WWW_HOST', 'PLC_BOOT_HOST', 'PLC_PLANETFLOW_HOST',
               'PLC_NAME', 'PLC_SLICE_PREFIX', 'PLC_MONITOR_HOST',
               'PLC_MAIL_SUPPORT_ADDRESS',
               'PLC_MAIL_MOM_LIST_ADDRESS',
               'PLC_MAIL_SLICE_ADDRESS');
} else {
	$plc_constants = array();
	$const = get_defined_constants(true);
	foreach ( $const['user'] as $name => $v ){
	    if ( preg_match('/^PLC_/', $name) == 1 ){
			$plc_constants[] = $name;
		}		
	}
}

foreach ($plc_constants as $name) {
	if (defined($name)) {
		// Perl, PHP, Python, and sh all support strong single quoting
		$value = "'" . str_replace("'", "\\'", constant($name)) . "'";
		printf($format, $name, $value);
	}
}
printf($format, 'PLC_API_CA_SSL_CRT', "'/usr/boot/cacert.pem'");
printf($format, 'PLC_ROOT_GPG_KEY_PUB', "'/usr/boot/pubring.gpg'");

echo $end . "\n";

?>
