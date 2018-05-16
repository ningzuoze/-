<?php
//导入php文件
    require_once 'dir.func.php';
    require_once 'file.func.php';
    require_once 'common.func.php';
    $path="file";
    $redirect="index.php?path={$path}";
    //通过
    $act=@$_REQUEST['act'];
    $filename=@$_REQUEST['filename'];
    $info=readDirectory($path);
    if($act=="createFile"){
    //创建文件
    $mes = createFile($path."/".$filename);
    //弹出提示信息并跳转路径
    alertMes($mes,$redirect);
    }elseif($act=="showContent"){
    //file_get_contents查看文件内容
        $content=file_get_contents($filename);
        // echo "<textarea readonly='readonly' cols='100' rows='10'>{$content}