<?php
/*
 * @Name:  Dbmovies
 * @Version: 3.0
 * @Author: Rifat Ahmed Sajeeb
 * @Date: 2021-07-01
*/

if(isset($_GET["imdb"])) {

	error_reporting(0);

	header('Content-Type: application/json');

	$IMDb = $_REQUEST['imdb'];

	$TMDbURL = 'http://api.themoviedb.org/3/movie/'.$IMDb.'?api_key=de4cdb32fd947a9d327b7fb5547350a9&append_to_response=trailers';

	$IMDbURL = 'https://www.imdb.com/title/'.$IMDb.'/reference';

	$JSON = array();

	$TMDbAPI = json_decode(GetURL($TMDbURL), TRUE);

	$IMDbAPI = file_get_contents($IMDbURL);

	$JSON['response'] = true;

	$JSON['adult'] = $TMDbAPI['adult'];

	$JSON['imdb_id'] = $TMDbAPI['imdb_id'];

	$JSON['tmdb_id'] = $TMDbAPI['id'];

	$JSON['title'] = $TMDbAPI['title'];


	preg_match('/\/country\/.*?">(?<content>.*?)<\/a>/ms', $IMDbAPI, $Country);

	$JSON['country'] = $Country['content'];


	preg_match("/".$Country['content'].":(?<content>.*?)<\/a>/ms", $IMDbAPI, $Rated);

	$JSON['rated'] = $Rated['content'];


	preg_match('/\/search\/title\?year=(?<id>\d{4})&/ms', $IMDbAPI, $Year);

	$JSON['year'] = (int) $Year['id'];


	preg_match('/ipl-rating-star__total-votes">\((?<content>.*?)\)<\/span>/ms', $IMDbAPI, $voteCount);

	preg_match('/<span class="ipl-rating-star__rating">(?<content>\d.\d)<\/span>/ms', $IMDbAPI, $Rating);

	$IMDbVotes = preg_replace('/,/ms', '', $voteCount['content']);

	$JSON['imdb_rating'] = array('count' => (int) $IMDbVotes, 'value' =>$Rating['content']);


	$JSON['tmdb_rating'] = array('count' => $TMDbAPI['vote_count'], 'value' =>$TMDbAPI['vote_average']);

	$JSON['money'] = array('budget' => $TMDbAPI['budget']);

	$JSON['duration'] = array('minutes' => $TMDbAPI['runtime'], 'hours' => date('H:i:s', mktime(0, $TMDbAPI['runtime'], 0 )));


	    if (preg_match('/image_src.*?href="(?<thumbnail>.*?)">/ms', $IMDbAPI)) 
	    {

	        preg_match('/image_src.*?href="(?<thumbnail>.*?)">/ms', $IMDbAPI, $ImgSrc);

	        preg_match('/M\/(?<id>.*?)\./ms', $ImgSrc['thumbnail'], $ImgID);

	        $thumbnail = 'https://m.media-amazon.com/images/M/'.$ImgID['id'].'._V1_SX100.jpg';
	        $normal = 'https://m.media-amazon.com/images/M/'.$ImgID['id'].'._V1_SX250.jpg';
	        $large = 'https://m.media-amazon.com/images/M/'.$ImgID['id'].'._V1_SX450.jpg';
	        $large = 'https://m.media-amazon.com/images/M/'.$ImgID['id'].'._V1_SX450.jpg';
	        $full = 'https://m.media-amazon.com/images/M/'.$ImgID['id'].'._V1_.jpg';

	        $JSON['posters'] = array('thumbnail' => $thumbnail, 'normal' => $normal, 'large' => $large, 'full' => $full);

	    }



	    $YouTubeID = $TMDbAPI['trailers']['youtube'][0]['source'];

	    if ($YouTubeID) 
	    {
	        $JSON['youtube'] = array('id' => $YouTubeID, 'url' => 'https://youtu.be/'.$YouTubeID, 'embed' => 'https://www.youtube.com/embed/'.$YouTubeID);
	    }


	function GetURL($url){
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	    $ip=rand(0,255).'.'.rand(0,255).'.'.rand(0,255).'.'.rand(0,255);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR: $ip"));
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/".rand(3,5).".".rand(0,3)." (Windows NT ".rand(3,5).".".rand(0,2)."; rv:2.0.1) Gecko/20100101 Firefox/".rand(3,5).".0.1");
	    $html = curl_exec($ch);
	    curl_close($ch);
	    return $html;
	}


	echo json_encode($JSON);

	//Output html
} else {
	?>
	<!DOCTYPE html>
	<html>
	<head>
	<title>DBMVS V3</title>
	<style>
		html, body {
			width:100%;
			height:100%;
			margin: 0;
			padding:0;
			font-family: Arial, serif;
			font-size:13px;
			background: -moz-linear-gradient(top,  rgba(188,188,188,1) 0%, rgba(255,255,255,1) 100%); /* FF3.6-15 */
			background: -webkit-linear-gradient(top,  rgba(188,188,188,1) 0%,rgba(255,255,255,1) 100%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom,  rgba(188,188,188,1) 0%,rgba(255,255,255,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#bcbcbc', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */
		}
		#imdbform {
			width:500px;
			margin:auto;
			display:flex;
			height:100%;
			align-items:center;
			justify-content:center;
		}
		.formline {
			padding:5px;
		}
		.formline>label {
			display:inline-block;
			width: 150px;
			color: #989898;
		}
		.formline>input {
			border: 1px solid #ccc;
			width:200px;
			font-size: inherit;
			padding: 5px;
		}
		
		.formline>select {
			width:210px;
			border: 1px solid #cccccc;
		    background-color: #fff;
		    height:30px;
		}
		.formline>button {
			border: 1px solid #cccccc;
		    width: 100%;
		    height: 30px;
		    background-color: #fff;
		    color: #000;
		}
		.formline>input:hover, .formline>button:hover,.formline>select:hover {
			border: 1px solid #aaa;
		}
	</style>
	</head>

	<body>
		<div id="imdbform">
			<form action="" method="get">
				<div class="formline">
					<label for="imdb">IMDB MOVIE ID:</label>
					<input type="text" placeholder="i.e. tt0848228" name="imdb" id="imdb"/>
				</div>
				<div class="formline">
					<button type="submit" id="submit">Scrape!</button>
				</div>
			</form>
		</div>
	</body>
	</html>
	<?php
}
?>