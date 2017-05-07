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
    public $content;

    private function entry_title($entry)
    {
        $title = $entry['title'];
        if ($title == "") {
            $title = "No title";
        }
        return $title;
    }

    public function __construct($entry)
    {
        $this->title = $this->entry_title($entry);
        $this->link = $entry['url'];
        $this->description = "Description";
        $this->id = $entry['id'];
        $this->publish_date = $entry['published'];
    }

}


?>

<?php

/* API key to connect to Google API */
$apiKey = '';

/* Get id of Google+ profile */
$profile_id = $_GET['profile'];

/* Create Google Client */
include_once __DIR__ . '/google-api-php-client-2.1.3/vendor/autoload.php';
$client = new Google_Client();
$client->setApplicationName("gplus collection rss");
$client->setDeveloperKey($apiKey);

/* Get the list of entries for a user */
$service = new Google_Service_Plus($client);
$activities = $service->activities->listActivities($profile_id, 'public');
?>

<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:content="http://purl.org/rss/1.0/modules/content/" version="2.0">
    <channel>
        <title>dive into mark</title>
        <link>http://diveintomark.org/</link>
        <description>A lot of effort went into making this effortless.</description>
        <dc:language>en-us</dc:language>
        <dc:creator>f8dy@diveintomark.org</dc:creator>
        <dc:rights>Copyright 2002</dc:rights>
        <dc:date><?= gmdate('D, d M Y H:i:s T', time()) ?></dc:date>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>

<?php foreach ($activities as $activity): ?>
    
    <?php $gplus_item = new RssGplusItem($activity) ?>
        <item>
            <title><?= $gplus_item->title ?></title>
            <link><?= $gplus_item->link ?></link>
            <description><?= $gplus_item->description ?></description>
            <guid isPermaLink="false"><?= $gplus_item->id ?></guid>
            <content:encoded> <![CDATA[ <p><a href="http://www.dooce.com/">Reborn</a>.</p> ]]> </content:encoded>
            <dc:subject/>
            <dc:date><?= $gplus_item->publish_date ?></dc:date>
        </item>
  
<?php endforeach ?>

    </channel>
</rss>
