<?php
//
// Print a subset of the variables from the PLC configuration store in
// various formats (Perl, Python, PHP, sh)
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2006 The Trustees of Princeton University
//
// $Id: get_plc_config.php,v 1.7 2006/05/08 21:45:29 mlhuang Exp $
//

// Try the new plc_config.php file first
include 'plc_config.php';

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

foreach (array('PLC_API_HOST', 'PLC_API_PATH', 'PLC_API_PORT',
	       'PLC_WWW_HOST', 'PLC_BOOT_HOST',
	       'PLC_NAME', 'PLC_SLICE_PREFIX',
	       'PLC_MAIL_SUPPORT_ADDRESS',
	       'PLC_MAIL_SLICE_ADDRESS')
	 as $name) {
  if (defined($name)) {
    // Perl, PHP, Python, and sh all support strong single quoting
    $value = "'" . str_replace("'", "\\'", constant($name)) . "'";
    printf($format, $name, $value);
  }
}

echo $end . "\n";

?>
