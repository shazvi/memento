<!DOCTYPE html>
<html lang="en">
<head>
    <title>Memento<?php if(isset($title)) echo ": ".$title; ?></title>
    <meta charset="utf-8">
        <!--  CSS  -->
        <link href="<?php echo base_url();?>css/bootstrap-combined.min.css" rel="stylesheet">
        <link href="<?php echo base_url();?>css/styles.css" rel="stylesheet"/>
        <link href="<?php echo base_url();?>css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet"/>

        <link rel="icon" type="image/png" href="<?php echo base_url();?>img/icon.png">
        
        <!--  JS  -->
        <script src="<?php echo base_url();?>js/jquery-1.8.2.js"></script>
        <script src="<?php echo base_url();?>js/bootstrap.min.js"></script>
        <script src="<?php echo base_url();?>js/jquery-ui-1.10.4.custom.min.js"></script>
        <script src="<?php echo base_url();?>js/sorttable.js"></script>
        <script src="<?php echo base_url();?>js/jquery.highlight-upd.js"></script>
        
        <!--  X-EDITABLE_BOOTSTRAP  -->
        <link href="<?php echo base_url();?>editable/bootstrap/css/bootstrap-editable.css" rel="stylesheet"/>
        <script src="<?php echo base_url();?>editable/bootstrap/js/bootstrap-editable.min.js"></script>
        
        <!--  X-EDITABLE_INPUTS_ADDRESS 
        <link href="<?php echo base_url();?>editable/inputs/address/address.css" rel="stylesheet"/>
        <script src="<?php echo base_url();?>editable/inputs/address/address.js"></script> -->
        <!--  X-EDITABLE_INPUTS_TYPEAHEAD 
        <script src="<?php echo base_url();?>editable/inputs/typeaheadjs/typeaheadjs.js"></script>
        <script src="<?php echo base_url();?>editable/inputs/typeaheadjs/lib/typeahead.js"></script>
        <link href="<?php echo base_url();?>editable/inputs/typeaheadjs/lib/typeahead.css" rel="stylesheet"/> -->
        
        <!--  X-EDITABLE_INPUTS_WYSIHTML5 
        <script src="<?php echo base_url();?>editable/inputs/wysihtml5/wysihtml5.js"></script>
        <script src="<?php echo base_url();?>editable/inputs/wysihtml5/lib/wysihtml5-0.3.0.min.js"></script>
        <link href="<?php echo base_url();?>editable/inputs/wysihtml5/lib/wysiwyg-color.css" rel="stylesheet"/> -->

        <!--  X-EDITABLE_SELECT2  -->
        <script src="<?php echo base_url();?>editable/select2/select2.full.min.js" type="text/javascript"></script>
        <link href="<?php echo base_url();?>editable/select2/select2.min.css" rel="stylesheet"/>

        <!--  X-EDITABLE_DATE-TIME  -->
        <script src="<?php echo base_url();?>editable/date-time/datetimepicker.min.js" type="text/javascript"></script>
        <link href="<?php echo base_url();?>editable/date-time/datetimepicker.min.css" rel="stylesheet"/>

