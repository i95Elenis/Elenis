<?php
    $xml = simplexml_load_file('./app/etc/local.xml', NULL, LIBXML_NOCDATA);

    if(is_object($xml)) {
        $db['host'] = $xml->global->resources->default_setup->connection->host;
        $db['name'] = $xml->global->resources->default_setup->connection->dbname;
        $db['user'] = $xml->global->resources->default_setup->connection->username;
        $db['pass'] = $xml->global->resources->default_setup->connection->password;
        $db['pref'] = $xml->global->resources->db->table_prefix;

        //echo "<pre>";print_r($db);exit;
        $tables = array(
            'dataflow_batch_export',
            'dataflow_batch_import',
            'log_customer',
            'log_quote',
            'log_summary',
            'log_summary_type',
            'log_url',
            'log_url_info',
            'log_visitor',
            'log_visitor_info',
            'log_visitor_online',
            'index_event',
            'report_event',
            
        );

        mysql_connect($db['host'], $db['user'], $db['pass']) or die(mysql_error());
        mysql_select_db($db['name']) or die(mysql_error());

        foreach($tables as $table) {
            //echo 'TRUNCATE table `'.$db['pref'].$table.'`'."<br/>";
            @mysql_query('TRUNCATE table `'.$db['pref'].$table.'`');
        }
    } else {
        exit('Unable to load local.xml file');
    }



    $dirs = array(
        'downloader/.cache/',
        'downloader/pearlib/cache/*',
        'downloader/pearlib/download/*',
        'var/cache/',
        'var/log/',
        'var/report/',
    );

    foreach($dirs as $dir) {
       // echo 'rm -rf '.$dir."<br/>";
        exec('rm -rf '.$dir);
    }





?>
