# Instagram2Piwigo

* Internal name: `instagram2piwigo` (directory name in `plugins/`)
* Plugin page: http://piwigo.org/ext/extension_view.php?eid=664
* Translation: http://piwigo.org/translate/project.php?project=instagram2piwigo

## Developement status

Tentative de portage vers la nouvelle API Instagram "Basic Display" by Facebook
"V0" Globalement fonctionnelle! 
Seul le mode opératoire et les traductions en français a été mis à jour.

L'authentification instagram n'accepte pas d'autre paramètre que le code d'authentification dans la redirect_uri.
Pas réussi à faire marcher le retour d'authentification facebook sans mettre une RewriteRule dans le .htaccess pour renvoyer vers le plugins




##   En l'état une règle htaccess est nécessaire
```
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{REQUEST_URI} ^/piwigo/instagram2piwigo-callback-url
	RewriteCond %{QUERY_STRING} code=(.*)
	RewriteRule (.*) "/piwigo/admin.php?page=plugin-instagram2piwigo&code=%1" [R,L]
</IfModule>
```