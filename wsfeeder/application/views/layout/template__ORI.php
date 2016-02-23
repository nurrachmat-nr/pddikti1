<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="<?php echo $this->config->item('meta_desc');?>" name="description" />
    <meta content="<?php echo $this->config->item('meta_key');?>" name="keywords" />
    <meta content="<?php echo $this->config->item('meta_author');?>" name="author" />
    <title><?php echo isset ($site_title)?$site_title.' | '.$this->config->item('site_title'):$this->config->item('site_title'); ?></title>
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/metisMenu.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url();?>assets/css/bootstrap-switch.min.css?v=3.3.2" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/app.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/dataTables.bootstrap.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <!-- top menu-->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo base_url();?>">Feeder Importir</a>
            </div>

            <!-- menu kanan-->
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  344067 <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> Generate Token</a></li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="<?php echo base_url(); ?>index.php/welcome/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- end menu kanan-->
            <!-- end top menu-->

            <!-- menu kiri-->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="<?php echo base_url();?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li class="active">
                            <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Import Data<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo base_url();?>index.php/mahasiswa">Mahasiswa</a>
                                </li>
                                <li>
                                    <a href="morris.html">Kelas Perkuliahan</a>
                                </li>
                            </ul>
                        </li>
                        <li class="active">
                            <a href="#"><i class="fa fa-wrench fa-fw"></i> Data Referensi<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="panels-wells.html">Data Agama</a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url();?>index.php/kk">Data Kebutuhan Khusus</a>
                                </li>
                                <li>
                                    <a href="notifications.html">Data Wilayah</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="<?php echo base_url();?>index.php/welcome/table"><i class="fa fa-database fa-fw"></i> List Table</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- end menu kiri-->
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper">
            <?php echo $view; ?>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url();?>assets/js/metisMenu.min.js"></script>
    <script src="<?php echo base_url();?>assets/js/sb-admin-2.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/dataTables.bootstrap.js"></script>
    <?php
        //echo $assign_js; 
        if ($assign_js != '') {
            $this->load->view($assign_js);
        }
    ?>

</body>

</html>
