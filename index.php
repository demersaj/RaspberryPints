<?php
	require("includes/config.php");
	
	//This can be used to choose between CSV or MYSQL DB
	$db = true;
	
	// Setup array for all the beers that will be contained in the list
	$beers = array();
	
	if($db){
		// Connect to the database
		db();
		$sql = "SELECT * FROM beers WHERE active = true ORDER BY tapnumber";
		$qry = mysql_query($sql);
		while($b = mysql_fetch_array($qry))
		{
			$beeritem = array(
				"id" => $b['beerid'],
				"beername" => $b['name'],
				"style" => $b['style'],
				"notes" => $b['notes'],
				"og" => $b['og'],
				"fg" => $b['fg'],
				"srm" => $b['srm'],
				"ibu" => $b['ibu'],
				"kegstart" => $b['kegstart'],
				"kegremain" => $b['kegremain'],
				"tapnumber" => $b['tapnumber'],
			);
			array_push($beers, $beeritem);
		}
	} else {
		
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
		<title>RaspberryPints</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

		<!-- Set location of Cascading Style Sheet -->
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="shortcut icon" href="img/pint.ico">
	</head> 

	<body>
    	<div class="bodywrapper">
        	<!-- Header with Brewery Logo and Project Name -->
            <div class="header clearfix">
                <div class="HeaderLeft">
                   <a href="admin"><img src="img/brewery.png" height="100" alt=""></a>
                </div>
                <div class="HeaderCenter">
                    <h1 id="HeaderTitle">BEERS ON TAP</h1>
                </div>
                <div class="HeaderRight">
                   <a href="https://github.com/raspberrypints/raspberrypints"><img src="img/RaspberryPints.png" height="100" alt=""></a>
                </div>
            </div>
            <!-- End Header Bar -->
			
			<table>
				<thead>
					<tr>
						<th class="tap-num">
							TAP<br>#
						</th>
						<th class="srm">
							GRAVITY<hr>COLOR
						</th>
						<th class="ibu">
							BALANCE<hr>BITTERNESS
						</th>
						<th class="name">
							BEER NAME &nbsp; & &nbsp; STYLE<hr>TASTING NOTES
						</th>
						<th class="abv">				
							CALORIES<hr>ALCOHOL
						</th>
						<th class="keg">
							POURED<hr>REMAINING
						</th>
					</tr>
                </thead>
				<tbody>
					<?php for($i = 0; $i < count($beers); $i++) { ?>
						<tr class="<?php if($i%2 > 0){ echo 'altrow'; }?>" id="<?php echo $beers[$i]['id']; ?>">
							<td class="tap-num">
								<span class="tapcircle"><?php echo $beers[$i]['tapnumber']; ?></span>
				            </td>
							<td class="srm">
								<h3><?php echo $beers[$i]['og']; ?> OG</h3>
								<?php
								if ($beers[$i]['srm'] > 30)
									echo "<img src=\"img/srm/", "offthechart", ".png\" height=\"100\"  alt=\"\">";
								else
									echo "<img src=\"img/srm/", $beers[$i]['srm'], ".png\" height=\"100\"  alt=\"\">";
								?>
								<h2><?php echo $beers[$i]['srm']; ?> SRM</h2>
							</td>
							<td class="ibu">
								<h3><?php echo number_format((($beers[$i]['ibu'])/(($beers[$i]['og']-1)*1000)), 2, '.', ''); ?> BU:GU</h3>					
								<div class="ibu-container">
									<?php
										$numHops = 0;
										$remaining = $beers[$i]['ibu'];
										for( $h = 0; $h < 10; $h++){                                                
											if( $remaining < 10 ){
												$level = $remaining;
											}else{
												$level = 10;
											}
											?><div class="ibu-indicator ibu-element ibu-element-<?php echo $h ?>"><div class="ibu-full" style="height:<?php echo $level * 10; ?>%"></div></div><?php
											
											$remaining = $remaining - $level;
											$numHops++;
										}
										
										if( $remaining > 0 ){
											?><img class="ibu-max" src="img/ibu/offthechart.png" /><?php
										}
									?>
								</div>								
								<h2><?php echo $beers[$i]['ibu']; ?> IBU</h2>
							</td>
							<td class="name">
								<h1><?php echo $beers[$i]['beername']; ?></h1>
								<h2 class="subhead"><?php echo $beers[$i]['style']; ?></h2>
								<p><?php echo $beers[$i]['notes']; ?></p>
							</td>
							<td class="abv">
								<h3><?php
									$calfromalc = (1881.22 * ($beers[$i]['fg'] * ($beers[$i]['og'] - $beers[$i]['fg'])))/(1.775 - $beers[$i]['og']);									
									$calfromcarbs = 3550.0 * $beers[$i]['fg'] * ((0.1808 * $beers[$i]['og']) + (0.8192 * $beers[$i]['fg']) - 1.0004);
									echo number_format($calfromalc + $calfromcarbs); ?> kCal</h3>
								<div class="abv-container">
									<?php
										$abv = ($beers[$i]['og'] - $beers[$i]['fg']) * 131;
										$numCups = 0;
										$remaining = $abv * 20;
										do{                                                                
												if( $remaining < 100 ){
														$level = $remaining;
												}else{
														$level = 100;
												}
												?><div class="abv-indicator"><div class="abv-full" style="height:<?php echo $level; ?>%"></div></div><?php                                                                
												
												$remaining = $remaining - $level;
												$numCups++;
										}while($remaining > 0 && $numCups < 2);
										
										if( $remaining > 0 ){
												?><img class="abv-max" src="img/abv/offthechart.png" />
											<?php
										}
									?>
								</div>
								<h2><?php echo number_format($abv, 1, '.', ',')."%"; ?> ABV</h2>
							</td>
							<td class="keg">
								<h3><?php echo number_format((($beers[$i]['kegstart'] - $beers[$i]['kegremain']) * 128)); ?> fl oz poured</h3>
								<?php 
									$keglvl = $beers[$i]['kegstart'] + $beers[$i]['kegremain'];
									if( $keglvl > 0 ){
										$percentRemaining = $beers[$i]['kegremain'] / $beers[$i]['kegstart'] * 100;
									}else{
										$percentRemaining = 0;
									}
									$kegImgClass = "";
									if( $percentRemaining < 15 )
										$kegImgClass = "keg-red";
									else if( $percentRemaining < 25 )
										$kegImgClass = "keg-orange";
									else if( $percentRemaining < 45 )
										$kegImgClass = "keg-yellow";
									else
										$kegImgClass = "keg-green"
								?>
								<div class="keg-indicator"><div class="keg-full <?php echo $kegImgClass ?>" style="height:<?php echo $percentRemaining; ?>%"></div></div>
								
								<h2><?php echo number_format(($beers[$i]['kegremain'] * 128)); ?> fl oz left</h2>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</body>
</html>