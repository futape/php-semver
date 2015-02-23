<?php
require_once implode(DIRECTORY_SEPARATOR, array(rtrim(__DIR__, DIRECTORY_SEPARATOR), ".", "src", "futape", "semver", "SemVer.php"));

use futape\semver\SemVer;

header("Content-Type: text/plain; charset=utf-8");

$test=array(
    "1.0.0 < 2.0.0 < 2.1.0 < 2.1.1",
    "1.0.0-alpha < 1.0.0",
    "1.0.0-alpha < 1.0.0-alpha.1 < 1.0.0-alpha.beta < 1.0.0-beta < 1.0.0-beta.2 < 1.0.0-beta.11 < 1.0.0-rc.1 < 1.0.0"
);

foreach($test as $val){
    $expect=$val;
    $semVers=array_map(function($val){
        return new SemVer($val);
    }, explode(" < ", $expect));

    shuffle($semVers);

    $data=implode(", ", $semVers);

    usort($semVers, function($a, $b){
        return $a->cmp($b);
    });

    $result=implode(" < ", $semVers);
    $match=$result===$expect;

    echo implode("\n", array(
        "data: ".$data,
        "expected: ".$expect,
        "got: ".$result,
        "result: ".(!$match ? "DOESN'T match" : "matches"),
        "", ""
    ));
}
?>
