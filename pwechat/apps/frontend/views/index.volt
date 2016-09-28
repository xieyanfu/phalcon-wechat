<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phalcon番茄时钟</title>
    <style>
        body{
            background-color: cadetblue;
        }
    </style>
    <?php
  $this->assets->outputJs();
    $this->assets->outputCss();
    ?>
</head>
<body>
<center>
<div>
    <span style="color:cyan;font-weight: bold;font-size:4.5rem;">Phalcon 番茄时钟</span>
    <hr>
    <img src="{{ info['headimgurl'] }}" style="width:300px;height:300px;border-radius:150px;">
    <br>
    <span style="color:yellow;font-weight:bold;font-size:4rem;">{{ info['nickname'] }}</span>
    <br>
    <hr>
     <div>
          <div id="retroclockbox1"></div>
     </div>
    <hr>
    <br><br>
    <button id="start" style="font-weight: bold;font-size: 5rem;color:red;">开始</button>
</div>
</center>

<script>
    $('#start').click(function(){
        alert('您确定要开始任务?');
    });
    $(function(){
        $('#retroclockbox1').flipcountdown();
    })
</script>
</body>
</html>