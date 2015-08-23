<?php
				add_action("wp_ajax_fn_oidfh_ajax", "fn_oidfh_ajax");
				add_action("wp_ajax_nopriv_fn_oidfh_ajax", "fn_oidfh_ajax");
				function fn_oidfh_ajax()
				{
					global $wpdb;
					
					$aColumns = array(
						'id','engine','browser','platform','version','grade'		
						);
					$sIndexColumn = "id";
					$sTable = "view_adore_demo";
					
					$sLimit = "";
					if ( isset( $_REQUEST["iDisplayStart"] ) && $_REQUEST["iDisplayLength"] != "-1" )
					{
						$sLimit = "LIMIT ".$wpdb->escape( $_REQUEST["iDisplayStart"] ).", ".
							$wpdb->escape( $_REQUEST["iDisplayLength"] );
					}
				
					/*
					 * Ordering
					 */
					$sOrder = "";
					if ( isset( $_REQUEST["iSortCol_0"] ) )
					{
						$sOrder = "ORDER BY  ";
						for ( $i=0 ; $i<intval( $_REQUEST["iSortingCols"] ) ; $i++ )
						{
							if ( $_REQUEST[ "bSortable_".intval($_REQUEST["iSortCol_".$i]) ] == "true" )
							{
								$sOrder .= "`".$aColumns[ intval( $_REQUEST["iSortCol_".$i] ) ]."` ".
								 	$wpdb->escape( $_REQUEST["sSortDir_".$i] ) .", ";
							}
						}
				
						$sOrder = substr_replace( $sOrder, "", -2 );
						if ( $sOrder == "ORDER BY" )
						{
							$sOrder = "";
						}
					}
				
					$sWhere = "";
					if ( isset($_REQUEST["sSearch"]) && $_REQUEST["sSearch"] != "" )
					{
						$sWhere = "WHERE (";
						for ( $i=0 ; $i<count($aColumns) ; $i++ )
						{
							$sWhere .= "`".$aColumns[$i]."` LIKE '%".$wpdb->escape( $_REQUEST["sSearch"] )."%' OR ";
						}
						$sWhere = substr_replace( $sWhere, "", -3 );
						$sWhere .= ")";
					}
				
					/*
					 * SQL queries
					 * Get data to display
					 */
					$sQuery = "
						SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
						FROM   $sTable
						$sWhere
						$sOrder
						$sLimit
						";	
					
					$rResult = $wpdb->get_results($sQuery,ARRAY_A);
					
					$sQuery = "
						SELECT FOUND_ROWS()
					";
					$aResultFilterTotal=$wpdb->get_results($sQuery,ARRAY_N);
					$iFilteredTotal = $aResultFilterTotal[0];
				
					$sQuery = "
						SELECT COUNT(`".$sIndexColumn."`)
						FROM   $sTable
					";
					$aResultTotal=$wpdb->get_results($sQuery,ARRAY_N);
					$iTotal = $aResultTotal[0];
				
					/*
					 * Output
					 */
					$output = array(
						"sEcho" => intval($_REQUEST["sEcho"]),
						"iTotalRecords" => $iTotal,
						"iTotalDisplayRecords" => $iFilteredTotal,
						"aaData" => array()
					);
					
					foreach($rResult as $aRow)
					{
						$row = array();
						for ( $i=0 ; $i<count($aColumns) ; $i++ )
						{
							if ( $aColumns[$i] == "version" )
							{
								/* Special output formatting for 'version' column */
								$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
							}
							else if ( $aColumns[$i] != "" )
							{
								/* General output */
								$row[] = $aRow[ $aColumns[$i] ];
							}
						}
						$output["aaData"][] = $row;
					}
				
					echo json_encode( $output );
					die();
				}