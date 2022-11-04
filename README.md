# Instagram2Piwigo

* Internal name: `instagram2piwigo` (directory name in `plugins/`)
* Plugin page: http://piwigo.org/ext/extension_view.php?eid=664
* Translation: http://piwigo.org/translate/project.php?project=instagram2piwigo

## Developement status

Portage de l'extantion Instagram2Piwigo vers la nouvelle API Instagram "Basic Display" by Facebook
Seul le mode opératoire et les traductions en français a été mis à jour.

L'authentification instagram n'accepte pas d'autre paramètre que leur code d'authentification dans l'url de redirection.
En l'état, l'extension nécessite de mettre en place manuellement une RewriteRule dans le .htaccess pour renvoyer vers le plugins (cf. plus bas)

Le plugins détecte les photos déjà téléchargées avec cette nouvelle version du plugins basée sur les nouvelles API.
Le stock récupéré avec l'ancienne api est ignoré (nom de fichier, id, sum md5: tout est différent)
L'ajout d'une date dans le paramétrage permet de limiter le problème.


##   Rewriterule à ajouter dans le .htaccess
```
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{REQUEST_URI} ^/piwigo/instagram2piwigo-callback-url
	RewriteCond %{QUERY_STRING} code=(.*)
	RewriteRule (.*) "/piwigo/admin.php?page=plugin-instagram2piwigo&code=%1" [R,L]
</IfModule>
```