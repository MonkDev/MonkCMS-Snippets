<?php require($_SERVER["DOCUMENT_ROOT"]."/monkcms.php");

header("Content-Type: application/json");

$outarray;
$nodes;
$json;



// define filters

$category = "";
$group = "";
$howmany = "";
$order = "recent";
$hideseries="";
$hidecategory="";
$hidegroup="";
$passage="";
$tags="";
$enablepast="";

$filters = $_GET;
if($filters != null){
	foreach($filters as $key=>$value){
		switch ($key)
		{
		case 'series':
			$series = $value;
			break;
		case 'category':
			$category = $value;
			break;
		case 'group':
			$group = $value;
			break;
		case 'limit':
			$howmany = $value;
			break;
		case 'order':
			$order = $value;
			break;
		case 'hide_series':
			$hideseries = $value;
			break;
		case 'hide_category':
			$hidecategory = $value;
			break;
		case 'hide_group':
			$hidegroup = $value;
			break;
		case 'tags':
			$tags = $value;
			break;
		case 'passage':
			$passage = $value;
			break;
		case 'start':
			$offset = $value;
			break;
		case 'enablepast':
			$enablepast = $value;
			break;

		}
	}
}


// define tags to call

$tags = array(
  "id",
  "title",
  "slug",
  "eventstart format='r'",
  "eventstartTwo format='M j, Y'",
  "eventstartThree format='g:ia'",
  "eventend format='r'",
  "eventtimes",
  "summary",
  "description",
  "imageurl",
  "category",
  "group",
  "cost",
  "website",
  "coordname",
  "coordemail",
  "coordphone",
  "coordcellphone",
  "coordworkphone",
  "location",
  "city",
  "state",
  "fulladdress",
  "longitude",
  "latitude",
  "googlemap",
  "import",
  "locationwebsite",
  "locationemail",
  "locationphone",
  "locationdescription",
  "locationcategory",
  "locationimageurl",
  "locationpostal",
  "locationgroup",
);


// build getContent

$getContent = array(
  "event",
	"display:list",
	"order:".$order,
	"enablepast:".$enablepast,
	"find_category:".$category,
	"find_group:".$group,
	"hide_category:".$hidecategory,
	"hide_group:".$hideseries,
	"howmany:".$howmany
);
foreach($tags as $key => $tag){
  if($key!==0){
    $getContent[] = "show:||";
  }
  $getContent[] = "show:__" . $tag . "__";
}
$getContent[] = "show:~~~~";
$getContent[] = "noecho";
$getContent[] = "noedit";


// call getContent

$string = call_user_func_array('getContent', $getContent);


// arrange the data

$prearray = explode("~~~~",$string);

for ($i=0; $i <count($prearray)-1; $i++) {
	$outarray[$i] = explode("||",$prearray[$i]);
}

$i = 0;
foreach ($outarray as $key1 => $value) {

	$data_arr =  array();

	foreach($outarray[$i] as $key2 => $item){
    $tag_name = preg_replace('/^([^ ]*).*/', '$1', $tags[$key2]);
    $data_arr[$tag_name] = $item;
	}

	$nodes[$i] = $data_arr;

	$i++;
}

$totalpossible = getContent(
	"event",
	"display:list",
	"order:".$order,
	"enablepast:".$enablepast,
	"find_category:".$category,
	"find_group:".$group,
	"hide_category:".$hidecategory,
	"hide_group:".$hidegroup,
	"before_show:__totalpossible__",
	"noecho"
);


$output = array(
	items => $nodes,
	total => intval($totalpossible)
);


$json = json_encode($output);

$callback = $_REQUEST['callback'];

if($callback){

	header('Content-type: text/javascript');
	//echo("jsonp");
	echo $callback . '(' . $json . ');';
}else{
	header('Content-type: application/json');
	//echo "no-jibber";
	echo $json;
}

?>