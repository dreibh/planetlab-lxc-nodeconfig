<?php 

/*
    hotfixes.php -- 
        The purpose is to provide a mechanism for deploying source fixes 
        faster than, or instead of, waiting for rpm updates.

        The machanism is via tar files copied from PlanetLabConf/hotfixes/* to
        the root filesystem of machines in a given nodegroup.

    Notes:
        Nodes call this script via ConfFiles; the tar file for the first 
        matching nodegroup is returned to the node.
*/

require_once 'plc_api.php';
global $adm;
if ( isset($_REQUEST['debug']) ) {
    $debug = true;
}

function logit($str) 
{
    global $debug;
    if ( $debug == true ) 
    {
        print $str . "\n";
    }
}

logit("nodeid check");
$ng_names = array();

if ( isset($_REQUEST['node_id']) ) {
  logit("getnodes");
  $nodes = $adm->GetNodes(array(intval($_REQUEST['node_id'])));
  if (!empty($nodes)) {
    $node = $nodes[0];
  } else {
    exit(1);
  }
  if ( count($node['nodegroup_ids']) > 0 )
  {
      // collect a list of all nodegroup names for this node.
      foreach ( $node['nodegroup_ids'] as $ng_id ) {
        $ngs = $adm->GetNodeGroups(array('nodegroup_id' => $ng_id));
        if (!empty($ngs)) {
            logit($ngs[0]['groupname']);
            $ng_names[] = $ngs[0]['groupname'];
        }
      }
  } else {
    $ng_names[] = "default";
  }
}

foreach ( $ng_names as $name ) 
{
    logit("name: " . $name);
    // check that directory exists
    $file= "hotfixes/$name.tar";
    $stat = stat($file);
    if ( !empty ($stat) )
    {
        // send to client
        readfile($file);
        // stop after the first match.
        exit(1);
    } 
}

?>
