<?php
    //转换文件大小
    function transByte($size){
    //建立数组储存单位
        $arr=array("B","KB","MB","GB","TB","EB");
    //小于1024字节为数组第一个
        $i=0;
    //判断大小
        while($size>=1024){
    //除以1024判断大小
            $size/=1024;
    //除一次单位加一
            $i++;
        }
        return round($size,2).$arr[$i];
    }

    //新建文件
    function createFile($filename){
        $pattern="/[\/,\\,\?,<>,\*,\|,:]/";
    //basename返回路径名中的文件名  preg_match(正则，文件)
        if(!preg_match($pattern,basename($filename))){
    //file_exists检测文件重名
            if(!file_exists($filename)){
    //touch 创建文件
                if(touch($filename)){
                    return "创建成功！";
                }else{
                    return "创建失败！";
                }
            }else{
                return "文件已存在，请重新取名";
            }
        }else{
            return "非法文件名";
        }
    }
    //重命名函数
    function renameFile($oldname,$newname){
        //验证文件名是否合法
        if(checkFileName($newname)){
        //dirname()去除文件名留下路径
        $path = dirname($oldname);
        //检测路径下是否已有此文件
            if(!file_exists($path."/".$newname)){
                if(rename($oldname,$path."/".$newname)){
                    return "重命名成功";
                }else{
                    return "重命名失败";
                }
            }else{
                return "文件重名，请重命名";
            }
        }else{
            return "非法文件名";
        }
    }
    //检测文件名合法性
    function checkFileName(){
        $pattern="/[\/,\\,\?,<>,\*,\|,:]/";
        if(preg_match($pattern,$filename)){
            return false;
        }else{
            return true;
        }
    }

    //删除文件函数
    function delFile($filename){
        if(unlink($filename)){
            return "文件删除成功！";
        }else{
            return "文件删除失败！";
        }
    }

    //下载文件函数
    function downFile($filename){
        //下载文件名
        header("content-disposition:attachment;filename=".basename($filename));
        //下载文件大小
        header("content-length:".filesize($filename));
        readfile($filename);
        return "下载成功！";
    }
?>