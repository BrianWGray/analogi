<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

$query="SELECT count(alert.id) as res_cnt, SUBSTRING_INDEX(SUBSTRING_INDEX(location.name, ' ', 1), '->', 1) as res_name, location.id as res_id
	FROM alert 
	LEFT JOIN location ON alert.location_id = location.id
	LEFT JOIN signature ON alert.rule_id = signature.rule_id
	WHERE signature.level>='".$inputlevel."'
	AND alert.timestamp>'".(time()-($inputhours*60*60))."'
	GROUP BY res_name
	ORDER BY res_cnt DESC
	LIMIT ".$glb_indexsubtablelimit;


echo "<div class='top10header' >
	<a href='#' class='tooltip'><img src='/images/help.png' /><span>Busiest locations in given time frame.</span></a>
	Top Loc, ".$inputhours." Hrs (Lvl ".$inputlevel."+)</div>";

if(!$result=mysql_query($query, $db_ossec)){
	echo "SQL Error:".$query;
}

$mainstring="";
$from=date("Hi dmy", (time()-($inputhours*3600)));
if(isset($_GET['level'])){
	$detailshours="&level=".$inputlevel;
}



while($row = @mysql_fetch_assoc($result)){
	$mainstring.="<div class='fleft top10data' style='width:60px'>".$row['res_cnt']."</div>
			<div class='fleft top10data'><a class='top10data' href='./detail.php?source=".$row['res_name']."&from=".$from.$detailshours."&breakdown=rule_id'>".htmlspecialchars(preg_replace($glb_hostnamereplace, "", $row['res_name']))."</a></div>
			<div class='clr'></div>";
}
$mainstring.="";

echo $mainstring;

?>
