# RootResources/pl_netflow

try:
    pl_netflow = RootSlice(nm_vserver_flags = "syncstart",
                           nm_cpu_share = 32)
except:
    pass
