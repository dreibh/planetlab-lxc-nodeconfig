<?php
//
// Exports the GPG public key for this PLC
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2006 The Trustees of Princeton University
//
// $Id: get_gpg_key.php,v 1.1 2006/05/08 18:53:30 mlhuang Exp $
//

include 'plc_config.php';

echo shell_exec("gpg --homedir=/tmp --export --armor" .
		" --no-default-keyring" .
		" --keyring " . PLC_ROOT_GPG_KEY_PUB);

?>
