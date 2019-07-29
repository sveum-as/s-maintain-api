<?php
function route_backup_get_tables() {
    require_once( ABSPATH . 'wp-config.php' );
    
    $conn = mysqli_connect( DB_HOST, DB_USER, DB_PASSWORD );
    mysqli_select_db( $conn, DB_NAME );
    
    $tables = array();
    $result = mysqli_query( $conn, "SHOW TABLES" );
    
    while ( $row = mysqli_fetch_row( $result ) ) {
        $tables[] = $row[0];
    }
    
    return rest_ensure_response( array( 
        'tables' => $tables
    ) );
}

function route_backup_backup_tables( $data ) {
    require_once( ABSPATH . 'wp-config.php' );
    
    $name = DB_NAME;
    $host = DB_HOST;
    
    $conn = mysqli_connect( DB_HOST, DB_USER, DB_PASSWORD );
    mysqli_select_db( $conn, DB_NAME );
    
    mysqli_query( $conn, "$host `utf8` COLLATE `utf8_general_ci`" );
    
    $tables = $data -> get_body();
    
    if ( $tables == '*' ) {
        $tables = array();
        $res = mysqli_query( $conn, 'SHOW TABLES' );

        while( $row = mysqli_fetch_row( $res ) ) {
            $tables[] = $row[0];
        }
    }
    else {
        $tables = json_decode( $data -> get_body() )->tables;
    }
    
    $sql = "/**\n * DB {$name} @ " . date( "d.m.Y H:i:s" ) . "\n * ";
    $sql .= "TABLES " . implode( ', ', $tables );
    $sql .= "\n */\n";
    
    foreach ( $tables as $table ) {
        $sql .= "\n/* `{$table}` */\n";
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
        
        $res = mysqli_query( $conn, "SHOW CREATE TABLE `{$table}`" );
        $row = mysqli_fetch_row( $res );
        $sql .=  $row[1] . ";\n";
        
        $result = mysqli_query( $conn, "SELECT * FROM `{$table}`" );
        $num_rows = mysqli_num_rows( $result );    
        
        if ( $num_rows > 0 ) {
            $values = build_rows( $conn, $result, $num_rows );
            $sql .= "INSERT INTO `{$table}` VALUES ";      
            $sql .= "  " . implode( ";\nINSERT INTO `{$table}` VALUES ", $values ) . ";\n";
        }
    }
    
    mysqli_close( $conn );
    
    $backup_dir = plugin_dir_path( __DIR__ ) . 'temp/backups/';
    $backup_file = date('Ymd_His') . '.sql';
    
    $handle = fopen( $backup_dir . $backup_file, 'w+' );
    fwrite( $handle, $sql );
    fclose( $handle );
    
    header("Content-Type: application/sql");
    header("Content-Length:" . filesize( $backup_dir . $backup_file ) );
    header("Content-Disposition: attachment; filename=" . $backup_file);
    readfile( $backup_dir . $backup_file );
    
    unlink( $backup_dir . $backup_file );
}

function build_rows( $conn, $result, $num_rows ) {
    $values = array();
    $z = 0;
    
    for ( $i = 0; $i < $num_rows; $i++ ) {
        $items = mysqli_fetch_row( $result );
        $values[$z] = "( ";
        
        for( $j = 0; $j < count( $items ); $j++ ) {
            $values = build_column( $conn, $values, $items, $j, $z);
        }
        
        $values[$z++] .= " )";
    }
    
    return $values;
}

function build_column( $conn, $values, $items, $j, $z) {
    if ( isset( $items[$j] ) )
        $values[$z] .= "'" . mysqli_real_escape_string( $conn, $items[$j] ) . "'";
    else
        $values[$z] .= "NULL";
    
    if ( $j < ( count( $items ) - 1 ) )
        $values[$z] .= ",";
        
    return $values;
}

function route_backup_backup_files() {
    $dir_path = ABSPATH;
    
    $backup_dir = plugin_dir_path( __DIR__ ) . 'temp/backups/';
    $backup_file = date('Ymd_His') . '.tar';
    
    $archive = new PharData( $backup_dir . $backup_file );
    $archive -> buildFromDirectory( $dir_path );
    
    header("Content-Type: application/tar");
    header("Content-Length:" . filesize( $backup_dir . $backup_file ) );
    header("Content-Disposition: attachment; filename=" . $backup_file);
    readfile( $backup_dir . $backup_file );
    
    unlink( $backup_dir . $backup_file );
}
?>