</head>
<body style="background: #c1c1c1 url(<?php echo base_url();?>img/back.png) repeat-x;">

    <div class="container-fluid">

            <div id="top">
                <?php if($this->usermodel->url_contains("about")) : ?>
                    <canvas id="myCanvas" style='height:400px'></canvas>
                    <script src="<?php echo base_url();?>js/alphabet.js"></script>
                    <script src="<?php echo base_url();?>js/bubbles.js"></script>
                    <script type="text/javascript">
                        //Title Bubbles
                        var myName = "Memento";
                    
                        var blue1 = [195, 80, 60];
                        //var orange = [40, 100, 60];
                        //var green = [75, 100, 40];
                        var blue2 = [200, 75, 55];
                        var blue3 = [205, 70, 50];
                        var letterColors = [blue1, blue2, blue3];
                        
                        drawName(myName, letterColors);
                        bubbleShape = 'circle';
                        bounceBubbles();
                    </script>
                <?php else: ?>
                    <a class="topp"><img alt="Memento" src="<?php echo base_url();?>img/logo.png"/></a><br>
                    <img class="topp" alt="A simple reminder app" src="<?php echo base_url();?>img/banner.png"/>
                <?php endif ?>
            </div>
            <div id="middle">
                <div >
                    <?php if ($this->session->userdata('id') == "") : ?>
                        <ul class="nav nav-tabs">
                            <li <?php if($this->usermodel->url_contains("login")) echo 'class="active"'; ?> >
                                <a href="<?php echo base_url();?>page/login"><i class="icon-home"></i> <strong> Login</strong></a>
                            </li>
                            <li <?php if($this->usermodel->url_contains("create")) echo 'class="active"'; ?> >
                                <a href="<?php echo base_url();?>page/create"><i class="icon-file"></i> <strong> Register</strong> New User</a>
                            </li>
                            <li <?php if($this->usermodel->url_contains("forgot")) echo 'class="active"'; ?> >
                                <a href="<?php echo base_url();?>page/forgot"><i class="icon-flag"></i> <strong> Forgot</strong> Password</a>
                            </li>
                            <li <?php if($this->usermodel->url_contains("about")) echo 'class="active"'; ?> >
                                <a href="<?php echo base_url();?>page/about"><i class="icon-list"></i> <strong> About</strong> Page</a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <?php $name = $this->usermodel->get_name($this->session->userdata("id"));?>
                        <ul class="nav nav-tabs">
                            <li <?php if($this->usermodel->url_contains("index")) echo 'class="active"'; ?> >
                                <a href="<?php echo base_url();?>"><i class="icon-home"></i> <strong> Home</strong></a>
                            </li>
                            <li <?php if($this->usermodel->url_contains("calendar")) echo'class="active"'; ?> >
                                <a href="<?php echo base_url();?>page/calendar"><i class="icon-calendar"></i> <strong> Calendar</strong> View</a>
                            </li>
                            <li <?php if($this->usermodel->url_contains("recycle_bin")) echo'id="rectab" class="active"'; ?> >
                                <a href="<?php echo base_url();?>page/recycle_bin"><i class="icon-trash"></i> <strong> Recycle</strong> Bin</a>
                            </li>
                            <li <?php if($this->usermodel->url_contains("profile")) echo'class="active"';?>>
                                <a href="<?php echo base_url();?>page/profile"><i class="icon-user"></i> <strong> User</strong> Profile</a>
                            </li>
                            <li <?php if($this->usermodel->url_contains("about")) echo'class="active"';?>>
                                <a href="<?php echo base_url();?>page/about"><i class="icon-list"></i> <strong> About</strong> Page</a>
                            </li>
                            <li><a href="<?php echo base_url();?>page/logout" title="Goodbye"><i class="icon-share-alt"></i> <strong> Log</strong> Out</a></li>
                            <span id="logged" class="pull-right">Logged in as <strong><?php echo ucfirst($name);?></strong></span>
                        </ul>
                    <?php endif ?>
                </div>


<!-- MasterPage tags must be capitalized and rest lowercase. -->
<mp:Content />


<!-- footer -->
            </div>
            <div id="bottom">
                <p>
                  <div>
                    <a href="mailto:shazvi@outlook.com" target="_blank"><img src="<?php echo base_url();?>img/MAIL.png"></a>
                    <a href="https://www.facebook.com/shazvi.ahmed" target="_blank"><img src="<?php echo base_url();?>img/FaceBook-icon.png"></a>
                    <a href="https://twitter.com/cybertox544" target="_blank"><img src="<?php echo base_url();?>img/twitter_icon.png"></a>
                  </div>
                  Copyright &copy 2013-<?php echo date("Y");?> Shazvi Ahmed
              </p>
            </div>
        </div>
    </body>
</html>