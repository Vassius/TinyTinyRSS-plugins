<?php

require_once "functions.php";

class Af_AmazingSuperPowers extends Plugin {

    function about() {
        return array(0.1, "Fetch larger image from Amazing SuperPowers webcomic", "Markus Wiik");

    }
    function init($host) {
        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
    }

    function hook_article_filter($article) {
        $owner_uid = $article["owner_uid"];
        if (strpos($article["link"], "amazingsuperpowers.com") !== FALSE) {
            $article["content"] = preg_replace("/comics-rss/", "comics", $article['content']);
            $doc = new DOMDocument();
            @$doc->loadHTML($article['content']);
            $xpath = new DOMXPath($doc);
            $img = $xpath->query('(//img)')->item(0);
            $article['content'] = $doc->saveHTML($img);
        }
        return $article;
    }
    function api_version() {
        return 2;
    }
}
?>
