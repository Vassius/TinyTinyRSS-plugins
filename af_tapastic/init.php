<?php
class Af_Tapastic extends Plugin {

    function about() {
        return array(0.1, "Fetch image from comics hosted at tapastic.com", "Markus Wiik");
    }

    function init($host) {
        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
    }

    function hook_article_filter($article) {
        $owner_uid = $article["owner_uid"];

        if (strpos($article["link"], "tapastic.com") !== FALSE) {
            if (strpos($article["plugin_data"], "tapastic,$owner_uid:") === FALSE) {

                $doc = new DOMDocument();
                $doc->loadHTML(fetch_file_contents($article["link"]));

                if ($doc) {
                    $xpath = new DOMXPath($doc);
                    $imgnode = $xpath->query('//img[@class="art-image"]')->item(0);
                    if (is_null($imgnode)) {
                        return $article;
                    }
                    $article["content"] = $doc->saveHTML($imgnode);
                    $article["plugin_data"] = "tapastic,$owner_uid:" . $article["plugin_data"];
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
