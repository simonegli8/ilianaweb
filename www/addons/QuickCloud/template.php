<!DOCTYPE html>
<html lang="de">

<head>

    <!-- Basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">   
   
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Site Icons -->
    <link rel="shortcut icon" href="themes/QuickCloud/images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="themes/QuickCloud/images/apple-touch-icon.png">

    <!-- Modernizer for Portfolio -->
    <script src="themes/QuickCloud/js/modernizer.js"></script>

    <!--[if lt IE 9]><?php
        // HTML5 shim, for IE6-8 support of HTML5 elements
        gpOutput::GetComponents( 'html5shiv' );
        gpOutput::GetComponents( 'respondjs' );
    ?><![endif]-->

    <?php gpOutput::GetHead(); ?>
</head>

<body>

	<!-- Start header -->
	<header class="top-navbar">
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<div class="container-fluid navbar-header">
                <div style="float:left">
                    <a class="navbar-brand" href=".">     
                        <?php gpOutput::GetImage( "images/logo.es.largo.paths.svg", array ('width' => '500px;') ); ?>
                    </a>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbars-host" aria-controls="navbars-rs-food" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                
                <div class="collapse navbar-collapse navbar-menu" id="navbars-host">
                    <div style="margin:auto;">
                        <?php
                        $GP_ARRANGE = false;
                        $GP_MENU_CLASSES = array(
                                        'menu_top'                      => 'navbar-nav ml-auto',
                                        'selected'                      => '',
                                        'selected_li'           => 'active',
                                        'childselected'         => '',
                                        'childselected_li'      => 'dropdown-item',
                                        'li_'                           => 'nav-item',
                                        'li_title'                      => '',
                                        'haschildren'           => 'dropdown-toggle',
                                        'haschildren_li'        => 'dropdown',
                                        'child_ul'                      => 'dropdown-menu',
                                        );

                        gpOutput::Get('Menu'); //top two levels
                        ?>
                    </div>
                </div>

                <div style="float:right;">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a class="hover-btn-new log" href="Admin" data-toggle="modal" data-target="#login"><img src="/themes/QuickCloud/images/admin.svg" alt="Admin" style="height:22px" /></a></li>
                    </ul>
                </div>

			</div>
		</nav>
	</header>
	<!-- End header -->

 	</div>

    <div id="content">
        <?php $page->GetContent(); ?>
    </div>

    <footer id="footer" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-xs-12">
                    <?php /* <div class="widget-title">
                        <h3>Sobre Nosostros</h3>
                    </div> */ ?>
                    <div class="widget clearfix">
                        <?php gpOutput::GetExtra('aboutus'); ?>
                    </div><!-- end clearfix -->
                </div><!-- end col -->

				<div class="col-lg-4 col-md-4 col-xs-12">
                    <div class="widget clearfix">
                        <?php gpOutput::GetExtra('infos'); ?>
                        <?php /* <div class="widget-title">
                            <h3>Informaciones</h3>
                        </div> */ ?>
                        <div class="widget clearfix">
                            <?php
                                $GP_MENU_CLASSES = array(
                                'menu_top'                      => 'footer-links',
                                'selected'                      => '',
                                'selected_li'           => 'active',
                                'childselected'         => '',
                                'childselected_li'      => 'dropdown-item',
                                'li_'                           => 'nav-item',
                                'li_title'                      => '',
                                'haschildren'           => 'dropdown-toggle',
                                'haschildren_li'        => 'dropdown',
                                'child_ul'                      => 'dropdown-menu',
                                );
            
                                $GP_MENU_CLASS = 'footer-links';
                                gpOutput::Get('Menu');
                            ?>
                        </div>
                        <!-- end links -->
                    </div><!-- end clearfix -->
                </div><!-- end col -->
				
                <div class="col-lg-4 col-md-4 col-xs-12">
                    <div class="widget clearfix">
                        <?php /* <div class="widget-title">
                            <h3>Contacto</h3>
                        </div> */ ?>
                        <?php gpOutput::GetExtra('contact'); ?>

                        <!-- end links -->
                    </div><!-- end clearfix -->
                </div><!-- end col -->
				
            </div><!-- end row -->
        </div><!-- end container -->
    </footer><!-- end footer -->

    <div class="copyrights">
        <div class="container">
            <div class="footer-distributed">
                <div class="footer-left">
                    <p class="footer-company-name">
                        Creada por <a href="http://www.estrellasdeesperanza.org" style="margin-right:100px;">Estrellas de Esperanza</a>
                        <?php gpOutput::GetAdminLink(); ?>
                    </p>
                </div>

                <div class="footer-right">
                    <?php gpOutput::GetExtra("sociallinks"); ?>
                   <?php /* <ul class="footer-links-soi">
						<li><a href="http://fb.me/estrellas.de.esperanza.ministerio"><i class="fa fa-facebook"></i></a></li>
                    </ul> */ ?>
                </div>
            </div>
        </div><!-- end container -->
    </div><!-- end copyrights -->

    <a href="#" id="scroll-to-top" class="dmtop global-radius"><i class="fa fa-angle-up"></i></a>

    <!-- ALL JS FILES -->
    <script src="themes/QuickCloud/js/all.js"></script>
    <!-- ALL PLUGINS -->
    <script src="themes/QuickCloud/js/custom.js"></script>
	<script src="themes/QuickCloud/js/timeline.min.js"></script>
	<script>
		timeline(document.querySelectorAll('.timeline'), {
			forceVerticalMode: 700,
			mode: 'horizontal',
			verticalStartPosition: 'left',
			visibleItems: 4
		});
	</script>

</body>
</html>
