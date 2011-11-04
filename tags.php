<?php 
require("config.php");

session_name($tagSession);
session_start();

$memcache = new Memcache();
$memcache->connect("localhost") or die ("Could not connect to memcache");

$memKey = "";
if (isset($_REQUEST['i'])) {
	$memKey = sha1("campusmaps_tags" . $_REQUEST['i']);
	$results = $memcache->get($memKey);
	if (!empty($results) && !isset($_REQUEST['t']) && !isset($_REQUEST['d']) ) {
		echo json_encode($results);
		exit();
	}
}

if (!isset($_SESSION["droppedTags"])) {
	$_SESSION["droppedTags"] = 0;
}

$badwords = array( "sweatymen", "meathead", "guido", "steroid", "rape", "gloryhole" , "cnt",  "clusterfuck", "cuntnugget", "ahole", "anus", "ashle", "ashles", "asholes", "ass", "assmonkey", "assface", "asshle", "asshlez", "asshole", "assholes", "assholz", "asswipe", "azzhole", "bassterds", "bastard", "bastards", "bastardz", "basterds", "basterdz", "biatch", "bitch", "bitches", "blowjob", "boffing", "butthole", "buttwipe", "cck", "ccks", "carpetmuncher", "cawk", "cawks", "clit", "cnts", "cntz", "cock", "cockhead",  "cocks", "cocksucker", "crap", "cum", "cunt", "cunts", "cuntz", "dick", "dild", "dilds", "dildo", "dildos", "dilld", "dillds", "dominatricks", "dominatrics", "dominatrix", "dyke", "enema", "fuck", "fucker", "fag", "fagt", "faget", "faggt", "faggit", "faggot", "fagit", "fags", "fagz", "faig", "faigs", "fart", "fuck", "fucker", "fuckin", "fucking", "fucks", "fudgepacker", "fuk", "fukah", "fuken", "fuker", "fukin", "fukk", "fukkah", "fukken", "fukker", "fukkin", "gay", "gayboy", "gaygirl", "gays", "gayz", "goddamned",  "har", "hre", "hells", "hoar", "hoor", "hoore", "jackoff", "jap", "japs", "jerkoff", "jisim", "jiss", "jizm", "jizz", "knob", "knobs", "knobz", "kunt", "kunts", "kuntz", "lesbian", "lezzian", "lipshits", "lipshitz", "masochist", "masokist", "massterbait", "masstrbait", "masstrbate", "masterbaiter", "masterbate", "masterbates", "mothafucker", "mothafuker", "mothafukkah", "mothafukker", "motherfucker", "motherfukah", "motherfuker", "motherfukkah", "motherfukker", "motherfucker", "muthafucker", "muthafukah", "muthafuker", "muthafukkah", "muthafukker", "ngr", "nastt", "nigger", "nigur", "niiger", "niigr", "orafis", "orgasim", "orgasm", "orgasum", "oriface", "orifice", "orifiss", "packi", "packie", "packy", "paki", "pakie", "paky", "pecker", "peeenus", "peeenusss", "peenus", "peinus", "pens", "penas", "penis", "penisbreath", "penus", "penuus", "phuc", "phuck", "phuk", "phuker", "phukker", "polac", "polack", "polak", "poonani", "prc", "prck", "prk", "pusse", "pussee", "pussy", "puuke", "puuker", "queer", "queers", "queerz", "qweers", "qweerz", "qweir", "recktum", "rectum", "retard", "sadist", "scank", "schlong", "screwing", "semen", "sex", "sexy", "sht", "sht", "shter", "shts", "shtter", "shtz", "shit", "shits", "shitter", "shitty", "shity", "shitz", "shyt", "shyte", "shytty", "shyty", "skanck", "skank", "skankee", "skankey", "skanks", "skanky", "slut", "sluts", "slutty", "slutz", "sonofabitch", "tit", "turd", "vajina", "vagna", "vagiina", "vagina", "vajna", "vajina", "vullva", "vulva", "wp", "whr", "whre", "whore", "xrated", "xxx", "bch", "bitch", "blowjob", "clit", "arschloch", "fuck", "shit", "ass", "asshole", "btch", "bch", "btch", "bastard", "bich", "boiolas", "buceta", "cck", "cawk", "chink", "cipa", "clits", "cock", "cum", "cunt", "dildo", "dirsa", "ejakulate", "fatass", "fcuk", "fuk", "fuxr", "hoer", "hore", "jism", "kawk", "litch", "lich", "lesbian", "masturbate", "masterbat", "masterbat", "motherfucker", "sob", "mofo", "nazi", "nigga", "nigger", "nutsack", "phuck", "pimpis", "pusse", "pussy", "scrotum", "sht", "shemale", "shi", "slut", "smut", "teets", "tits", "boobs", "bbs", "teez", "testical", "testicle", "titt", "wse", "jackoff", "wank", "whoar", "whore", "damn", "dyke", "fuck", "shit", "amcik", "andskota", "arse", "assrammer", "ayir", "bich", "bitch", "bollock", "breasts", "buttpirate", "cabron", "cazzo", "chraa", "chuj", "cock", "cunt", "dmn", "daygo", "dego", "dick", "dike", "dupa", "dziwka", "ejackulate", "ekrem", "ekto", "enculer", "faen", "fag", "fanculo", "fanny", "feces", "feg", "felcher", "ficken", "fitt", "flikker", "foreskin", "fotze", "fu", "fuk", "futkretzn", "gay", "gook", "guiena", "hxr", "hell", "helvete", "hoer", "honkey", "huevon", "hui", "injun", "jizz", "kanker", "kike", "klootzak", "kraut", "knulle", "kuk", "kuksuger", "kurac", "kurwa", "kusi", "kyrpa", "lesbo", "mamhoon", "masturbat", "merd", "mibun", "monkleigh", "mouliewop", "muie", "mulkku", "muschi", "nazis", "nepesaurio", "nigger", "orospu", "paska", "perse", "picka", "pierdol", "pillu", "pimmel", "piss", "pizda", "poontsee", "poop", "porn", "prn", "preteen", "pula", "pule", "puta", "puto", "qahbeh", "queef", "rautenberg", "schaffer", "scheiss", "schlampe", "schmuck", "screw", "sht", "sharmuta", "sharmute", "shipal", "shiz", "skribz", "skurwysyn", "sphencter", "spic", "spierdalaj", "splooge", "suka", "testicle", "titt", "twat", "vittu", "wank", "wetback", "wichser", "wop", "yed", "zabourah");
$stopwords = array("pig", "buddha", "muhammad", "allah", "god", "jesus", "blah", "a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');

$dbname = 'map';
mysql_select_db($dbname);


// i = id
// t = new tag(s)
// d = drop tag
$sql = "";
if (isset($_REQUEST['i']) && isset($_REQUEST['d'])) {
	$output = array();
	if ($_SESSION["droppedTags"] >= 20) {
		$output["valid"] = 0;
		$output["message"] = "You've dropped 20 tags recently. Please wait a bit before doing more."; 
		echo json_encode($output);
		mysql_close($conn);
		exit();
	}
	$locationId = mysql_real_escape_string($_REQUEST['i']);
	$tagId = mysql_real_escape_string($_REQUEST['d']);
	$sql = "
	DELETE FROM locations_tags 
	WHERE location_id = '$locationId' 
	AND tag_id = '$tagId'
	AND permanency = 0";
	$result = mysql_query($sql) or die(mysql_error($result));
	
	$sql = "
	INSERT INTO changes (`date`, `change_id`, `tag_id`, `location_id`)
	VALUES (NOW(), '0', '$tagId', '$locationId')";	
	$result = mysql_query($sql) or die(mysql_error($result));
	
	++$_SESSION["droppedTags"];	
	$output["valid"] = 1; 
	$memcache->delete($memKey);
	echo json_encode($output);
	mysql_close($conn);
	exit();
	
} else if (isset($_REQUEST['i']) && isset($_REQUEST['t'])) {
	$output = array();
	
	$ip = $_SERVER["REMOTE_ADDR"];
	$sql = "SELECT id, strike, date FROM badips WHERE ip='$ip'";	
	$result = mysql_query($sql) or die(mysql_error($result));
	$badSeed = mysql_fetch_assoc($result);
	
	if (!empty($badSeed)) {
		
		$strike = $badSeed["strike"];
		$id = $badSeed["id"];
		$date = $badSeed["date"];
		
		switch ($strike) {
			case 1:
				if ( date("Y-m-d") < date("Y-m-d", strtotime("$date + 1 day")) ) {
					$output["valid"] = 0;
					$output["message"] = "This IP is currently banned from entering tags.";
					echo json_encode($output);
					mysql_close($conn);
					exit();
				}
				break;
			case 2:
				if ( date("Y-m-d") < date("Y-m-d", strtotime("$date + 3 day")) ) {
					$output["valid"] = 0;
					$output["message"] = "This IP is currently banned from entering tags.";
					echo json_encode($output);
					mysql_close($conn);
					exit();
				}
				break;
			case 3:
				if ( date("Y-m-d") < date("Y-m-d", strtotime("$date + 1 week")) ) {
					$output["valid"] = 0;
					$output["message"] = "This IP is currently banned from entering tags.";
					echo json_encode($output);
					mysql_close($conn);
					exit();
				}
				break;
			default:
				$output["valid"] = 0;
				$output["message"] = "This IP has been permabanned."; 
				echo json_encode($output);
				mysql_close($conn);
				exit();
				break;
		}
	}
	
	$tags = explode(" ", $_REQUEST['t']);
	$tagsResult = "";
	
	foreach ($tags as $value) {
		if ($value == " " || $value == "") {continue;}
		$insert = strtolower(mysql_real_escape_string($value));
		$checker = preg_replace("/[^a-zA-Zs]/", "", $insert);
		
		
		if (in_array($checker, $badwords)) {
			
			$output["valid"] = 0;
			$output["message"] = "You have tried to input a flagged word."; 
			
			if (empty($badSeed)) {
				$sql = "INSERT INTO badips (`ip`, `strike`, `date`) VALUES ('$ip', 1, NOW())";				
				$output["message"] .= "
This IP will be blocked from inputting any new tags for one day.";				
			} else {
				$strike++;
				$sql = "UPDATE badips SET strike='$strike', date=NOW() where id = '$id'";	
				switch ($strike) {
					case 2:
						$output["message"] .= "
This IP will be blocked from inputting any new tags for three days.";
						break;
					case 3:
						$output["message"] .= "
This IP will be blocked from inputting any new tags for one week.";
						break;
					default:
						$output["message"] .= "
This IP is now permanently blocked from inputting new tags.";
						break;
				}				
			}
			$result = mysql_query($sql) or die(mysql_error($result));
			echo json_encode($output);
			mysql_close($conn);
			exit();
		}
		
		if (!in_array($insert, $stopwords)) {		
			$sql = "SELECT id 
			FROM tags 
			WHERE title ='".mysql_real_escape_string($value)."'";
			$result = mysql_query($sql) or die(mysql_error($result));
			$row = mysql_fetch_assoc($result);
			$tagId = 0;
			if (empty($row)) {			
				$sql = "
				INSERT INTO tags
				(`title`) VALUES ('$insert')";
				$result = mysql_query($sql) or die(mysql_error($result));
				$tagId = mysql_insert_id();
			} else {
				$tagId = $row["id"];
			}
			$locationId = mysql_real_escape_string($_REQUEST['i']);
			
			$sql = "
			DELETE FROM locations_tags 
			WHERE location_id = '$locationId' 
			AND tag_id = '$tagId';";
			$result = mysql_query($sql) or die(mysql_error($result));
			
			$sql = "
			INSERT INTO locations_tags (`location_id`, `tag_id`)
			VALUES ('$locationId', '$tagId')";		
			$result = mysql_query($sql) or die(mysql_error($result));
			
			$sql = "
			INSERT INTO changes (`date`, `change_id`, `tag_id`, `location_id`)
			VALUES (NOW(), '1', '$tagId', '$locationId')";	
			//echo $sql;
			$result = mysql_query($sql) or die(mysql_error($result));
			
			$tagsResult .= "$insert ";
		}
	}
	$output["valid"] = 1;
	$tagsResult = trim($tagsResult);
	$output["message"] = "You have added the tag(s): \"$tagsResult\".";
	$memcache->delete($memKey);
	echo json_encode($output);
} else if(isset($_REQUEST['i'])) {
	$sql = "SELECT tags.id, tags.title, locations_tags.permanency
	FROM tags 
	LEFT JOIN locations_tags ON tags.id = locations_tags.tag_id 
	WHERE locations_tags.location_id = '";
	$sql .= mysql_real_escape_string($_REQUEST['i']);
	$sql .= "' ORDER BY tags.title";
	$result = mysql_query($sql) or die(mysql_error($result));
	$resultsArray = array();
	while ($row = mysql_fetch_assoc($result)) {
		$resultsArray[] = $row;
	}	
	$memcache->set($memKey, $resultsArray, 0, 86400);
	echo json_encode($resultsArray);
	mysql_close($conn);
}
?>