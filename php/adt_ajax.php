<?php
/**
 * Complete rewrite by lenasterg
 * @global type $wpdb
 */
function fn_adt_ajax() {
    global $wpdb;
    $request = $_REQUEST;

    $adt_datatable_dataset = fn_adore_fetch_adt(array('table' => $request['adt']));


    $adt_table_settings = json_decode($adt_datatable_dataset->adt_table_settings , TRUE);

    $sIndexColumn = $adt_table_settings["columns_array"][0]["column_name"];
    $sTable = $adt_table_settings['database_table_name'];

    $aColumns = array_column($adt_table_settings["columns_array"] , "column_name");

    $sLimit = adoreSSP::limit($request);
    $sOrder = adoreSSP::order($request , $aColumns);
    $sWhere = adoreSSP::filter($request , $aColumns);


    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT SQL_CALC_FOUND_ROWS `" . str_replace(" , " , " " , implode("`, `" , $aColumns)) . "`
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
		";

    $rResult = $wpdb->get_results($sQuery , ARRAY_A);

    $sQuery = " SELECT FOUND_ROWS() ";
    $aResultFilterTotal = $wpdb->get_results($sQuery , ARRAY_N);
    $iFilteredTotal = $aResultFilterTotal[0];

    $sQuery = "SELECT COUNT(`" . $sIndexColumn . "`)	FROM   $sTable	";
    $aResultTotal = $wpdb->get_results($sQuery , ARRAY_N);
    $iTotal = $aResultTotal[0];

    /*
     * Output
     */
    $output = array(
        "draw" =>  isset($request['draw']) ?
            intval($request['draw']) :
            0 ,
        "recordsTotal" => $iTotal ,
        "recordsFiltered" => $iFilteredTotal ,
        "data" => array()
    );

    foreach ( $rResult as $aRow ) {
        $row = array();
        for ( $i = 0; $i < count($aColumns); $i++ ) {
            if ( $aColumns[$i] == "version" ) {
                /* Special output formatting for 'version' column */
                $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
            } else if ( $aColumns[$i] != "" ) {
                /* General output */
                $row[] = $aRow[$aColumns[$i]];
            }
        }
        $output["data"][] = $row;
    }
    echo json_encode($output);
    die();
}
