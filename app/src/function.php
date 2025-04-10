<?php

//htmlspecialchars()を省略
function h($value) {
    return htmlspecialchars($value, ENT_QUOTES);
}


//改行に対応して出力する用
function nh($value) {
    return nl2br(htmlspecialchars($value, ENT_QUOTES));
}
?>