<?php

/**
 *
 * Default view
 *
 * @version             1.0.0
 * @package             Gavern Framework
 * @copyright			Copyright (C) 2010 - 2011 GavickPro. All rights reserved.
 *               
 */
 
// No direct access.
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$user = JFactory::getUser();

// getting User ID
$userID = $user->get('id');

// getting params
$option = JRequest::getCmd('option', '');
$view = JRequest::getCmd('view', '');
// defines if com_users
define('GK_COM_USERS', $option == 'com_users' && ($view == 'login' || $view == 'registration'));
// other variables
$btn_login_text = ($userID == 0) ? JText::_('TPL_GK_LANG_LOGIN') : JText::_('TPL_GK_LANG_LOGOUT');

if($this->browser->get('browser') == 'ie8') {
	$this->page_suffix .= ' ie8mode';
}

$tpl_page_suffix = $this->page_suffix != '' ? ' class="'.$this->page_suffix.'"' : '';
// make sure that the modal will be loaded
JHTML::_('behavior.modal');

?>
<!DOCTYPE html>
<html lang="<?php echo $this->APITPL->language; ?>">
<head>
<?php $this->layout->addTouchIcon(); ?>
<?php if(
			$this->browser->get('browser') == 'ie6' || 
			$this->browser->get('browser') == 'ie7' || 
			$this->browser->get('browser') == 'ie8' || 
			$this->browser->get('browser') == 'ie9'
		) : ?>
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
<?php endif; ?>
<?php if($this->API->get('rwd', 1)) : ?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2.0">
<?php else : ?>
<meta name="viewport" content="width=<?php echo $this->API->get('template_width', 1020)+80; ?>">
<?php endif; ?>
<jdoc:include type="head" />
<?php $this->layout->loadBlock('head'); ?>
<?php $this->layout->loadBlock('cookielaw'); ?>
</head>
<body<?php echo $tpl_page_suffix; ?><?php if($this->browser->get("tablet") == true) echo ' data-tablet="true"'; ?><?php if($this->browser->get("mobile") == true) echo ' data-mobile="true"'; ?><?php $this->layout->generateLayoutWidths(); ?>>
	<div id="gkBg">	
		<?php if ($this->browser->get('browser') == 'ie7' || $this->browser->get('browser') == 'ie6') : ?>
		<!--[if lte IE 7]>
		<div id="ieToolbar"><div><?php echo JText::_('TPL_GK_LANG_IE_TOOLBAR'); ?></div></div>
		<![endif]-->
		<?php endif; ?>
		
		<div id="gkContentWrapper" class="gkPage">
	        <?php if(count($app->getMessageQueue())) : ?>
	        <jdoc:include type="message" />
	        <?php endif; ?>
	        
	        <div id="gkHeader">
	            <div id="gkTop">
				<?php if(($this->API->get('reg_link') == '1' && $userID == 0) || $this->API->modules('login')) : ?>
	                <div id="gkUserArea">
	                 	<?php if($this->API->modules('login')) : ?>
							<a href="<?php echo $this->API->get('login_url', 'index.php?option=com_users&view=login'); ?>" id="gkLogin"><?php if($userID == 0) echo JText::_('TPL_GK_LANG_LOGIN'); ?></a>
	                 	<?php endif; ?>
	                 			
	                 	<?php $usersConfig = JComponentHelper::getParams('com_users'); ?>
	                 	<?php if ($usersConfig->get('allowUserRegistration') && $userID == 0) : ?>
	                 		<a href="<?php echo JRoute::_('index.php?option=com_comprofiler&view=registers'); ?>" id="gkRegister"> <?php echo JText::_('TPL_GK_LANG_REGISTER'); ?></a>
	                 	<?php elseif($userID != 0) :?>
                            <jdoc:include type="modules" name="login" style="<?php echo $this->module_styles['login']; ?>" />
                        <?php endif; ?>
	                </div>
	            <?php endif; ?>
                         
	                 <?php $this->layout->loadBlock('logo'); ?>
	                 
	                 <?php if($this->API->get('menu_type', 'overlay') == 'classic') : ?>
	                    <?php if($this->API->get('show_menu', 1)) : ?>
	                    <div id="gkMobileMenu" class="gkPage"> <i id="mobile-menu-toggler" class="icon-reorder"></i>
	                        <select onChange="window.location.href=this.value;">
	                            <?php 
	                 		    	$this->mobilemenu->loadMenu($this->API->get('menu_name','mainmenu')); 
	                 		    	$this->mobilemenu->genMenu($this->API->get('startlevel', 0), $this->API->get('endlevel',-1));
	                 			?>
	                        </select>
	                    </div>
	                    <?php endif; ?>
	                 <?php endif; ?> 
	                 
	                 <?php if($this->API->modules('search')) : ?>
	                 <div id="gkSearch">
	                 	<jdoc:include type="modules" name="search" style="<?php echo $this->module_styles['search']; ?>" />
	                 </div>
	                 <?php endif; ?>
	                  
	                  <?php if($this->API->get('show_menu', 1)) : ?>
	                  <div id="gkMainMenu"<?php echo $this->API->get('menu_type', 'overlay') == 'overlay' ? ' class="gkMenuOverlay"' : ' class="gkMenuClassic"'; ?>>
	                          <?php
	                  		$this->mainmenu->loadMenu($this->API->get('menu_name','mainmenu')); 
	                  	    $this->mainmenu->genMenu($this->API->get('startlevel', 0), $this->API->get('endlevel',-1));
	                  	?>
	                  </div>
	                  <?php endif; ?>
	             </div>
	             
	             <?php if($this->API->modules('header')) : ?>
	             <jdoc:include type="modules" name="header" style="<?php echo $this->module_styles['header']; ?>" />
	             <?php endif; ?>
	        </div>
	        
	        <?php if($this->API->modules('header_bottom')) : ?>
	        <div id="gkHeaderBottom">
	             <div class="gkCols3">   
	                <jdoc:include type="modules" name="header_bottom" style="<?php echo $this->module_styles['header_bottom']; ?>"  modnum="<?php echo $this->API->modules('header_bottom'); ?>" modcol="3" />
	             </div>
	        </div>
	        <?php endif; ?>
	        
	        <?php if($this->API->modules('breadcrumb')) : ?>
	        <div id="gkBreadcrumb">
                <div class="gkPage">
                        <?php if($this->API->modules('breadcrumb')) : ?>
                        <jdoc:include type="modules" name="breadcrumb" style="<?php echo $this->module_styles['breadcrumb']; ?>" />
                        <?php endif; ?>
                </div>
	        </div>
	        <?php endif; ?>
	        
	        <div id="gkPageContentWrap"<?php if($this->API->modules('bottom5 or bottom6')) : ?> class="gkFooterBorder"<?php endif; ?>>
	            <?php if(JURI::current() !== JURI::base()){ ?>
				<div id="gkPageContent" class="gkPage">
	                <section id="gkContent"<?php if($this->API->get('sidebar_position', 'right') == 'left') : ?> class="gkColumnLeft"<?php endif; ?>>
	                    <?php if($this->API->modules('top1')) : ?>
	                    <div id="gkTop1" class="gkCols3<?php if($this->API->modules('top1') == 1) : ?> gkNoMargin<?php endif; ?>">
	                            <jdoc:include type="modules" name="top1" style="<?php echo $this->module_styles['top1']; ?>"  modnum="<?php echo $this->API->modules('top1'); ?>" modcol="3" />
	                    </div>
	                    <?php endif; ?>
	                    
	                    <?php if($this->API->modules('top2')) : ?>
	                    <div id="gkTop2" class="gkCols3<?php if($this->API->modules('top2') == 1) : ?> gkNoMargin<?php endif; ?>">
	                            <jdoc:include type="modules" name="top2" style="<?php echo $this->module_styles['top2']; ?>" modnum="<?php echo $this->API->modules('top2'); ?>" modcol="3" />
	                    </div>
	                    <?php endif; ?>
	                    
	                    <?php if($this->API->modules('mainbody_top')) : ?>
	                    <div id="gkMainbodyTop">
	                            <jdoc:include type="modules" name="mainbody_top" style="<?php echo $this->module_styles['mainbody_top']; ?>" />
	                    </div>
	                    <?php endif; ?>
	                    
	                    <div id="gkMainbody">
	                            <?php if(($this->layout->isFrontpage() && !$this->API->modules('mainbody')) || !$this->layout->isFrontpage()) : ?>
	                            <jdoc:include type="component" />
	                            <?php else : ?>
	                            <jdoc:include type="modules" name="mainbody" style="<?php echo $this->module_styles['mainbody']; ?>" />
	                            <?php endif; ?>
	                    </div>
	                    
	                    <?php if($this->API->modules('mainbody_bottom')) : ?>
	                    <div id="gkMainbodyBottom">
	                            <jdoc:include type="modules" name="mainbody_bottom" style="<?php echo $this->module_styles['mainbody_bottom']; ?>" />
	                    </div>
	                    <?php endif; ?>
	                </section>
	                
	                <?php if($this->API->modules('sidebar')) : ?>
	                <aside id="gkSidebar"<?php if($this->API->modules('sidebar') == 1) : ?> class="gkOnlyOne"<?php endif; ?>>
	                        <jdoc:include type="modules" name="sidebar" style="<?php echo $this->module_styles['sidebar']; ?>" />
	                </aside>
	                <?php endif; ?>
	            </div>
				<?php } ?>
	            
	            <?php if($this->API->modules('bottom1')) : ?>
	            <div id="gkBottom1"<?php if($this->API->modules('bottom1') == 1) : ?> class="gkSingleModule"<?php endif; ?>>
                    <div class="gkCols6">
                        <jdoc:include type="modules" name="bottom1" style="<?php echo $this->module_styles['bottom1']; ?>" modnum="<?php echo $this->API->modules('bottom1'); ?>" />
                	</div>
	            </div>
	            <?php endif; ?>
	            
	            <?php if($this->API->modules('bottom2')) : ?>
	            <div id="gkBottom2"<?php if($this->API->modules('bottom2') == 1) : ?> class="gkSingleModule"<?php endif; ?>>
                    <div class="gkCols6">
                        <jdoc:include type="modules" name="bottom2" style="<?php echo $this->module_styles['bottom2']; ?>" modnum="<?php echo $this->API->modules('bottom2'); ?>" />
                    </div>
	            </div>
	            <?php endif; ?>
	            
	            <?php if($this->API->modules('bottom3')) : ?>
	            <div id="gkBottom3"<?php if($this->API->modules('bottom3') == 1) : ?> class="gkSingleModule"<?php endif; ?>>
	                <div class="gkCols6">
	                    <jdoc:include type="modules" name="bottom3" style="<?php echo $this->module_styles['bottom3']; ?>" modnum="<?php echo $this->API->modules('bottom3'); ?>" />
	                </div>
	            </div>
	            <?php endif; ?>
	            
	            <?php if($this->API->modules('bottom4')) : ?>
	            <div id="gkBottom4"<?php if($this->API->modules('bottom4') == 1) : ?> class="gkSingleModule"<?php endif; ?>>
	                <div class="gkCols6">
	                    <jdoc:include type="modules" name="bottom4" style="<?php echo $this->module_styles['bottom4']; ?>" modnum="<?php echo $this->API->modules('bottom4'); ?>" />
	                </div>
	            </div>
	            <?php endif; ?>
	        </div>
	        
	        <?php $this->layout->loadBlock('footer'); ?>
	        
	        <?php if($this->API->modules('lang')) : ?>
	        <div id="gkLang" class="gkPage">
	                <jdoc:include type="modules" name="lang" style="<?php echo $this->module_styles['lang']; ?>" />
	        </div>
	        <?php endif; ?>
		</div>
	</div>
	
	<?php $this->layout->loadBlock('social'); ?>
	<?php $this->layout->loadBlock('tools/login'); ?>
	<div id="gkPopupOverlay"></div>
	<jdoc:include type="modules" name="debug" />
	<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter31551268 = new Ya.Metrika({
                    id:31551268,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/31551268" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-65972883-1', 'auto');
  ga('send', 'pageview');

</script>

<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '228621327472974',
      xfbml      : true,
      version    : 'v2.5'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
</body>
</html>