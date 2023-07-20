<?php
const POSTS_PATH = "../";

class MyDateTime extends \DateTime implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return $this->format("c");
    }
}

function getPosts()
{
    $out = Array();
    $files = glob(POSTS_PATH."*.md",);
    foreach ($files as $postFile)
    {
        $postFileName = substr($postFile,strlen(POSTS_PATH));
        $out[]=array_merge(Array('path'=>$postFileName), getMdMetadata($postFile));
    }

    return $out;
};

function getMdMetadata($path){
    $fres = fopen($path,"r");
    $ret = Array();
    $strPos=0;
    if(strstr(fgets($fres),"---")){
        $current_str="";
        while($current_str!==false && !strstr($current_str,"---")){
            $strPos++;
            $current_str = fgets($fres);
            $currentPair = explode(":",$current_str,2);
            if(count($currentPair)==2) $ret[trim($currentPair[0])]=json_decode(trim($currentPair[1]),true);
        }

        //$ret['published_on'] = new MyDateTime($ret['published_on']);
        //$ret['modified_on'] = date_create($ret['modified_on']);
        $ret['mDataEndPos'] = ftell($fres);
        return  $ret;
    }
    return false;
}

function getMdContent($path, $startPos){
    $fres = fopen($path,"r");
    fseek($fres,$startPos+2);

    $ret = "";
        while(($curStr = fgets($fres))!==false){
            $ret .= $curStr;

        }
    return $ret;


}

function savePost($path, $metadata, $content){
    $out=[];
    if(substr($path,-3,3)==".md") {
        $fres = fopen($path, "w");
        fputs($fres, "---\r\n");

        foreach ($metadata as $key => $value) {
            fputs($fres, "$key: " . json_encode($value) . "\r\n");
        }
        fputs($fres, "---\r\n\r\n");
        fwrite($fres, $content);
        fclose($fres);
        $out[] = error_get_last();
        $out['status'] = 'SAVED';
    }
    else {$out['status'] = 'ERROR';}
    return $out;
}

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");}
    header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))



            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))

            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
}


if(isset($_GET['action'])) $action=$_GET['action'];
else {echo json_encode(getPosts());
die;
}

if($action=="getPost" && isset($_GET['post']) && file_exists(POSTS_PATH.$_GET['post'])){
    $out = getMdMetadata(POSTS_PATH.$_GET['post']);
    $out['content'] = str_replace("â€™","'", getMdContent(POSTS_PATH.$_GET['post'],$out["mDataEndPos"]??0));
    $out['content'] = str_replace("â€","-", $out['content']);
    echo json_encode($out);
}

if($action=="savePost" && isset($_GET['post']) && file_exists(POSTS_PATH.$_GET['post']))
{
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode(savePost(POSTS_PATH.$_GET['post'],$data["metaData"], $data['content']));
}
if($action=="searchPosts" && isset($_GET['search']) && strlen($_GET['search'])>3 )
{
    $rc=0;
    $query = preg_replace('/[^a-zа-я\d\-]/ui', ' ', $_GET['search']);
    $command="grep -li '". escapeshellcmd($query)."' ".POSTS_PATH."*.md";
    exec($command,$out['found'],$rc);
    foreach ($out['found'] as &$item)
    {
        $item = substr($item,strlen(POSTS_PATH));
    }

    $out['er']=error_get_last();

    echo json_encode($out);

}
