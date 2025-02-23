<?php

session_start();

if (! isset( $_SESSION['item']) ) {
	include('include/selector.php');  
}

// Get random word
function getRandomWord($len = 10) {
    $word = array_merge(range('a', 'z'), range('A', 'Z'));
    shuffle($word);
    // return substr(implode($word), 0, $len);
	return(session_id());
}

parse_str($_SERVER['QUERY_STRING']);

if ( isset($nextpage) ) { 
	$item = $nextpage;
} 
else { 
	// Grab a random page to start with
	$noitems = sizeof($_SESSION['item']);
	$itemno = rand(1, $noitems) - 1;
	$item = $_SESSION['item'][$itemno]['name'];
	// $item = $_SESSION['topitem'];
}
$_SESSION['name'] = $item;

// Get index of current item
$itemno = 0;
for ( $i=0; $i < sizeof($_SESSION['item']); $i++ ) {
	if ($item == $_SESSION['item'][$i]['name'] ) { 
		$itemno = $i; 
	}
}

$ratingsession = $_SESSION['item'][$itemno]['rating'];
if ( isset( $rating ) ) { 
	$_SESSION['item'][$itemno]['rating'] = $rating; 
}

// Write item ratings to php log whenever ratings are changed
if ( isset( $favorite ) ) {

    $clientip = $_SERVER['HTTP_CLIENT_IP']?$_SERVER['HTTP_CLIENT_IP']:($_SERVER['HTTP_X_FORWARDE‌​D_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']);
	$_SESSION['name'] = $_SESSION['item'][$itemno]['name'];
	$_SESSION['rating'] = $_SESSION['item'][$itemno]['rating'];
	include('include/insertratings.php');

	$logmsg = '<'. $_SESSION['app'].'> { ';
	$timezone = 'America/Havana';
	$date = new DateTime('now', new DateTimeZone($timezone));
	$localtime = $date->format('Y:m:d H:i:s');
 	$logmsg = $logmsg . '"user" : "' .getRandomWord(10). '", "items" : [ ';
	for ( $i=0; $i < sizeof($_SESSION['item']); $i++ ) {
    	if ( $_SESSION['item'][$i]['rating'] > 0 ) {
      		$logmsg = $logmsg.'{"' .$_SESSION['item'][$i]['name']. '" : "' .$_SESSION['item'][$i]['rating']. '"}, ';
    	}
  	}
	$logmsg = $logmsg.'] ,';
	$logmsg = $logmsg.' "client_ip" : "' .$clientip. '", ';
	$logmsg = $logmsg.' "sydney_time" : "' .$localtime. '", ';
	$logmsg = $logmsg.' } </'.$_SESSION['app'].'>';
	error_log($logmsg);
}

$help = "<p>You are looking at a list of ".$_SESSION['SELECTOR'];
if ( $_SESSION['SELECTOR'] == "cats" ) {
  $help = $help." from amongst my work colleagues.";
} else {
  $help = $help. ".";
}
$help = $help." To navigate, swipe left/right or click the arrow buttons.";
$help = $help." Click About to find out more. Like your favourites by clicking the hearts and pressing Save.";
if ( $_SESSION['DB'] == 'true' ) {
	$help = $help." Your ratings revise the rankings.";
}
$help = $help." Best viewed on a smart phone in portrait mode. </p>";
$help = $help."<p style='color:silver;font-size:80%;font-style:italic'> Enjoy, StefanoPicozzi@gmail.com</p>";

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>COTD</title>
	<!-- <link rel="stylesheet"  href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css"> -->
	<link rel="stylesheet"  href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<link rel="stylesheet" href="css/jqm-demos.css">
	<link rel="stylesheet" href="css/swipe-page.css">
	<link rel="shortcut icon" href="css/favicon.ico">
	<script src="js/jquery.js"></script>
	<script src="js/index.js"></script>
	<!-- <script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script> -->
	<script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<script src="js/swipe-page.js"></script>
	<script src="js/rateThis.js"></script>

