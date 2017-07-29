<?php
ob_start ();
$table_color_arr = explode(" ", "red orange yellow olive teal blue violet purple pink grey black");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $apiDirName; ?> - 在线接口列表 - <?php echo $projectName; ?></title>
    <link href="https://cdn.bootcss.com/semantic-ui/2.2.2/semantic.min.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/semantic-ui/2.2.2/semantic.min.js"></script>
    <meta name="robots" content="none"/>
</head>
<body>
<br/>


<div class="ui text container" style="max-width: none !important; width: 1200px" id="menu_top">
    <div class="ui floating message">
        <?php
        if (!empty($errorMessage)) {
        echo  '<div class="ui error message">
            <strong>错误：' . $errorMessage . '</strong> 
            </div>';
        }
        ?>

        <div class="ui grid container" style="max-width: none !important;">
            <?php
            if ($theme == 'fold') {
            ?>
            <div class="four wide column">
                <div class="ui vertical pointing menu">
                    <?php
                    $methodTotal = 0;
                    foreach ($allApiS as $item) {
                        $methodTotal += count($item['methods']);
                    }
                    ?>
                    <div class="item"><h4>接口服务列表&nbsp;(<?php echo $methodTotal; ?> )</h4></div>
                    <?php
                    $num = 0;
                    foreach ($allApiS as $key => $item) {
                        ?>
                        <a class="item <?php if ($num == 0) {
                            echo 'active';
                        } ?>" data-tab="<?php echo $key; ?>"><?php echo $item['title']; ?> </a>
                        <?php
                        $num++;
                    }

                    ?>
                    <?php
                    if ($num > 12) {
                        echo '<a class="item" href="#menu_top">返回顶部↑↑↑</a>';
                    }
                    ?>
                </div>
            </div>
            <?php } ?> <!-- 折叠时的菜单 -->

            <!-- 折叠时与展开时的布局差异 -->
            <?php if ($theme == 'fold') { ?>
            <div class="twelve wide stretched column">
            <?php } else { ?>
            <div class="wide stretched column">
            <?php 
                    // 展开时，将全部的接口服务，转到第一组
                    $mergeAllApiS = array('all' => array('methods' => array()));
                    foreach ($allApiS as $key => $item) {
                        foreach ($item['methods'] as $mKey => $mItem) {
                            $mergeAllApiS['all']['methods'][$mKey] = $mItem;
                        }
                    }
                    $allApiS = $mergeAllApiS;
            } 
            ?>
                <?php
                $uri  = str_ireplace('listAllApis.php', 'checkApiParams.php', $_SERVER['REQUEST_URI']);
                $num2 = 0;
                foreach ($allApiS as $key => $item) {
                    ?>
                    <div class="ui  tab <?php if ($num2 == 0) { ?>active<?php } ?>" data-tab="<?php echo $key; ?>">
                        <table
                            class="ui red celled striped table <?php echo $table_color_arr[$num2 % count($table_color_arr)]; ?> celled striped table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>接口服务</th>
                                <th>接口名称</th>
                                <th>更多说明</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            $num = 1;
                            foreach ($item['methods'] as $mKey => $mItem) {
                                $concator = strpos($uri, '?') ? '&' : '?';
                                $link = $uri . $concator . 'service=' . $mItem['service'];
                                $NO   = $num++;
                                echo "<tr><td>{$NO}</td><td><a href=\"$link\" target='_blank'>{$mItem['service']}</a></td><td>{$mItem['title']}</td><td>{$mItem['desc']}</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>

                    <!-- 主题切换，仅当在线时才支持 -->
                    <?php
                        $curUrl = $_SERVER['SCRIPT_NAME'];
                        if ($theme == 'fold') {
                            echo '<div style="float: right"><a href="' . $uri . '&type=expand">切换回展开版</a></div>';
                        } else {
                            echo '<div style="float: right"><a href="' . $uri . '&type=fold">切换回折叠版</a></div>';
                        }
                    ?>

                    </div>
                    <?php
                    $num2++;
                }
                ?>


            </div>
        </div>
        <div class="ui blue message">
            <strong>温馨提示：</strong> 此接口服务列表根据后台代码自动生成，可在接口类的文件注释的第一行修改左侧菜单标题。
        </div>
        <p>&copy; Powered  By <a href="http://www.phalapi.net/" target="_blank">PhalApi</a> <span id="version_update"></span> <p>
    </div>
    </div>
</div>
<script type="text/javascript">
    $('.pointing.menu .item').tab();
    $('.ui.sticky').sticky();
	//当点击跳转链接后，回到页面顶部位置
    $(".pointing.menu .item").click(function() {
        $('body,html').animate({
                scrollTop: 0
            },
            500);
        return false;
    });


    $('#version_update').html('&nbsp; | &nbsp; <a target="_blank" href="http://www.liyangweb.com"><strong>kaopur移植至Yii2</strong></a>');
</script>

</body>
</html>


