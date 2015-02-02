# Setup Multitenancy extension

Cette extension permet de transformer votre application CodeIgniter pour l'utiliser dans un contexte Multi-tenant (Multitenancy)

## Install

Nous supposons que votre application Code Igniter se trouver dans /path/to/code-igniter-app

Copier les fichiers dans l'application :

> $ cd /path/to/cromatic-saas-extension
> $ cp -Rf ./* /path/to/code-igniter-app

Dans /path/to/application, éditer le fichier application/core/XYZ_Loader.php et remplacer :

	<?php
	
	class XYZ_Loader

par :

	<?php
	
	class CROC_Loader

si *CROC_* est le préfixe de votre application et paramétré ainsi dans application/config/config.php :

	$config['subclass_prefix'] = 'CROC_';

## Configuration

Les différents applications sont à définir dans applications/config/saas_app.php

Chaque configuration d'application héritera de la configuration par défaut se trouvant dans applications/config/database.php

## Informations techniques

Le préfixe utilisé pour cette extension est : saas_app

