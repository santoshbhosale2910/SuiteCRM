<?php /* Smarty version 2.6.31, created on 2018-07-25 19:42:33
         compiled from custom/modules/AOS_Quotes/templates/showPopupWithTemplates.tpl */ ?>
<div id="popupDiv_ara"
     style="display:none;position:fixed;top: 39%; left: 41%;opacity:1;z-index:9999;background:#FFFFFF;">
    <form id="popupForm" action="index.php?entryPoint=generateQuotePdf" method="post">
        <table style="border: #000 solid 2px; padding-left:40px; padding-right:40px; padding-top:10px; padding-bottom:10px; font-size:110%;">
            <tr height="20">
                <td colspan="2">
                    <b><?php echo $this->_tpl_vars['APP']['LBL_SELECT_TEMPLATE']; ?>
:-</b>
                </td>
            </tr>
            <?php $_from = $this->_tpl_vars['TEMPLATES']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['template'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['template']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['templateKey'] => $this->_tpl_vars['template']):
        $this->_foreach['template']['iteration']++;
?>
                <?php if (empty ( $this->_tpl_vars['template'] ) == false): ?>
                    <?php ob_start(); ?>
                        document.getElementById('popupDivBack_ara').style.display='none';document.getElementById('popupDiv_ara').style.display='none';var form=document.getElementById('popupForm');if(form!=null){form.templateID.value='<?php echo $this->_tpl_vars['template']; ?>
';form.submit();}else{alert('Error!');}
                    <?php $this->_smarty_vars['capture']['on_click_js'] = ob_get_contents();  $this->assign('on_click_js', ob_get_contents());ob_end_clean(); ?>
                    <tr height="20">
                        <td width="17" valign="center"><a href="#" onclick="<?php echo $this->_tpl_vars['on_click_js']; ?>
">
                            <a href="#" onclick="<?php echo $this->_tpl_vars['on_click_js']; ?>
">
                                <img src="themes/default/images/txt_image_inline.gif" width="16" height="16"/>
                            </a>
                        </td>
                        <td>
                            <a href="#" onclick="<?php echo $this->_tpl_vars['on_click_js']; ?>
">
                                <b><?php echo $this->_tpl_vars['APP_LIST_STRINGS']['template_ddown_c_list'][$this->_tpl_vars['template']]; ?>
</b>
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>
            <input type="hidden" name="templateID" value=""/>
            <input type="hidden" name="task" value="pdf"/>
            <input type="hidden" name="module" value="<?php echo $this->_tpl_vars['FOCUS']->module_name; ?>
"/>
            <input type="hidden" name="uid" value="<?php echo $this->_tpl_vars['FOCUS']->id; ?>
"/>
    </form>
    <tr style="height:10px;">
    </tr>
    <tr>
        <td colspan="2">
            <button style=" display: block;margin-left: auto;margin-right: auto" onclick="document.getElementById('popupDivBack_ara').style.display='none';document.getElementById('popupDiv_ara').style.display='none';return false;">
                Cancel
            </button>
        </td>
    </tr>
    </table>
</div>
<div id="popupDivBack_ara" onclick="this.style.display='none';document.getElementById('popupDiv_ara').style.display='none';" style="top:0px;left:0px;position:fixed;height:100%;width:100%;background:#000000;opacity:0.5;display:none;vertical-align:middle;text-align:center;z-index:9998;">
</div>
<script>
  <?php echo '
  /**
   *
   * @param task
   * @return {boolean}
   * @see generatePdf (entrypoint)
   */
  '; ?>

  function showPopup(task) {
    var form = document.getElementById('popupForm');
    var ppd = document.getElementById('popupDivBack_ara');
    var ppd2 = document.getElementById('popupDiv_ara');
    var totalTemplates = <?php echo $this->_tpl_vars['TOTAL_TEMPLATES']; ?>

    if (totalTemplates === 1) {
      form.task.value = task;
      form.templateID.value = '<?php echo $this->_tpl_vars['template']; ?>
';
      form.submit();
    } else if (form !== null && ppd !== null && ppd2 !== null) {
      ppd.style.display ='block';
      ppd2.style.display ='block';
      form.task.value = task;
    } else {
      alert('Error!');
    }
    return false;
  }
</script>