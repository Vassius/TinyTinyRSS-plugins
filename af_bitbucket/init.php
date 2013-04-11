<?php
class Af_Bitbucket extends Plugin {

    function about() {
        return array(0.1, "Fetch full comments from Bitbucket feed", "Markus Wiik");
    }

    function init($host) {
        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
    }

    function hook_article_filter($article) {
        $owner_uid = $article["owner_uid"];

        if (preg_match('/bitbucket.org/', $article['link'])) {
            if (strpos($article["plugin_data"], "bitbucket,$owner_uid:") === FALSE) {
                if (preg_match('/^[A-Za-z0-9]+ commented/', trim($article['title']))) {

                    /* 
                     * Comments are dynamically loaded into the webpage, so we need to fetch the comment 
                     * from the Bitbucket API
                     */

                    $url = parse_url($article['link']);
                    $path = split('/', $url['path']);
                    $owner = $path[3];
                    $repo = $path[4];
                    $type = $path[5];
                    $typeId = $path[6];
                    $matches = array();
                    preg_match('/([0-9]+)/', $url['fragment'], $matches);
                    $commentId = $matches[1]; // split('-', $url['fragment'])[1];
                    if (strcmp($type, "pull-request") == 0) {
                        $type = "pullrequests";
                    }
                    else if (strcmp($type, "issue") == 0) {
                        $type = "issues";
                    }

                    $jsonUrl = "https://api.bitbucket.org/1.0/repositories/" . $owner . "/" . $repo . "/" . $type . "/" . $typeId . "/comments/" . $commentId;
                    //_debug($jsonUrl);
                    $jsonString = fetch_file_contents($jsonUrl);
                    $json = json_decode($jsonString, true);
                    $content = "<img src='" . $json['author_info']['avatar'] . "'/> " . "<span>". $json['author_info']['display_name'] ."</span>";
                    $paragraphs = explode("\n\n", $json['content']);
                    for ($i = 0; $i < sizeof($paragraphs); $i = $i + 1) {
                        $content = $content . "<p>" . $paragraphs[$i] . "</p>";
                    }
                    preg_replace("/\n/", "<br/>", $content);
                    $article['content'] = $content;
                }
                $article["plugin_data"] = "bitbucket,$owner_uid:" . $article["plugin_data"];
            }
            else if (isset($article["stored"]["content"])) {
                $article["content"] = $article["stored"]["content"];
            }
        }
        return $article;
    }
}
?>
