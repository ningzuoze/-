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
         if(strlen($content)){
              // echo "<textarea readonly='readonly' cols='100' rows='10'>{$content}</textarea>";
        //高亮显示字符串PHP代码
        $newContent=highlight_string($content,true);
        //高亮显示文件PHP代码
        // highlight_file($filename);
        $str=<<<EOF
        <table width="100%" style="border:1px solid red;" cellpadding="0" cellspacimg="0">
            <tr>
                <td>
                   {$newContent}
                </td>
            </tr>
        </table>
EOF;
        echo $str;
         }else{
             alertMes("文件没有内容，请编辑再查看！",$redirect);
         }
    }elseif ($act=="editContent") {
        //修改文件内容
        //打开文件
        $content=file_get_contents($filename);
        $str=<<<EOF
        <form action="index.php?act=doEdit" method="post">
            <textarea name="content" cols='190' rows='10'>{$content}</textarea><br/>
            <input type="hidden" name="filename" value="{$filename}"/>
            <input style="float:right;margin:10px;" class="btn btn-info" type="submit" value="修改文件内容"/>
        </form>
EOF;
        echo $str;
    }elseif($act=="doEdit"){
        //获取传输过来的数据$_REQUEST（全局）
        $content=$_REQUEST['content'];
        if(file_put_contents($filename,$content)){
            $mes="文件修改成功";
        }else{
            $mes="文件修改失败";
        }
        //弹出提示信息并刷新$readirect首页
        alertMes($mes,$redirect);
    }else if($act=="renameFile"){
        //完成重命名
        $str=<<<EOF
        <form action="index.php?act=doRename" method="post">
        请填写新文件名：<input type="text" name="newname" placeholder="重命名"/>
        <input type="hidden" name="filename" value='{$filename}'/>
        <input type="submit" value="重命名" />
        </form>
EOF;
        echo $str;
        //$filename
    }else if($act=="doRename"){
        //实现重命名

        //获取重命名的值
        $newname=$_REQUEST['newname'];
        //重命名函数
        $mes = renameFile($filename,$newname);
        //对返回信息做出提示并跳转页面
        alertMes($mes,$redirect);
    }else if($act=="delFile"){
        //调用删除函数$filename(文件路径和文件名)
        $mes=delFile($filename);
        alertMes($mes,$redirect);
    }else if($act=="downFile"){
        $mes=downFile($filename);
        alertMes($mes,$redirect);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>文件管理</title>
    <link rel="stylesheet" href="style/bootstrap.css">
    <link rel="stylesheet" href="jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.min.css">
    <style>
        .head{
            margin-bottom:20px;
        }
        img{
            width:32px;
            height:32px;
        }
        .h{
            display:none;
            margin-bottom:20px;
        }
        code{
            background: none;
        }
        #showImg{
            width:100%;
            height:100%;
        }
    </style>
