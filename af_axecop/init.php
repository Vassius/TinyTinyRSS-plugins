<?php
class Af_AxeCop extends Plugin {

    function about() {
        return array(0.1, "Fetch image from the Axe Cop webcomic", "Markus Wiik");

    }
    function init($host) {
        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
    }

    function hook_article_filter($article) {
        $owner_uid = $article["owner_uid"];
        if (strpos($article["link"], "axecop.com") !== FALSE) {
            if (strpos($article["plugin_data"], "axecop,$owner_uid:") === FALSE) {
                $url = parse_url($article["link"]);
                $matches = array();
                $result = preg_match("/episode_([0-9]+)\/$/", $url["path"], $matches);
                if ($result === FALSE || $result == 0) {
                    return $article;
                }
                $image_name = "axecop" . $matches[1] . ".jpg";
                $image_path = "/images/uploads/" . $image_name;
                $article["content"] = '<img src="http://' . $url["host"] . '/' . $image_path . '"/>';
                $article["plugin_data"] = "axecop,$owner_uid:" . $article["plugin_data"];
            }
            else if (isset($article["stored"]["content"])) {
                $article["content"] = $article["stored"]["content"];
            }
        }
        return $article;
    }
}
?>
