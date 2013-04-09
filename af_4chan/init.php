<?php
class Af_4chan extends Plugin {

    function about() {
        return array(0.1, "Fetch full size image from 4chan /b/ feed", "Markus Wiik");
    }

    function init($host) {
        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
    }

    function hook_article_filter($article) {
        $owner_uid = $article["owner_uid"];

        if (preg_match('/4chan.org\/b\//', $article['link'])) {
            if (strpos($article["plugin_data"], "4chan,$owner_uid:") === FALSE) {

                $doc = new DOMDocument();
                $doc->loadHTML($article['content']);

                if ($doc) {
                    $xpath = new DOMXPath($doc);
                    $imgurl = $xpath->query('//a')->item(0)->getAttribute('href');
                    if (is_null($imgurl)) {
                        return $article;
                    }
                    $xpath->query('//img')->item(0)->setAttribute('src', $imgurl);
                    $article["content"] = $doc->saveHTML();
                    $article["plugin_data"] = "4chan,$owner_uid:" . $article["plugin_data"];
                }
            }
            else if (isset($article["stored"]["content"])) {
                $article["content"] = $article["stored"]["content"];
            }
        }
        return $article;
    }
}
?>
