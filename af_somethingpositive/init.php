<?php
class Af_SomethingPositive extends Plugin {

    function about() {
        return array(0.1, "Fetch image from Something Positive webcomic", "Markus Wiik");

    }
    function init($host) {
        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
    }

    function hook_article_filter($article) {
        $owner_uid = $article["owner_uid"];
        if (strpos($article["link"], "somethingpositive.net") !== FALSE) {
            if (strpos($article["plugin_data"], "somethingpositive,$owner_uid:") === FALSE) {
                $image_url = preg_replace("/shtml/", "png", $article['link']);
                $article["content"] = '<img src="' . $image_url . '"/>';
                $article["plugin_data"] = "somethingpositive,$owner_uid:" . $article["plugin_data"];
            }
            else if (isset($article["stored"]["content"])) {
                $article["content"] = $article["stored"]["content"];
            }
        }
        return $article;
    }
}
?>