</head>
<body>
    <div class="container">
    <div id="showDetail" style="display:none;margin:20px auto"><img src="" id="showImg" alt=""></div>
        <h1>在线管理器</h1>
        <div class="head">
            <div class="btn-group btn-group-lg">
                <button class="btn btn-default" title="主页" id="homeBtn">
                    <a href="<?php echo $redirect;?>"><span class="glyphicon glyphicon-home" title="主页"></span></a>
                </button>
                <button class="btn btn-default" title="新建文件" id="newFileBtn">
                    <span class="glyphicon glyphicon-file"></span>
                </button>
                <button class="btn btn-default">
                    <span class="glyphicon glyphicon-folder-close title="新建文件""></span>
                </button>
                <button class="btn btn-default">
                    <span class="glyphicon glyphicon-upload"></span>
                </button>
                <button class="btn btn-default">
                    <span class="glyphicon glyphicon-arrow-left"></span>
                </button>
            </div>
        </div>
        <form class="form-inline h" id="newFileInput">
            <div class="form-group">
                <label for="newFile">请输入文件名称</label>
                <input type="hidden" name="path" value="<?php echo $path;?>"/>
                <input type="hidden" name="act" value="createFile"/>
                <input type="text" name="filename" class="form-control" id="newFile" placeholder="请输入文件夹名">
            </div>
            <button type="submit" class="btn btn-default">创建文件</button>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <th>编号</th>
                    <th>文件</th>
                    <th>类型</th>
                    <th>大小</th>
                    <th>可读</th>
                    <th>可写</th>
                    <th>可执行</th>
                    <th>可创建时间</th>
                    <th>修改时间</th>
                    <th>访问时间</th>
                    <th>操作</th>
                </thead>
                <?php 
                    if($info['file']){
                        $i=1;
                        foreach($info['file'] as $val){
                        $p=$path.'/'.$val;
                        
                ?>
                <tr>
                    <td><?php echo $i;?></td>
                    <td><?php echo $val;?></td>
                    <td><?php $src = filetype($p)=='file'?'file_ico.png':'folder_ico.png';?><img src="images/<?php echo $src;?>" title="文件"></td>
                    <td><?php echo transByte(filesize($p)); ?></td>
                    <!-- is_readable判断是否可读 -->
                    <td><?php $src=is_readable($p)?"correct.png":"error.png";?><img style="width:32px;height:32px;" src="images/<?php echo $src ?>"/></td>
                    <!-- is_writable判断是否可写 -->
                    <td><?php $src=is_writable($p)?"correct.png":"error.png";?><img style="width:32px;height:32px;" src="images/<?php echo $src ?>"/></td>
                    <!-- is_executable判断是否可执行 -->
                    <td><?php $src=is_executable($p)?"correct.png":"error.png";?><img style="width:32px;height:32px;" src="images/<?php echo $src ?>"/></td>
                    <!-- filectime创建文件的日期 data格式化时间  -->
                    <td><?php echo date("Y-m-d H:i:s",filectime($p)); ?></td>
                    <!-- filemtime修改文件的日期 data格式化时间  -->
                    <td><?php echo date("Y-m-d H:i:s",filemtime($p)); ?></td>
                    <!-- filectiae访问文件的日期 data格式化时间  -->
                    <td><?php echo date("Y-m-d H:i:s",filectime($p)); ?></td>
                    <td>
                        <?php 
                           
                            $arr = (explode(".",$val));//切割文件后缀
                            $ext = end($arr);//取得后缀名
                            $tex = strtolower($ext);//小写后缀名
                            $imagesExt=array("gif","jpg","jpeg","png");
                            if(in_array($tex,$imagesExt)){ 
                        ?>
                            <a href="#" onclick="showContent('<?php echo $val ?>','<?php echo $p ?>')"><img src="images/show.png" title="查看"/></a>           
                        <?php                         
                            }else{
                        ?>
                        <a href="index.php?act=showContent&filename=<?php echo $p;?>"><img src="images/show.png" title="查看"/></a>
                        <?php } ?>
                        <a href="index.php?act=editContent&filename=<?php echo $p;?>"><img src="images/edit.png" title="编辑"/></a>
                        <a href="index.php?act=renameFile&filename=<?php echo $p; ?>"><img src="images/rename.png" title="重命名"/></a>
                        <a><img src="images/copy.png" title="复制"/></a>
                        <a><img src="images/cut.png" title="剪切"/></a>
                        <a href="#" onclick="delFile('<?php echo $p;?>')"><img src="images/delete.png" title="删除"/></a>
                        <a href="index.php?act=downFile&filename=<?php echo $p ?>"><img src="images/download.png" title="下载"/></a>                        
                    </td>
                </tr>
                <?php
                        $i++;   
                        }
                    }
                ?>

                <!-- 读取目录的操作-->
                <?php 
                    if($info['dir']){
                        foreach($info['dir'] as $val){
                        //文件的路径
                        $p=$path.'/'.$val;
                ?>
                <tr>
                    <td><?php echo $i;?></td>
                    <td><?php echo $val;?></td>
                    <td><?php $src = filetype($p)=='file'?'file_ico.png':'folder_ico.png';?><img src="images/<?php echo $src;?>" title="文件"></td>
                    <td><?php echo transByte(dirSize($p)); ?></td>
                    <!-- is_readable判断是否可读 -->
                    <td><?php $src=is_readable($p)?"correct.png":"error.png";?><img style="width:32px;height:32px;" src="images/<?php echo $src ?>"/></td>
                    <!-- is_writable判断是否可写 -->
                    <td><?php $src=is_writable($p)?"correct.png":"error.png";?><img style="width:32px;height:32px;" src="images/<?php echo $src ?>"/></td>
                    <!-- is_executable判断是否可执行 -->
                    <td><?php $src=is_executable($p)?"correct.png":"error.png";?><img style="width:32px;height:32px;" src="images/<?php echo $src ?>"/></td>
                    <!-- filectime创建文件的日期 data格式化时间  -->
                    <td><?php echo date("Y-m-d H:i:s",filectime($p)); ?></td>
                    <!-- filemtime修改文件的日期 data格式化时间  -->
                    <td><?php echo date("Y-m-d H:i:s",filemtime($p)); ?></td>
                    <!-- filectiae访问文件的日期 data格式化时间  -->
                    <td><?php echo date("Y-m-d H:i:s",filectime($p)); ?></td>
                    <td>
                        <a href="index.php?act=showContent&filename=<?php echo $p;?>"><img src="images/show.png" title="查看"/></a>
                        <a href="index.php?act=editContent&filename=<?php echo $p;?>"><img src="images/edit.png" title="编辑"/></a>
                        <a href="index.php?act=renameFile&filename=<?php echo $p; ?>"><img src="images/rename.png" title="重命名"/></a>
                        <a><img src="images/copy.png" title="复制"/></a>
                        <a><img src="images/cut.png" title="剪切"/></a>
                        <a href="#" onclick="delFile('<?php echo $p;?>')"><img src="images/delete.png" title="删除"/></a>
                        <a href="index.php?act=downFile&filename=<?php echo $p ?>"><img src="images/download.png" title="下载"/></a>                        
                    </td>
                </tr>
                <?php
                $i++;
                        }
                    }
                ?>

            </table>
        </div>
    </div>
    <script src="script/jquery-3.2.1.min.js"></script>
    <script src="script/bootstrap.min.js"></script>
    <script src="jquery-ui/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script>
        function showContent (t,f) {
                    $("#showImg").attr("src",f);
                    $("#showDetail").dialog({
                        width:"auto",
                        height:"auto",
                        title:t
                    });
                }
        function delFile(filename) {
            if(window.confirm("您确定要删除吗？删除之后无法恢复哦！")){
                location.href="index.php?act=delFile&filename="+filename;
            }
        }
        $(document).ready(function(){
            $("#homeBtn").click(function(){
                location.reload();
            })
            $("#newFileBtn").click(function(){
                $("#newFileInput").toggle();
            })
           
        });
    </script>
</html>