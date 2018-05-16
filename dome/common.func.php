<?php
//对返回信息做出提示并跳转页面
function alertMes($mes,$url){
    echo "<script>alert('{$mes}');location.href='$url';</script>";
}