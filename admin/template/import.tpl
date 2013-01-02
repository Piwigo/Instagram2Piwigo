<div class="titrePage">
	<h2>Instagram2Piwigo</h2>
</div>

{* <!-- LOGIN --> *}
{if $ACTION == 'init_login'}
<p><input type="submit" onClick="javascript:window.location.href ='{$instagram_login}';" value="{'Login'|@translate}"></p>

{* <!-- MAIN MENU --> *}
{elseif $ACTION == 'main'}
{footer_script}{literal}
jQuery('input[type="submit"]').click(function() {
  window.location.href = $(this).attr("data");
});
jQuery('.load').click(function() {
  $("#loader_import").fadeIn();
});
{/literal}{/footer_script}

<p>
  <b>{'Logged in as'|@translate}</b> : <a href="{$profile_url}" target="_blank">{$username}</a><br><br>
  <input type="submit" data="{$logout_url}" value="{'Logout'|@translate}">
</p>
<br>
<p>
  <input type="submit" data="{$list_photos_url}" class="load" value="{'List my pictures'|@translate}">
  <br>
  <span id="loader_import" style="display:none;"><img src="admin/themes/default/images/ajax-loader.gif"> <i>{'Processing...'|@translate}</i></span>
</p>

{* <!-- PHOTOS LIST --> *}
{elseif $ACTION == 'list_photos'}
{include file=$INSTAG_ABS_PATH|@cat:'admin/template/import.list_photos.tpl'}

{/if}