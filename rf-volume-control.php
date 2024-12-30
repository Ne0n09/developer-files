<?php
include_once "/opt/fpp/www/common.php";
$pluginConfigFile = $settings['configDirectory'] . "/plugin.remote-falcon";
$pluginSettings = parse_ini_file($pluginConfigFile);


$remoteFppEnabled = urldecode($pluginSettings['remote_fpp_enabled']);
$remoteFppEnabled = $remoteFppEnabled == "true" ? true : false;
$remotePlaylist = urldecode($pluginSettings['remotePlaylist']);


if($remoteFppEnabled == 1) {
    while(true) {
        $fppStatus = getFppStatus();
        $currentVolume = $fppStatus->volume;

        //This is the volume you want to have when a request is playing
        $requestVolume = 100;
        //This is the volume you want to have when a request is not playing
        $normalVolume = 50;

        if($fppStatus->scheduler->status=="playing") {
            $currentPlaylist = $fppStatus->current_playlist->playlist;
            if($currentPlaylist == $remotePlaylist) {
                if($currentVolume != $requestVolume) {
                    setVolume($requestVolume);
                }
            }else {
                if($currentVolume != $normalVolume) {
                    setVolume($normalVolume);
                }
            }
        }
        sleep(1);
    }
}

function setVolume($volume) { 
    echo "Setting volume to " . $volume;
    $url = "http://127.0.0.1/api/command/Volume%20Set/" . $volume;
    $options = array(
        'http' => array(
        'method'  => 'GET'
        )
    );
    $context = stream_context_create( $options );
    $result = file_get_contents( $url, false, $context );
}

function getFppStatus() {
    $result = file_get_contents("http://127.0.0.1/api/fppd/status");
    return json_decode( $result );
}
?>
