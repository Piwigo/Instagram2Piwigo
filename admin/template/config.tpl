<div class="titrePage">
	<h2>Instagram2Piwigo</h2>
</div>

<form method="post" action="" class="properties">
<fieldset>
  <legend>{'Instagram logins'|translate}</legend>
  
  <ul>
    <li>
      <label>
        <span class="property">Client ID</span>
        <input type="text" name="api_key" value="{$Instagram2Piwigo.api_key}" size="40">
      </label>
    </li>

    <li>
      <label>
        <span class="property">Client Secret</span>
        <input type="text" name="secret_key" value="{$Instagram2Piwigo.secret_key}" size="40">
      </label>
    </li>
  </ul>
</fieldset>

<p><input type="submit" name="save_config" value="{'Save Settings'|translate}"></p>

<fieldset>
  <legend>{'How do I get my Instagram Client ID ?'|translate}</legend>
  
  <p><b>OAuth redirect_uri :</b> <span style="font-family:monospace;font-size:14px;">{$INSTAG_CALLBACK}</span></p>
  {$INSTAG_HELP_CONTENT}
</fieldset>
  
</form>