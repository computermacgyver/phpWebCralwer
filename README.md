phpWebCralwer
=============

A web crawler in PHP.

Note that an additional file, CONFIG_db.php, is required. This sets the 
database server, name and password. Full details are commented in
LIB_db_functions.php.

TODO:
* Separate domain into its own db table.
* Set up per-domain timers, to allow local, not global, rate limit.
* Move to libcurl for fetching -- restrict size/wrong MIME fetches.