<script>
$.ajaxSetup ({
// Disable caching of AJAX responses
cache: false
});
</script>

    <script type="text/javascript">

	var getQueryString = function ( field, url ) {
    	var href = url ? url : window.location.href;
    	var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
    	var string = reg.exec(href);
    	return string ? string[1] : null;
	};

	var ratingqp = getQueryString('rating');
	
	var rating = 0;
	if (ratingqp != null) { rating = ratingqp; }

    jQuery(document).ready(function(){ 
		$('#ratetest').rateThis({value:rating, disabledFullImg: "images/rating/full-disabled.png", disabledEmptyImg: "images/rating/empty-disabled.png"});		
	});
	</script>
	
</head>
<body>

<div data-role="page" id=<?php echo $item; ?> style="background-image:url( <?php echo $_SESSION['item'][$itemno]['filename']; ?> );" class="demo-page" data-dom-cache="true" data-theme="a" data-rating=<?php echo $_SESSION['item'][$itemno]['rating']; ?>  data-prev=<?php echo $_SESSION['item'][$itemno]['prev']; ?> data-next=<?php echo $_SESSION['item'][$itemno]['next']; ?> >

	<div id="help" class="trivia ui-content" data-role="popup" data-position-to="window" data-tolerance="50,30,30,30" data-theme="b">
				<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
				<?php echo $help; ?>
				</div>

	<div data-role="header" data-position="fixed" data-fullscreen="true" data-id="hdr" data-tap-toggle="false">
		<?php echo "<h1> No. ".$_SESSION['item'][$itemno]['rank']." ".$_SESSION['item'][$itemno]['caption']."</h1>"; ?>
		<a href="index.php" data-ajax="false" data-direction="reverse" data-icon="home" data-iconpos="notext" data-shadow="false" data-icon-shadow="false">Back</a>
	  <a href="#help" data-rel="popup"  data-role="button" data-iconpos="notext" data-icon="alert" data-iconpos="left" data-mini="true"></a>
    </div><!-- /header -->

	<div data-role="content">

		<div id="trivia" class="trivia ui-content" data-role="popup" data-position-to="window" data-tolerance="50,30,30,30" data-theme="b">
        	<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
				<?php 
					include('include/inserthits.php');
					echo $_SESSION['item'][$itemno]['trivia']; 
				?>
        </div>

	</div><!-- /content -->



    <div data-role="footer" data-position="fixed" data-fullscreen="true" data-id="ftr" data-tap-toggle="false">

		<div data-role="controlgroup" class="control ui-btn-left" data-type="horizontal" data-mini="true">
        	<a href="#" class="prev" data-role="button" data-icon="arrow-l" data-iconpos="notext" data-theme="d">Previous</a>
        	<a href="#" class="next" data-role="button" data-icon="arrow-r" data-iconpos="notext" data-theme="d">Next</a>
        </div>

		<div data-role="none" class="ui-content" style="text-align:center;" data-mini="true" data-inline="true" style="width: 100px;">
			
			<div class="rate-example" >
				<input data-role="none" name="ratetest" id="ratetest" type="text" data-mini="true" style="text-align:center; outline:none; border:0; broder-style:none; border-color: transparent !important; border:none!important; box-shadow:none!important;"/>
		    </div>

        </div>

		<div style="text-align:center;" class="control save-btn ui-btn-center" data-inline="true" >
		<a href="#" class="save" data-inline="true"  data-icon="check" data-iconpos="left" data-role="button" data-theme="d" data-mini="true">Save</a>
		<a href="#" class="cancel" data-inline="true"  data-icon="delete" data-iconpos="left" data-role="button" data-theme="d" data-mini="true">Cancel</a>
		</div>


		<a href="#trivia" data-rel="popup" class="trivia-btn ui-btn-right" data-role="button" data-icon="info" data-iconpos="left" data-theme="d" data-mini="true">About</a>
    </div><!-- /footer -->

</div><!-- /page -->

</body>
</html>
