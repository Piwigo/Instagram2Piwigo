# Instagram2Piwigo

* Internal name: `instagram2piwigo` (directory name in `plugins/`)
* Plugin page: http://piwigo.org/ext/extension_view.php?eid=664
* Translation: http://piwigo.org/translate/project.php?project=instagram2piwigo

## Developement status

Tentative de portage vers la nouvelle API Instagram "Basic Display" by Facebook
pas réussi à faire marcher le retour d'authentification facebook sans mettre une RewriteRule dans le .htaccess


"V0" Globalement fonctionnelle! 
Seul le mode opératoire et les traductions en français a été mis à jour.


##   En l'état nécessite une règle htaccess
```
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{REQUEST_URI} ^/piwigo/instagram2piwigo-import
	RewriteCond %{QUERY_STRING} code=(.*)
	RewriteRule (.*) "/piwigo/admin.php?page=plugin-instagram2piwigo&code=%1" [R,L]
</IfModule>
```