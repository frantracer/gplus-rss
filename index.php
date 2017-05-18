<?php
header("Content-type: text/xml;charset=utf-8");  
?>

<?php

class RssGplusItem {

    public $title;
    public $link;
    public $description;
    public $id;
    public $publish_date;
    public $source;
    public $collecion;
    public $content;

    private function trim_string($str, $max) {
        $tag = "...";
        $max = $max ?: -1;
        $formatted_str = $str;
        if($max != -1 && strlen($str) > $max) {
            if($max > strlen($tag)) {
                $formatted_str = substr($str, 0, $max - strlen($tag)) . $tag;
            }else{
                $formatted_str = substr($str, 0, $max);
            }
        } 
        return $formatted_str;
    }
    
    private function generate_content() {
        return "<![CDATA[ <p><a href=\"".$this->source."\">Source</a></p> ]]>";
    }

    private function get_data($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    
    private function get_collection($url) {
        $data = $this->get_data($url);
        $re = '/<a href="\.(.+)" class="UTObDb" aria-label="(.+?)">(.+?)<\/a>/';
        preg_match($re, $data, $matches);
        $collection_name = $matches[3];
        return $collection_name;
    }

    public function __construct($entry, $settings)
    {
        $title = $entry['title'];
        $source = "";
        if ($title == "") {
            $attachments = $entry['object']['attachments'];
            if(count($attachments) > 0) {
                $title = $attachments[0]['displayName'];
                $source = $attachments[0]['url'];
            } else {
                $title = 'No title';
            }
        }
        $this->title = $this->trim_string($title, $settings['title_max']);
        $this->source = $source;
        $this->content = $this->generate_content();
        $this->link = $entry['url'];
        $this->description = $title;
        $this->id = $entry['id'];
        $this->publish_date = $entry['published'];
        if ($settings['skip_collection']) {
            $this->collection = "";
        } else {
            $this->collection = $this->get_collection($entry["url"]);
        }
    }

}


?>

<?php

/* API key to connect to Google API */
$apiKey = '';

/* Get url parameters */
$profile_id = $_GET['profile'];
$collection = $_GET['collection'];
$title_max = $_GET['title_max'];

/* Create Google Client */
include_once __DIR__ . '/google-api-php-client-2.1.3/vendor/autoload.php';
$client = new Google_Client();
$client->setApplicationName("gplus collection rss");
$client->setDeveloperKey($apiKey);

/* Get the list of entries for a user */
$service = new Google_Service_Plus($client);
$activities = $service->activities->listActivities($profile_id, 'public', [ 'maxResults' => 5 ] );
$profile = $service->people->get($profile_id);

/* Create list of entries */
$activity_list = array();
foreach ($activities as $activity) {
    $gplus_item = new RssGplusItem($activity, [ 'title_max' => $title_max, 'skip_collection' => $collection == "" ]);
    if ($gplus_item->collection == $collection) {
        array_push($activity_list, $gplus_item);
    }
}

?>

<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:content="http://purl.org/rss/1.0/modules/content/" version="2.0">
    <channel>
        <title><?= $profile->displayName ?> gplus profile</title>
        <link><?= $profile->url ?></link>
        <description>Rss public gplus profile of <?= $profile->displayName ?></description>
        <dc:language>es</dc:language>
        <dc:creator><?= $profile->displayName ?></dc:creator>
        <dc:rights>Copyright <?= gmdate('Y', time()) ?></dc:rights>
        <dc:date><?= date("c", time()) ?></dc:date>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>12</sy:updateFrequency>
        <sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>

<?php foreach ($activity_list as $gplus_item): ?>    
        <item>
            <title><?= $gplus_item->title ?></title>
            <link><?= $gplus_item->link ?></link>
            <description><?= $gplus_item->description ?></description>
            <guid isPermaLink="false"><?= $gplus_item->id ?></guid>
            <content:encoded><?= $gplus_item->content ?></content:encoded>
            <dc:subject><?= $gplus_item->collection ?></dc:subject>
            <dc:date><?= $gplus_item->publish_date ?></dc:date>
        </item>
<?php endforeach ?>

    </channel>
</rss>
