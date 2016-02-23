<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset ($site_title)?$site_title.' | '.$this->config->item('site_title'):$this->config->item('site_title'); ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="<?php echo $this->config->item('meta_desc');?>" name="description" />
    <meta content="<?php echo $this->config->item('meta_key');?>" name="keywords" />
    <meta content="<?php echo $this->config->item('meta_author');?>" name="author" />

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url();?>assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo base_url();?>assets/font-awesome/css/font-awesome.min.css?v=<?php echo time()?>" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/bootstrap-switch.min.css?v=3.3.2" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/dataTables.bootstrap.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/app.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url();?>">WS Client</a>
        </div>
    
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <?php
                if ($this->session->userdata('login')) {
                    echo "<ul class=\"nav navbar-nav\">
                                <li class=\"dropdown\">
                                  <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\"><i class=\"fa fa-clone\"></i> Import Data <span class=\"caret\"></span></a>
                                  <ul class=\"dropdown-menu\" role=\"menu\">
                                    <li><a href=\"".base_url()."index.php/mahasiswa\">Mahasiswa</a></li>
                                    <li><a href=\"".base_url()."index.php/ws_nilai\">Nilai Semester Mahasiswa</a></li>
                                    <li><a href=\"".base_url()."index.php/ws_akm\">Aktivitas Kuliah Mahasiswa</a></li>
                                  </ul>
                                </li>
                                <li class=\"dropdown\">
                                  <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\"><i class=\"fa fa-th\"></i> Data Referensi <span class=\"caret\"></span></a>
                                  <ul class=\"dropdown-menu\" role=\"menu\">
                                    <li><a href=\"".base_url()."index.php/ref_agama\">Data Agama</a></li>
                                    <li><a href=\"".base_url()."index.php/kk\">Data Kebutuhan Khusus</a></li>
                                    <li><a href=\"".base_url()."index.php/ref_pekerjaan\">Data Pekerjaan</a></li>
                                    <li><a href=\"".base_url()."index.php/ref_penghasilan\">Data Penghasilan</a></li>
                                    <li><a href=\"".base_url()."index.php/ref_status\">Data Status Mahasiswa</a></li>
                                    <li><a href=\"".base_url()."index.php/ref_wilayah\">Data Wilayah</a></li>
                                    <!--li class=\"divider\"></li>
                                    <li><a href=\"#\">Separated link</a></li>
                                    <li class=\"divider\"></li>
                                    <li><a href=\"#\">One more separated link</a></li-->
                                  </ul>
                                </li>
                                <li class=\"dropdown\">
                                  <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\"><span class=\"glyphicon glyphicon-refresh\" aria-hidden=\"true\"></span> Epsbed <span class=\"caret\"></span></a>
                                  <ul class=\"dropdown-menu\" role=\"menu\">
                                    <li><a href=\"".base_url()."index.php/epsbed_mahasiswa\">Mahasiswa</a></li>
                                    <li><a href=\"#\"-->Kelas Perkuliahan</a></li>
                                    <li><a href=\"#\">Mahasiswa Lulus / Drop Out</a></li>
                                    <!--li class=\"divider\"></li>
                                    <li><a href=\"#\">Separated link</a></li>
                                    <li class=\"divider\"></li>
                                    <li><a href=\"#\">One more separated link</a></li-->
                                  </ul>
                                </li>
                                <li><a href=\"".base_url()."index.php/welcome/table\"><i class=\"fa fa-database\"></i> List Tabel</a></li>
                                <!--li><a href=\"#\">Tabel Data</a></li-->
                      </ul>";
                }
            ?>
          
          <ul class="nav navbar-nav navbar-right">
            <?php
                if ($this->session->userdata('login')) {
                    echo "<li class=\"white\">
                              <a href=\"".base_url()."index.php/welcome/update\">
                                  <i class=\"fa fa-bell-o fa-refresh\"></i> <span class=\"badge\" id=\"notif\">0</span>
                              </a>
                              <!--ul class=\"dropdown-menu\" role=\"menu\" id=\"text\">
                                  <li><a href=\"#\"><i class=\"fa fa-info\"></i> Updatean tanggal 10-Sept-2015</a></li>
                              </ul-->
                          </li>
                          <li class=\"dropdown active\"><a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">
                                    <i class=\"fa fa-user\"></i>  ".$this->session->userdata('username')." <span class=\"caret\"></span>
                              </a>
                              <ul class=\"dropdown-menu\" role=\"menu\">
                                <li><a href=\"".base_url()."index.php/welcome/token/".$this->uri->segment(1)."-".$this->uri->segment(2)."\"><i class=\"fa fa-random\"></i>  Generate Token</a></li>
                                <li><a href=\"".base_url()."index.php/welcome/setting\"><i class=\"fa fa-cog\"></i> Setting</a></li>
                                <li class=\"divider\"></li>
                                <li><a href=\"".base_url()."index.php/welcome/logout\"><i class=\"fa fa-sign-out\"></i> Logout</a></li>
                              </ul>
                          </li>";
                } else {
                    echo "<li><a href=\"".base_url()."index.php/ws\">Login</a></li>";
                }
            ?>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

    <div class="container-fluid ws-container">
        <?php echo $view; ?>
    </div> <!-- /container -->

    <footer class="footer">
      <div class="container copy">
        WS CLient &copy; <?php echo date('Y')." <a href=\"http://wsfeeder.jago.link\" target=\"_blank\">".$this->config->item('meta_author');?> 
      </div>
    </footer>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script>var top_url = '<?php echo base_url();?>'; </script>
    <script src="<?php echo base_url();?>assets/js/jquery.js?v=<?php echo time()?>"></script>
    <script src="<?php echo base_url();?>assets/js/bootstrap.min.js?v=<?php echo time()?>"></script>
    <script src="<?php echo base_url();?>assets/js/bootstrap-switch.min.js?v=3.3.2"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/dataTables.bootstrap.js"></script>
    <script src="<?php echo base_url();?>assets/js/back-to-top.js"></script>
    <script src="<?php echo base_url();?>assets/js/app.js"></script>
    <?php
        //echo $assign_js; 
        if ($assign_js != '') {
            $this->load->view($assign_js);
        }

        if ($assign_modal != '') {
            $this->load->view($assign_modal);
        }
    ?>
  </body>
</html>
