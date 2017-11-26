<?php error_reporting(0);

function curl($url)
{
    $ch = @curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    $head[] = "Connection: keep-alive";
    $head[] = "Keep-Alive: 300";
    $head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $head[] = "Accept-Language: en-us,en;q=0.5";
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    $page = curl_exec($ch);
    curl_close($ch);
    return $page;
}
 
function getIdVideo($_link)
{
    if(substr($_link, -1) != '/' && is_numeric(substr($_link, -1))){
        $_link = $_link .'/';
    }
    preg_match('/https:\/\/www.facebook.com\/(.*)\/videos\/(.*)\/(.*)\/(.*)/U', $_link, $id); // ex: https://www.facebook.com/userName/videos/vb.IDuser/IDvideo/?type=2&theater
    if(isset($id[4])){
        $idVideo = $id[3];
    }else{
        preg_match('/https:\/\/www.facebook.com\/(.*)\/videos\/(.*)\/(.*)/U', $_link, $id); // ex: https://www.facebook.com/userName/videos/IDvideo
        if(isset($id[3])){
            $idVideo = $id[2];
        }else{
            preg_match('/https:\/\/www.facebook.com\/video\.php\?v\=(.*)/', $_link, $id); // ex: https://www.facebook.com/video.php?v=IDvideo
            $idVideo = $id[1];
            $idVideo = substr($idVideo, 0, -1);
        }
    }
    return $idVideo;
}
 
function getLinkPublicVideo($_link)
{
    $video_id = getIdVideo($_link);
    $link = 'https://www.facebook.com/video/embed?video_id=' . $video_id;
 
    $getPage = curl($link);
 
    //$pattern = '/\{\"is_hds\"\:(.+)\"is_spherical\"\:(true|false)\}/';
    $pattern = '/\{\"is_hds\"\:(.+?)\}/';
     
    $subject = curl($link);
    preg_match($pattern, $subject, $matches);
 
 
    $arr = json_decode($matches[0]);
 
    $linkDownload = array();
    if (isset($arr->hd_src)) {
        $linkDownload['HD'] = $arr->hd_src;
    }
    if (isset($arr->sd_src)) {
        $linkDownload['SD'] = $arr->sd_src;
    }
 
    return $linkDownload;
}