<!doctype html>
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" >
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="Content-Language" content="zh-cn"/>
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
<title><?php echo $Title; ?> - <?php echo $Powered; ?></title>
<link rel="stylesheet" href="./css/install.css?v=v1.3.1" />
<script src="./js/jquery.js?v=v1.3.1"></script> 
<script src="./../public/plugins/layer-v3.1.0/layer.js?v=v1.3.1"></script> 
</head>
<body>
<div class="wrap">
  <?php require './templates/header.php';?>
  <section class="section">
    <div class="blank30"></div>
    <div class="go go3"></div>
    <div class="blank30"></div>
    <form id="J_install_form" action="index.php?step=4" method="post">
      <input type="hidden" name="force" value="0" />
      <div class="server">
        <table width="100%" id="table" border="0" cellspacing="1" cellpadding="4">
          <tr>
            <td class="td1" colspan="2">数据库信息</td>
          </tr>
      <tr>
            <td class="tar">数据库地址</td>
            <td><input type="text" name="dbhost" id="dbhost" value="127.0.0.1" class="input"><div id="J_install_tip_dbhost"><span class="gray">一般为127.0.0.1 或 localhost</span></div></td>
          </tr>
      <tr>
            <td class="tar">数据库端口</td>
            <td><input type="text" name="dbport" id="dbport" value="3306" class="input"><div id="J_install_tip_dbport"><span class="gray">一般为3306</span></div></td>
          </tr>
          <tr>
            <td class="tar">数据库账号</td>
            <td><input type="text" name="dbuser" id="dbuser" value="root" class="input"><div id="J_install_tip_dbuser"></div></td>
          </tr>
          <tr>
            <td class="tar">数据库密码</td>
            <td><input type="password" name="dbpw" id="dbpw" value="" class="input" autoComplete="off" onBlur="TestDbPwd(0)"><div id="J_install_tip_dbpw"></div></td>
          </tr>
          <tr>
            <td class="tar">数据库名</td>
            <td><input type="text" name="dbname" id="dbname" value="eyoucms" class="input" onBlur="TestDbPwd(0)"><div id="J_install_tip_dbname"></div></td>
          </tr>
          <tr>
            <td class="tar">数据库表前缀</td>
            <td><input type="text" name="dbprefix" id="dbprefix" value="ey_" class="input" ><div id="J_install_tip_dbprefix"><span class="gray">推荐使用&nbsp;ey_</span></div></td>
          </tr>
        </table>
       
        <table width="100%" id="table" border="0" cellspacing="1" cellpadding="4">
          <tr>
            <td class="td1" colspan="2">管理员信息</td>
          </tr>
          <tr>
            <td class="tar">管理员帐号</td>
            <td><input type="text" name="manager" id="manager" value="admin" class="input"><div id="J_install_tip_manager"></div></td>
          </tr>
          <tr>
            <td class="tar">管理员密码</td>
            <td><input type="password" name="manager_pwd" id="manager_pwd" class="input" autoComplete="off"><div id="J_install_tip_manager_pwd"></div></td>
          </tr>
          <tr>
            <td class="tar">请确认密码</td>
            <td><input type="password" name="manager_ckpwd" id="manager_ckpwd" class="input" autoComplete="off"><div id="J_install_tip_manager_ckpwd"></div></td>
          </tr>
          
        </table>
        <div id="J_response_tips" style="display:none;"></div>
      </div>
      <div class="blank20"></div>
      <div class="bottom tac">
        <center>
        <a href="./index.php?step=2" class="btn_b">上一步</a>
        <button id="next_submit" type="button" onClick="checkForm();" class="btn btn_submit J_install_btn">创建数据</button>
        </center>
      </div>
      <div class="blank20"></div>
    </form>
  </section>
  <div  style="width:0;height:0;overflow:hidden;"> <img src="./images/pop_loading.gif"> </div>
  <script src="./js/jquery.js?v=9.0"></script> 
  <script src="./js/validate.js?v=9.0"></script> 
  <script src="./js/ajaxForm.js?v=9.0"></script> 
  <script src="./../public/plugins/layer-v3.1.0/layer.js?v=9.0"></script> 
  <script>
   
  function TestDbPwd(connect_db)
  {
      var dbHost = $('#dbhost').val();
      var dbUser = $('#dbuser').val();
      var dbPwd = $('#dbpw').val();
      var dbName = $('#dbname').val();
      var dbport = $('#dbport').val();
      var demo  =  $('#demo').val();
      data={'dbHost':dbHost,'dbUser':dbUser,'dbPwd':dbPwd,'dbName':dbName,'dbport':dbport,'demo':demo};
      var url =  "<?php echo $_SERVER['PHP_SELF']; ?>?step=3&testdbpwd=1";
      $.ajax({
          type: "POST",
          url: url,
          data: data,
          dataType:'JSON',
          beforeSend:function(){         
          },
          success: function(res){     
              if(res.errcode == 1)
              {
                  if(connect_db == 1)
                  {
                    ajaxSubmit(); // ajax 验证通过后再提交表单
                    return false;
                  }   
                  $('#J_install_tip_dbpw').html(res.dbpwmsg);
                  $('#J_install_tip_dbname').html(res.dbnamemsg);
              }
              else if(res.errcode == -1)
              {           
                  $('#J_install_tip_dbpw').html(res.dbpwmsg);
              }
              else if(res.errcode == -2)
              {           
                  $('#J_install_tip_dbname').html(res.dbnamemsg);
              }
              else
              {
                  $('#J_install_tip_dbpw').html(res.dbpwmsg);
              }
          },
          complete:function(){
          },
          error:function(){
              $('#J_install_tip_dbpw').html('<span for="dbname" generated="true" class="tips_error" style="">数据库连接失败，请重新设定</span>');    
          }
      });
  }

  function ajaxSubmit()
  {
      $.ajax({
          // async:false,
          url: $('#J_install_form').attr('action'),
          data: $('#J_install_form').serialize(),
          type:'post',
          dataType:'json',
          success:function(res){
              if (1 == res.code) {
                  window.location.href = res.url;
              } else {
                  layer.closeAll();
                  layer.msg(res.msg, {icon: 5});
              }
              return false;
          },
          error:function() {
              layer.closeAll();
              layer.alert('网络失败，请刷新页面后重试', {icon: 5, title: false});
              return false;
          }
      });
  }
   
  function beforeSubmit()
  {
      var flag = false;
      var dbHost = $('#dbhost').val();
      var dbUser = $('#dbuser').val();
      var dbPwd = $('#dbpw').val();
      var dbName = $('#dbname').val();
      var dbport = $('#dbport').val();
      data={'dbHost':dbHost,'dbUser':dbUser,'dbPwd':dbPwd,'dbName':dbName,'dbport':dbport};
      var url =  "<?php echo $_SERVER['PHP_SELF']; ?>?step=3&check=1";
      $.ajax({
          type: "POST",
          url: url,
          async: false,
          data: data,
          dataType:'JSON',
          beforeSend:function(){
          },
          success: function(res){
              if (-1 == res.code) {
                  layer.closeAll();
                  layer.msg(res.msg, {icon: 5});
              } else {
                  flag = true;
              }
          },
          complete:function(){
          },
          error:function(){
              layer.closeAll();
              layer.alert('网络失败，请刷新页面后重试', {icon: 5, title: false});
          }
      });

      return flag;
  }

  function checkForm()
  {
      dbhost = $.trim($('#dbhost').val());        //数据库地址
      dbport = $.trim($('#dbport').val());        //数据库端口
      dbuser = $.trim($('#dbuser').val());        //数据库账号
      dbpw = $.trim($('#dbpw').val());        //数据库密码
      dbname = $.trim($('#dbname').val());        //数据库名
      dbprefix = $.trim($('#dbprefix').val());        //数据库表前缀
      manager = $.trim($('#manager').val());        //用户名表单
      manager_pwd = $.trim($('#manager_pwd').val());        //密码表单
      manager_ckpwd = $.trim($('#manager_ckpwd').val());    //密码提示区
       
      if(dbhost.length == 0 )
      {
        $('#dbhost').focus();
        layer.msg('数据库地址不能为空', {icon: 5, time: 1500});
        return false;
      }
      if(dbport.length == 0 )
      {
        $('#dbport').focus();
        layer.msg('数据库端口不能为空', {icon: 5, time: 1500});
        return false;
      }
      if(dbuser.length == 0 )
      {
        $('#dbuser').focus();
        layer.msg('数据库账号不能为空', {icon: 5, time: 1500});
        return false;
      }
      if(dbpw.length == 0 )
      {
        $('#dbpw').focus();
        layer.msg('数据库密码不能为空', {icon: 5, time: 1500});
        return false;
      }
      if(dbname.length == 0 )
      {
        $('#dbname').focus();
        layer.msg('数据库名不能为空', {icon: 5, time: 1500});
        return false;
      }
      if(dbprefix.length == 0 )
      {
        $('#dbprefix').focus();
        layer.msg('数据库表前缀不能为空', {icon: 5, time: 1500});
        return false;
      }
      if(manager.length == 0 )
      {
        $('#manager').focus();
        layer.msg('管理员账号不能为空', {icon: 5, time: 1500});
        return false;
      }
      if(manager_pwd.length < 5 )
      {
        $('#manager_pwd').focus();
        layer.msg('管理员密码必须5位数以上', {icon: 5, time: 1500});
        return false;
      } 
      if(manager_ckpwd !=  manager_pwd)
      {
        $('#manager_ckpwd').focus();
        layer.msg('两次密码不一致', {icon: 5, time: 1500});
        return false;
      }
      layer_loading('正在安装');
      if (!beforeSubmit()) {
        return false;
      }
      TestDbPwd(1);
  }

  /**
   * 封装的加载层
   */
  function layer_loading(msg){
      var loading = layer.msg(
      msg+'...<img src="./images/loading-0.gif"/>&nbsp;请勿刷新页面', 
      {
          icon: 1,
          time: 3600000, //1小时后后自动关闭
          shade: [0.2] //0.1透明度的白色背景
      });

      return loading;
  }
</script> 
</div>
<?php require './templates/footer.php';?>

<script type="text/javascript">
  $(function(){
    $('#next_submit').focus();
    $(document).keydown(function(event){
        if(event.keyCode ==13){
            checkForm();
            return false;
        }
    });
  });
</script>
</body>
</html>