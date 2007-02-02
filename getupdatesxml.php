<?php
//
// Deprecated. Generates an XML file that the old PlanetLabConf script understands.
//
// Aaron Klingaman <alk@absarokasoft.com>
// Mark Huang <mlhuang@cs.princeton.edu>
//
// Copyright (C) 2006 The Trustees of Princeton University
//
// $Id: getupdatesxml.php,v 1.2 2006/11/09 20:21:43 mlhuang Exp $
//

// Get admin API handle
require_once 'plc_api.php';
global $adm;

function writeXMLHeader()
{
  print( "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n\n" );
}

define("ENT_UNPARSED", 0); // i.e., CDATA
define("ENT_PARSED", 1); // i.e., PCDATA (attributes)

function xmlspecialchars_decode($string, $parsed = ENT_UNPARSED)
{
  if ($parsed == ENT_PARSED) {
    // &amp; to &
    // &apos; to '
    // &lt; to <
    // &gt; to >
    // &quot; to "
    return str_replace('&amp;', '&',
		       str_replace(array('&apos;', '&lt;', '&gt;', '&quot;'),
				   array("'", '<', '>', '"'),
				   $string));
  } else {
    // &amp; to &
    // &apos; to '
    // &lt; to <
    // &gt; to >
    // \" to "
    // \\ to \
    return str_replace('&amp;', '&',
		       str_replace(array('&apos;', '&lt;', '&gt;', '\"', '\\'),
				   array("'", '<', '>', '"', "\\"),
				   $string));
  }
}
  
function xmlspecialchars($string, $parsed = ENT_UNPARSED)
{
  if ($parsed == ENT_PARSED) {
    // & to &amp;
    // ' to &apos;
    // < to &lt;
    // > to &gt;
    // " to &quot;
    $string = str_replace(array("'", '<', '>', '"'),
			  array('&apos;', '&lt;', '&gt;', '&quot;'),
			  str_replace('&', '&amp;', $string));
  } else {
    // & to &amp;
    // ' to &apos;
    // < to &lt;
    // > to &gt;
    // " to \"
    // \ to \\
    $string = str_replace(array("'", '<', '>', '"'),
			  array('&apos;', '&lt;', '&gt;', '\"'),
			  str_replace(array('&', "\\"),
				      array('&amp;', '\\'),
				      $string));
  }

  return utf8_encode($string);
}
    
// Look up the node
if (!empty($_REQUEST['node_id'])) {
  $node = $adm->GetSlivers(intval($_REQUEST['node_id']));
} else {
  $nodenetworks = $adm->GetNodeNetworks(array('ip' => $_SERVER['REMOTE_ADDR']));
  if (!empty($nodenetworks)) {
    $node = $adm->GetSlivers($nodenetworks[0]['node_id']);
  }
}

if (empty($node)) {
  exit();
}
$node_id = $node['node_id'];

writeXMLHeader();
$curtime= time();
print( "<planetlab_conf version=\"0.1\" time=\"$curtime\">\n" );
print( "<node id=\"$node_id\">\n" );

foreach( $node['conf_files'] as $conf_file )
{
  $source            = xmlspecialchars($conf_file["source"]);
  $dest              = xmlspecialchars($conf_file["dest"]);
  $file_permissions  = xmlspecialchars($conf_file["file_permissions"]);
  $file_owner        = xmlspecialchars($conf_file["file_owner"]);
  $file_group        = xmlspecialchars($conf_file["file_group"]);
  $preinstall_cmd    = xmlspecialchars($conf_file["preinstall_cmd"]);
  $postinstall_cmd   = xmlspecialchars($conf_file["postinstall_cmd"]);
  $error_cmd         = xmlspecialchars($conf_file["error_cmd"]);
  $ignore_cmd_errors = $conf_file["ignore_cmd_errors"];
  $always_update     = $conf_file["always_update"];

  if( $ignore_cmd_errors == 1 )
    $ignore_cmd_errors= "y";
  else
    $ignore_cmd_errors= "n";

  if( $always_update == 1 )
    $always_update= "y";
  else
    $always_update= "n";

  print( "<file always_update=\"$always_update\" ignore_cmd_errors=\"$ignore_cmd_errors\">\n" );
  print( "<source>$source</source>\n" );
  print( "<destination>$dest</destination>\n" );
  print( "<permissions>$file_permissions</permissions>\n" );
  print( "<owner>$file_owner</owner>\n" );
  print( "<group>$file_group</group>\n" );
  print( "<preinstall_cmd>$preinstall_cmd</preinstall_cmd>\n" );
  print( "<postinstall_cmd>$postinstall_cmd</postinstall_cmd>\n" );
  print( "<error_cmd>$error_cmd</error_cmd>\n" );
  print( "</file>\n" );
}

print( "</node>\n" );
print( "</planetlab_conf>\n" );

?>
