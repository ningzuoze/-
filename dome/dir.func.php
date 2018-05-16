<?php
    function readDirectory($path){
    //打开指定目录（opendir打开路径）
        $handle=opendir($path);
    //判断路径是否为空并保存起来
        while(($item=readdir($handle))!==false){
    //去除特殊目录当前.上层..
            if($item!="."&&$item!=".."){
    //is_file判断是否为文件是文件并保存到数组
                if(is_file($path."/".$item)){
                    $arr['file'][]=$item;
                }
    //is_dir判断是否为文件夹是文件夹并保存到数组
                if(is_dir($path."/".$item)){
                    $arr['dir'][]=$item;
                }
            }
        }
    //关闭目录句柄
        closedir($handle);
        return $arr;
    }

    //得到文件夹大小
    function dirSize($path){
    //求$path文件路径下的文件大小
    $sum=0;
    //打开目录句柄
    $handle=opendir($path);
    //读取路径下的内容并判断是否为空
        while(($item=readdir($handle))!==false){
    //去除当前和上级目录
            if($item!="."&&$item!=".."){
    //判断是否为文件
                if(is_file($path."/".$item)){
    //将此路径下的文件大小加在一起
                    $sum+=filesize($path."/".$item);
                }
    //判断是否问文件夹
                if(is_dir($path."/".$item)){
    //递归函数重复打开文件夹
                    $func=__FUNCTION__;
                    $sum+=$func($path."/".$item);
                }
            }

        }
    //关闭目录句柄
    closedir($handle);
    //返回值
    return $sum;
    }
?>
   