<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Package Status Checker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="../assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="../assets/ico/favicon.png">
  </head>

  <body>

    <!-- facebook button script-->
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">Package Status Checker</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="mailto:shereshevsky@gmail.com">Contact</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span10">
          <h1>Package Status Checker</h1>
<?php
        include('simple_html_dom.php');
        include('get_status_functions.php');
        include_once('MySQL.class.php');
        $db = new MySQL('DATABASE_NAME', 'DATABASE_OWNER', 'DATABASE_PASSWORD', 'DATABASE_HOST');
  
        if(isset($_REQUEST['periodic'])) {
          fn_periodic_check();
        }
        elseif(isset($_POST['itemcode'])) {
  
          $itemcode = isset($_POST['itemcode'])?preg_replace("/[^A-Za-z0-9\r\n]/u", "", $_POST['itemcode']):false;
          $email = isset($_POST["email"])?$_POST['email']:false;;
  
          $itemcodeArr = array();
          $itemcodeArr = preg_split("/\r\n/",$itemcode,-1,PREG_SPLIT_NO_EMPTY);
          $itemcodeArr = array_unique($itemcodeArr);
  
          //if(preg_match("/^[a-zA-Z]\w+(\.\w+)*\@\w+(\.[0-9a-zA-Z]+)*\.[a-zA-Z]{2,4}$/", $email) === 0) {
  
          foreach ($itemcodeArr as $item) {
            //if(preg_match("/^\D{2}\d{9}\D{2}$|^9\d{15,21}$/", $item) === 0) {
            print fn_israpost($item, $email, false)."<br>";
          }
        }elseif(isset($_REQUEST['item'])) {
          $item = isset($_REQUEST['item'])?$_REQUEST['item']:false;
          $email = isset($_REQUEST["email"])?$_REQUEST['email']:false;
          fn_israpost($item, $email, true);
        }else{
  ?>
        <p>Bulk status checker for Israel Post with notifications about status change.
          <br> If you have a package(s) should be delivered to Israel, just enter tracking number(s) and email address,
          <br> I will check the delivery status and sent notification when the item arrives to Israel.
        </p>
  
      <table>
        <form action="index.php" method="post">
          <tr>
            <td>Tracking      Number(s):</td>
            <td><textarea name="itemcode" rows="3" ></textarea></td>
          </tr>
          <tr>
            <td>Email for Notifications:</td>
            <td><input type="text" name="email" /></td>
          </tr>
          <tr>
            <td></td><td><input type="submit" value="Submit"></td>
          </tr>
        </form>
      </table>

    <div class="social">

      <div class="fb-like" data-href="http://kelim2go.com/pack_status/" data-send="true" data-layout="button_count" data-width="400" data-show-faces="true" data-font="tahoma"></div>

      <!-- Place this tag where you want the +1 button to render. -->
      <div class="g-plusone" data-size="medium" data-annotation="none" data-width="300"></div>

      <!-- Place this tag after the last +1 button tag. -->
      <script type="text/javascript">
        (function() {
          var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
          po.src = 'https://apis.google.com/js/plusone.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
        })();
      </script>

      <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
      <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

    </div>

<?php
}
?>
    </div>
      <div class="span2">
        <script type="text/javascript"><!--
        google_ad_client = "ca-pub-6913896091659544";
        /* right block pack_stat */
        google_ad_slot = "7134357492";
        google_ad_width = 160;
        google_ad_height = 600;
        //-->
        </script>
        <script type="text/javascript"
        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
        </script>
      </div>
    </div>

<hr>

  <div class="footer">
    <p>&copy; <a href="mailto:shereshevsky@gmail.com">Alexander Shereshevsky</a> 2013</p>
  </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../assets/js/google_analytics.js"></script>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap-transition.js"></script>
    <script src="../assets/js/bootstrap-alert.js"></script>
    <script src="../assets/js/bootstrap-modal.js"></script>
    <script src="../assets/js/bootstrap-dropdown.js"></script>
    <script src="../assets/js/bootstrap-scrollspy.js"></script>
    <script src="../assets/js/bootstrap-tab.js"></script>
    <script src="../assets/js/bootstrap-tooltip.js"></script>
    <script src="../assets/js/bootstrap-popover.js"></script>
    <script src="../assets/js/bootstrap-button.js"></script>
    <script src="../assets/js/bootstrap-collapse.js"></script>
    <script src="../assets/js/bootstrap-carousel.js"></script>
    <script src="../assets/js/bootstrap-typeahead.js"></script>

  </body>
</html>
