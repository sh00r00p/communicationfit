<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/
defined('_JEXEC') or die();
?>

<div class="cGuest">
          <h1><?php echo JText::_('COM_COMMUNITY_GET_CONNECTED_TITLE'); ?></h1>
          <p><?php echo JText::_('COM_COMMUNITY_HERO_PARAGRAPH'); ?></p>
          <?php if ($allowUserRegister) : ?>
          <a href="<?php echo CRoute::_( 'index.php?option=com_community&view=register' , false ); ?>"> <?php echo JText::_('COM_COMMUNITY_JOIN_US_NOW'); ?> </a>
          <?php endif; ?>
</div>
<div class="login-area">
          <form action="<?php echo CRoute::getURI();?>" method="post" name="login" id="form-login" >
                    <input type="text" name="username" id="username" tabindex="1" placeholder="<?php echo JText::_('COM_COMMUNITY_USERNAME'); ?>" />
                    <input type="password" name="<?php echo COM_USER_PASSWORD_INPUT;?>" id="password"  tabindex="2" placeholder="<?php echo JText::_('COM_COMMUNITY_PASSWORD'); ?>" />
                    <input type="submit" value="<?php echo JText::_('COM_COMMUNITY_LOGIN_BUTTON');?>" name="submit" id="submit" class="button"  tabindex="3" />
                      <?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
                              <label for="remember" class="checkbox inline">
                                        <input type="checkbox" alt="<?php echo JText::_('COM_COMMUNITY_REMEMBER_MY_DETAILS'); ?>" value="yes" id="remember" name="remember"  tabindex="4" />
                                        <?php echo JText::_('COM_COMMUNITY_REMEMBER_MY_DETAILS'); ?> </label>
                              <?php endif; ?>
                    <div>
                            
                              <a class="reminder-link" href="<?php echo CRoute::_( 'index.php?option='.COM_USER_NAME.'&view=remind' ); ?>" tabindex="5"> <?php echo JText::_('COM_COMMUNITY_FORGOT_USERNAME_LOGIN'); ?></a> <a class="reminder-link" href="<?php echo CRoute::_( 'index.php?option='.COM_USER_NAME.'&view=reset' ); ?>" tabindex="6"> <?php echo JText::_('COM_COMMUNITY_FORGOT_PASSWORD_LOGIN'); ?> </a>
                              <?php if ($useractivation) { ?>
                              <a class="reminder-link" href="<?php echo CRoute::_( 'index.php?option=com_community&view=register&task=activation' ); ?>" class="login-forgot-username"> <span><?php echo JText::_('COM_COMMUNITY_RESEND_ACTIVATION_CODE'); ?></span> </a>
                              <?php } ?>
                    </div>
                    <input type="hidden" name="option" value="<?php echo COM_USER_NAME;?>" />
                    <input type="hidden" name="task" value="<?php echo COM_USER_TAKS_LOGIN;?>" />
                    <input type="hidden" name="return" value="<?php echo $return; ?>" />
                    <?php echo JHTML::_( 'form.token' ); ?>
          </form>
          <?php echo $fbHtml;?> </div>
