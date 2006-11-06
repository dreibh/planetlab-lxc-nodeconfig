<?php
include('plc_config.php');
?>
# RootResources/plc_slice_pool

SA_HOST = "<?php print(PLC_BOOT_HOST); ?>"
SA_URL = SA_HOST + "/xml/"
CACERT_PATH = "/mnt/cdrom/bootme/cacert/%s/cacert.pem" % SA_HOST

plc_slice_pool = RootPool(slice = "pl_conf",
			  plc_sa_prefix = "pl",
			  plc_sa_server = SA_URL,
			  plc_sa_cacert = CACERT_PATH,
			  nm_pool_child_type = "VServerSlice",
			  nm_cpu_share = RES_INF,
			  nm_net_avg_rate = RES_INF,
			  nm_net_min_rate = RES_INF,
			  nm_net_max_rate = RES_INF,
			  nm_net_exempt_avg_rate = RES_INF,
			  nm_net_exempt_min_rate = RES_INF,
			  nm_net_exempt_max_rate = RES_INF,
			  nm_disk_quota = RES_INF)
