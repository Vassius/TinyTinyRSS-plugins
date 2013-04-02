<?php
class Af_SomethingPositive extends Plugin {
    private $host;
    private $link;

    function about() {
        return array(0.1, "Fetch image from Something Positive webcomic", "Markus Wiik");

    }
    function init($host) {
        $this->link = $host->get_link();
        $this->host = $host;
        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
    }

    function hook_article_filter($article) {
        $owner_uid = $article["owner_uid"];
        if (strpos($article["link"], "somethingpositive.net") !== FALSE) {
            if (strpos($article["plugin_data"], "somethingpositive,$owner_uid:") === FALSE) {
                $url = parse_url($article["link"]);
                $image_name = substr($url["path"], 0, -5) . "png";
                $article["content"] = '<img src="http://' . $url["host"] . '/' . $image_name . '"/>';
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
