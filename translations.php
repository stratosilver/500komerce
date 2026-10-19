<?php
/**
 * Texts of the interface, in French and in English.
 *
 * Each text is a constant whose name starts with T_, used in the views and the controllers:
 *     <?=T_SAVE?>                         a text
 *     sprintf(T_ITEMS, $count)            a text with values: %s = a text, %d = a number, %% = the sign %
 * PHP already has constants named T_... (extension tokenizer: T_NEW, T_CONTINUE, T_ECHO, T_STRING...):
 * a new name must not be one of them, a warning is displayed if it is.
 *
 * Choice of the language
 *   - a link with ?lang=fr or ?lang=en (the switch FR | EN of the header): the choice is kept in the session
 *     and in the cookie "lang" (1 year)
 *   - otherwise the language of the session, then the one of the cookie, then LANG_DEFAULT
 *   LANGUAGES and LANG_DEFAULT can be defined in config.php to replace the values below.
 *
 * Add a text: one line in $translations, with the same keys (fr, en) for all the languages.
 * Add a language (ex: Dutch): add 'nl' => 'Nederlands' to LANGUAGES and 'nl' => '...' to each line;
 * a text without translation is displayed in LANG_DEFAULT.
 *
 * This file is loaded by index.php and api.php, after config.php and before the classes.
 */

// Languages of the interface: code => name, the first one is the default one
if(!defined('LANGUAGES')){
    define('LANGUAGES', array('fr' => 'Français', 'en' => 'English'));
}
if(!defined('LANG_DEFAULT')){
    define('LANG_DEFAULT', array_key_first(LANGUAGES));
}


// Language of the request
// ------------------------------------------------------------------------------------------------
$lang = LANG_DEFAULT;
if(isset($_GET['lang']) && is_string($_GET['lang']) && isset(LANGUAGES[$_GET['lang']])){
    $lang = $_GET['lang'];
    if(session_status() === PHP_SESSION_ACTIVE){
        $_SESSION['lang'] = $lang;
    }
    if(!headers_sent()){
        setcookie('lang', $lang, array(
            'expires' => time() + 365 * 24 * 3600,
            'path' => (defined('BASE_URL') && BASE_URL != '') ? BASE_URL : '/',
            'samesite' => 'Lax',
        ));
    }
}
elseif(isset($_SESSION['lang']) && is_string($_SESSION['lang']) && isset(LANGUAGES[$_SESSION['lang']])){
    $lang = $_SESSION['lang'];
}
elseif(isset($_COOKIE['lang']) && is_string($_COOKIE['lang']) && isset(LANGUAGES[$_COOKIE['lang']])){
    $lang = $_COOKIE['lang'];
}
define('LANG', $lang);


// Texts
// ------------------------------------------------------------------------------------------------
$translations = array(

    // Language
    'T_LANGUAGE'                  => array('fr' => 'Langue', 'en' => 'Language'),

    // Actions and common words
    'T_SAVE'                      => array('fr' => 'Enregistrer', 'en' => 'Save'),
    'T_ADD'                       => array('fr' => 'Ajouter', 'en' => 'Add'),
    'T_NEW_ENTRY'                 => array('fr' => 'Nouveau', 'en' => 'New'),
    'T_VIEW'                      => array('fr' => 'Voir', 'en' => 'View'),
    'T_EDIT'                      => array('fr' => 'Modifier', 'en' => 'Edit'),
    'T_DELETE'                    => array('fr' => 'Supprimer', 'en' => 'Delete'),
    'T_RESTORE'                   => array('fr' => 'Restaurer', 'en' => 'Restore'),
    'T_TRASH'                     => array('fr' => 'Corbeille', 'en' => 'Trash'),
    'T_SEARCH'                    => array('fr' => 'Rechercher', 'en' => 'Search'),
    'T_RESET'                     => array('fr' => 'Réinitialiser', 'en' => 'Reset'),
    'T_NEXT_STEP'                 => array('fr' => 'Continuer', 'en' => 'Continue'),
    'T_YES'                       => array('fr' => 'Oui', 'en' => 'Yes'),
    'T_OR'                        => array('fr' => 'ou', 'en' => 'or'),
    'T_CONFIRM_DELETE'            => array('fr' => 'Êtes-vous sûr ?', 'en' => 'Are you sure?'),
    'T_NO_DATA'                   => array('fr' => 'Aucune donnée', 'en' => 'No data'),
    'T_MENU'                      => array('fr' => 'Menu', 'en' => 'Menu'),
    'T_CLOSE'                     => array('fr' => 'Fermer', 'en' => 'Close'),
    'T_HOME'                      => array('fr' => 'Accueil', 'en' => 'Home'),
    'T_BREADCRUMB'                => array('fr' => 'Fil d’Ariane', 'en' => 'Breadcrumb'),
    'T_SKIP_TO_CONTENT'           => array('fr' => 'Aller au contenu', 'en' => 'Skip to content'),
    'T_PAGINATION'                => array('fr' => 'Pagination', 'en' => 'Pagination'),
    'T_PREVIOUS_PAGE'             => array('fr' => 'Page précédente', 'en' => 'Previous page'),
    'T_NEXT_PAGE'                 => array('fr' => 'Page suivante', 'en' => 'Next page'),
    'T_DEV_MODE'                  => array('fr' => 'Mode développement', 'en' => 'Development mode'),
    'T_INVALID_TOKEN'             => array('fr' => 'Jeton de sécurité non valide : rechargez la page', 'en' => 'Invalid token: reload the page'),

    // Messages
    'T_MSG_SAVED'                 => array('fr' => 'L’élément a été enregistré', 'en' => 'The entry has been saved'),
    'T_MSG_ADDED'                 => array('fr' => 'L’élément a été ajouté', 'en' => 'The entry has been added successfully'),
    'T_MSG_DONE'                  => array('fr' => 'C’est fait', 'en' => 'Done'),
    'T_MSG_MISSING_PARAMETER'     => array('fr' => 'Paramètre manquant', 'en' => 'Missing parameter'),
    'T_MSG_FILL_MANDATORY'        => array('fr' => 'Remplissez tous les champs obligatoires', 'en' => 'Fill all mandatory fields'),
    'T_MSG_FIELDS_INVALID'        => array('fr' => 'Des champs sont manquants ou non valides', 'en' => 'Some fields are missing or not valid'),
    'T_ERROR_REQUIRED'            => array('fr' => 'Obligatoire', 'en' => 'Required'),
    'T_ERROR_TOO_LONG'            => array('fr' => 'Trop long', 'en' => 'Too long'),
    'T_ERROR_EMAIL_INVALID'       => array('fr' => 'L’adresse e-mail n’est pas valide', 'en' => 'The email address is not valid'),
    'T_ERROR_NOT_SAVED'           => array('fr' => 'L’élément n’a pas pu être enregistré', 'en' => 'The entry could not be saved'),
    'T_PAGE_NOT_FOUND'            => array('fr' => 'Page introuvable', 'en' => 'Page not found'),
    'T_ACCESS_DENIED'             => array('fr' => 'Accès refusé', 'en' => 'Access denied'),
    'T_ACCESS_DENIED_TEXT'        => array('fr' => 'Accès refusé : vos droits ne permettent pas d’ouvrir cette page', 'en' => 'Access denied: your permissions do not allow this page'),
    'T_404_TITLE'                 => array('fr' => 'Cette page n’existe pas', 'en' => 'This page does not exist'),
    'T_404_TEXT'                  => array('fr' => 'Le lien est peut-être cassé, ou la page a été supprimée.', 'en' => 'The link may be broken, or the page has been removed.'),
    'T_BACK_HOME'                 => array('fr' => 'Retour à l’accueil', 'en' => 'Back to the home page'),

    // Shop: header and footer
    'T_SHOP'                      => array('fr' => 'Boutique', 'en' => 'Shop'),
    'T_MAIN_MENU'                 => array('fr' => 'Menu principal', 'en' => 'Main menu'),
    'T_SEARCH_PRODUCT'            => array('fr' => 'Rechercher un produit', 'en' => 'Search a product'),
    'T_CART'                      => array('fr' => 'Panier', 'en' => 'Cart'),
    'T_CART_COUNT_ONE'            => array('fr' => 'Panier, %d article', 'en' => 'Cart, %d item'),
    'T_CART_COUNT'                => array('fr' => 'Panier, %d articles', 'en' => 'Cart, %d items'),
    'T_MY_PROFILE'                => array('fr' => 'Mon profil', 'en' => 'My profile'),
    'T_MY_CART'                   => array('fr' => 'Mon panier', 'en' => 'My cart'),
    'T_BROWSE'                    => array('fr' => 'Consulter', 'en' => 'Browse'),
    'T_SIGN_IN'                   => array('fr' => 'Se connecter', 'en' => 'Sign in'),
    'T_SIGN_OUT'                  => array('fr' => 'Se déconnecter', 'en' => 'Sign out'),
    'T_ACCOUNT'                   => array('fr' => 'Compte', 'en' => 'Account'),
    'T_ALL_PRODUCTS'              => array('fr' => 'Tous les produits', 'en' => 'All the products'),
    'T_CREATE_ACCOUNT'            => array('fr' => 'Créer un compte', 'en' => 'Create an account'),
    'T_FORGOT_PASSWORD'           => array('fr' => 'Mot de passe oublié', 'en' => 'Forgot password'),
    'T_FOOTER_VAT'                => array('fr' => 'Prix TVA comprise (%s %%).', 'en' => 'Prices include VAT (%s %%).'),

    // Back office: menu, header of the pages, search
    'T_BACK_OFFICE'               => array('fr' => 'Back-office', 'en' => 'Back office'),
    'T_BACK_OFFICE_MENU'          => array('fr' => 'Menu du back-office', 'en' => 'Back office menu'),
    'T_ADMIN_TAG'                 => array('fr' => 'Admin', 'en' => 'Admin'),
    'T_VIEW_SHOP'                 => array('fr' => 'Voir la boutique', 'en' => 'View the shop'),
    'T_SECTION_CATALOG'           => array('fr' => 'Catalogue', 'en' => 'Catalog'),
    'T_SECTION_SALES'             => array('fr' => 'Ventes', 'en' => 'Sales'),
    'T_SECTION_CONTENT'           => array('fr' => 'Contenu', 'en' => 'Content'),
    'T_SECTION_ADMINISTRATION'    => array('fr' => 'Administration', 'en' => 'Administration'),
    'T_SECTION_ACCOUNT'           => array('fr' => 'Compte', 'en' => 'Account'),
    'T_LISTS'                     => array('fr' => 'Listes', 'en' => 'Lists'),
    'T_EDIT_LIST'                 => array('fr' => 'Édition', 'en' => 'Edit list'),
    'T_VIEW_LIST'                 => array('fr' => 'Consultation', 'en' => 'View list'),
    'T_TAB_INFO'                  => array('fr' => 'Infos', 'en' => 'Info'),
    'T_TEXT_SEARCH'               => array('fr' => 'Recherche dans le texte', 'en' => 'Text search'),
    'T_FIELD'                     => array('fr' => 'Champ', 'en' => 'Field'),
    'T_FILTER_ON_FIELD'           => array('fr' => 'Filtrer sur un champ', 'en' => 'Filter on a field'),
    'T_VALUE'                     => array('fr' => 'Valeur', 'en' => 'Value'),

    // Components: title of the pages
    'T_COMPONENT_PRODUCT'         => array('fr' => 'Produit', 'en' => 'Product'),
    'T_COMPONENT_CATEGORY'        => array('fr' => 'Catégorie', 'en' => 'Category'),
    'T_COMPONENT_MEDIA'           => array('fr' => 'Média', 'en' => 'Media'),
    'T_COMPONENT_CUSTOMERORDER'   => array('fr' => 'Commande', 'en' => 'Customer order'),
    'T_COMPONENT_CUSTOMERORDERITEM' => array('fr' => 'Ligne de commande', 'en' => 'Customer order item'),
    'T_COMPONENT_CARTITEM'        => array('fr' => 'Ligne de panier', 'en' => 'Cart item'),
    'T_COMPONENT_DISCOUNT'        => array('fr' => 'Remise', 'en' => 'Discount'),
    'T_COMPONENT_POST'            => array('fr' => 'Article', 'en' => 'Post'),
    'T_COMPONENT_TRANSLATION'     => array('fr' => 'Traduction', 'en' => 'Translation'),
    'T_COMPONENT_USER'            => array('fr' => 'Utilisateur', 'en' => 'User'),
    'T_COMPONENT_PRODUCTMEDIA'    => array('fr' => 'Image du produit', 'en' => 'Product media'),
    'T_COMPONENT_PRODUCTCATEGORY' => array('fr' => 'Catégorie du produit', 'en' => 'Product category'),
    'T_COMPONENT_PRODUCTVARIANT'  => array('fr' => 'Variante', 'en' => 'Product variant'),

    // Components: menu of the back office and tabs above the forms
    'T_MENU_PRODUCT'              => array('fr' => 'Produits', 'en' => 'Products'),
    'T_MENU_CATEGORY'             => array('fr' => 'Catégories', 'en' => 'Categories'),
    'T_MENU_MEDIA'                => array('fr' => 'Médias', 'en' => 'Media'),
    'T_MENU_CUSTOMERORDER'        => array('fr' => 'Commandes', 'en' => 'Orders'),
    'T_MENU_CUSTOMERORDERITEM'    => array('fr' => 'Lignes de commande', 'en' => 'Order items'),
    'T_MENU_CARTITEM'             => array('fr' => 'Lignes de panier', 'en' => 'Cart items'),
    'T_MENU_DISCOUNT'             => array('fr' => 'Remises', 'en' => 'Discounts'),
    'T_MENU_POST'                 => array('fr' => 'Articles', 'en' => 'Posts'),
    'T_MENU_TRANSLATION'          => array('fr' => 'Traductions', 'en' => 'Translations'),
    'T_MENU_USER'                 => array('fr' => 'Utilisateurs', 'en' => 'Users'),
    'T_MENU_PRODUCTMEDIA'         => array('fr' => 'Images du produit', 'en' => 'Product images'),
    'T_MENU_PRODUCTCATEGORY'      => array('fr' => 'Catégories du produit', 'en' => 'Product categories'),
    'T_MENU_PRODUCTVARIANT'       => array('fr' => 'Variantes', 'en' => 'Variants'),

    // Fields of the database: T_FIELD_ + name of the column in capitals (ViewTemplate::fieldLabel())
    'T_FIELD_ID'                  => array('fr' => 'N°', 'en' => 'ID'),
    'T_FIELD_ID_CART_ITEM'        => array('fr' => 'Ligne de panier', 'en' => 'Cart item'),
    'T_FIELD_ID_CATEGORY'         => array('fr' => 'Catégorie', 'en' => 'Category'),
    'T_FIELD_ID_CUSTOMER_ORDER'   => array('fr' => 'Commande', 'en' => 'Order'),
    'T_FIELD_ID_CUSTOMER_ORDER_ITEM' => array('fr' => 'Ligne de commande', 'en' => 'Order item'),
    'T_FIELD_ID_DISCOUNT'         => array('fr' => 'Remise', 'en' => 'Discount'),
    'T_FIELD_ID_MEDIA'            => array('fr' => 'Média', 'en' => 'Media'),
    'T_FIELD_ID_PARENT'           => array('fr' => 'Parent', 'en' => 'Parent'),
    'T_FIELD_ID_POST'             => array('fr' => 'Article', 'en' => 'Post'),
    'T_FIELD_ID_PRODUCT'          => array('fr' => 'Produit', 'en' => 'Product'),
    'T_FIELD_ID_PRODUCT_VARIANT'  => array('fr' => 'Variante', 'en' => 'Variant'),
    'T_FIELD_ID_TRANSLATION'      => array('fr' => 'Traduction', 'en' => 'Translation'),
    'T_FIELD_ID_USER'             => array('fr' => 'Utilisateur', 'en' => 'User'),
    'T_FIELD_ALT_TEXT'            => array('fr' => 'Texte alternatif', 'en' => 'Alt text'),
    'T_FIELD_AMOUNT'              => array('fr' => 'Montant', 'en' => 'Amount'),
    'T_FIELD_BODY'                => array('fr' => 'Contenu', 'en' => 'Body'),
    'T_FIELD_CAPTION'             => array('fr' => 'Légende', 'en' => 'Caption'),
    'T_FIELD_COOKIE_ID'           => array('fr' => 'Identifiant du cookie', 'en' => 'Cookie ID'),
    'T_FIELD_CREATED_AT'          => array('fr' => 'Créé le', 'en' => 'Created at'),
    'T_FIELD_CURRENT_IMAGE'       => array('fr' => 'Image actuelle', 'en' => 'Current image'),
    'T_FIELD_DATE_FROM'           => array('fr' => 'Date de début', 'en' => 'Date from'),
    'T_FIELD_DATE_TO'             => array('fr' => 'Date de fin', 'en' => 'Date to'),
    'T_FIELD_DELETED_AT'          => array('fr' => 'Supprimé le', 'en' => 'Deleted at'),
    'T_FIELD_DESCRIPTION'         => array('fr' => 'Description', 'en' => 'Description'),
    'T_FIELD_DISCOUNT_AMOUNT'     => array('fr' => 'Montant de la remise', 'en' => 'Discount amount'),
    'T_FIELD_EMAIL'               => array('fr' => 'E-mail', 'en' => 'Email'),
    'T_FIELD_EMAIL_VERIFIED'      => array('fr' => 'E-mail vérifié', 'en' => 'Email verified'),
    'T_FIELD_EXCERPT'             => array('fr' => 'Extrait', 'en' => 'Excerpt'),
    'T_FIELD_FILENAME'            => array('fr' => 'Nom du fichier', 'en' => 'Filename'),
    'T_FIELD_FIRST_NAME'          => array('fr' => 'Prénom', 'en' => 'First name'),
    'T_FIELD_HEIGHT'              => array('fr' => 'Hauteur', 'en' => 'Height'),
    'T_FIELD_LANG'                => array('fr' => 'Langue', 'en' => 'Language'),
    'T_FIELD_LAST_LOGIN_AT'       => array('fr' => 'Dernière connexion', 'en' => 'Last login at'),
    'T_FIELD_LAST_NAME'           => array('fr' => 'Nom', 'en' => 'Last name'),
    'T_FIELD_LINE_TOTAL_AMOUNT'   => array('fr' => 'Total de la ligne', 'en' => 'Line total amount'),
    'T_FIELD_MIME_TYPE'           => array('fr' => 'Type MIME', 'en' => 'MIME type'),
    'T_FIELD_NAME'                => array('fr' => 'Nom', 'en' => 'Name'),
    'T_FIELD_PASSWORD_HASH'       => array('fr' => 'Mot de passe', 'en' => 'Password'),
    'T_FIELD_PASSWORD_HASH_CONFIRMATION' => array('fr' => 'Confirmation du mot de passe', 'en' => 'Password confirmation'),
    'T_FIELD_PERCENTAGE'          => array('fr' => 'Pourcentage', 'en' => 'Percentage'),
    'T_FIELD_PERMISSIONS'         => array('fr' => 'Droits', 'en' => 'Permissions'),
    'T_FIELD_POSITION'            => array('fr' => 'Position', 'en' => 'Position'),
    'T_FIELD_PRICE_AMOUNT'        => array('fr' => 'Prix', 'en' => 'Price'),
    'T_FIELD_PRICE_WITHOUT_TAX'   => array('fr' => 'Prix (HT)', 'en' => 'Price (without tax)'),
    'T_FIELD_PRODUCT'             => array('fr' => 'Produit', 'en' => 'Product'),
    'T_FIELD_PRODUCT_NAME'        => array('fr' => 'Nom du produit', 'en' => 'Product name'),
    'T_FIELD_PROVIDER'            => array('fr' => 'Connexion via', 'en' => 'Provider'),
    'T_FIELD_PROVIDER_USER_ID'    => array('fr' => 'Identifiant chez le fournisseur', 'en' => 'Provider user ID'),
    'T_FIELD_PUBLISHED_AT'        => array('fr' => 'Publié le', 'en' => 'Published at'),
    'T_FIELD_QUANTITY'            => array('fr' => 'Quantité', 'en' => 'Quantity'),
    'T_FIELD_SIZE_BYTES'          => array('fr' => 'Taille (octets)', 'en' => 'Size (bytes)'),
    'T_FIELD_SKU'                 => array('fr' => 'Référence', 'en' => 'SKU'),
    'T_FIELD_SLUG'                => array('fr' => 'Slug', 'en' => 'Slug'),
    'T_FIELD_STATUS'              => array('fr' => 'Statut', 'en' => 'Status'),
    'T_FIELD_SUMMARY'             => array('fr' => 'Résumé', 'en' => 'Summary'),
    'T_FIELD_TAX_AMOUNT'          => array('fr' => 'Montant de la TVA', 'en' => 'Tax amount'),
    'T_FIELD_TEXT'                => array('fr' => 'Texte', 'en' => 'Text'),
    'T_FIELD_TEXT_KEY'            => array('fr' => 'Clé du texte', 'en' => 'Text key'),
    'T_FIELD_TITLE'               => array('fr' => 'Titre', 'en' => 'Title'),
    'T_FIELD_UNIT_PRICE_AMOUNT'   => array('fr' => 'Prix unitaire', 'en' => 'Unit price'),
    'T_FIELD_UPDATED_AT'          => array('fr' => 'Modifié le', 'en' => 'Updated at'),
    'T_FIELD_WIDTH'               => array('fr' => 'Largeur', 'en' => 'Width'),

    // Values of the lists of choices: T_VALUE_ + value in capitals (ViewTemplate::value())
    'T_VALUE_ACTIVE'              => array('fr' => 'Actif', 'en' => 'Active'),
    'T_VALUE_ARCHIVED'            => array('fr' => 'Archivé', 'en' => 'Archived'),
    'T_VALUE_DELETED'             => array('fr' => 'Supprimé', 'en' => 'Deleted'),
    'T_VALUE_DISABLED'            => array('fr' => 'Désactivé', 'en' => 'Disabled'),
    'T_VALUE_DRAFT'               => array('fr' => 'Brouillon', 'en' => 'Draft'),
    'T_VALUE_PUBLISHED'           => array('fr' => 'Publié', 'en' => 'Published'),
    'T_VALUE_SUSPENDED'           => array('fr' => 'Suspendu', 'en' => 'Suspended'),

    // Catalog and product page
    'T_SORT_BY'                   => array('fr' => 'Trier par', 'en' => 'Sort by'),
    'T_SORT_PRODUCTS'             => array('fr' => 'Trier les produits', 'en' => 'Sort the products'),
    'T_SORT_NEWEST'               => array('fr' => 'Nouveautés', 'en' => 'Newest'),
    'T_SORT_PRICE_ASC'            => array('fr' => 'Prix croissant', 'en' => 'Price: low to high'),
    'T_SORT_PRICE_DESC'           => array('fr' => 'Prix décroissant', 'en' => 'Price: high to low'),
    'T_SORT_NAME'                 => array('fr' => 'Nom', 'en' => 'Name'),
    'T_PRODUCTS_ON_PAGE_ONE'      => array('fr' => '%d produit sur cette page', 'en' => '%d product on this page'),
    'T_PRODUCTS_ON_PAGE'          => array('fr' => '%d produits sur cette page', 'en' => '%d products on this page'),
    'T_RESULTS_FOR'               => array('fr' => 'Résultats pour « %s »', 'en' => 'Results for “%s”'),
    'T_CLEAR_SEARCH'              => array('fr' => 'Effacer la recherche', 'en' => 'Clear the search'),
    'T_NO_PRODUCT'                => array('fr' => 'Aucun produit trouvé', 'en' => 'No product found'),
    'T_NO_PRODUCT_HINT'           => array('fr' => 'Essayez un autre mot, ou parcourez tous les produits.', 'en' => 'Try another word, or look at all the products.'),
    'T_SEE_ALL_PRODUCTS'          => array('fr' => 'Voir tous les produits', 'en' => 'See all the products'),
    'T_ADD_TO_CART'               => array('fr' => 'Ajouter au panier', 'en' => 'Add to cart'),
    'T_QUANTITY'                  => array('fr' => 'Quantité', 'en' => 'Quantity'),
    'T_LESS'                      => array('fr' => 'Moins', 'en' => 'Less'),
    'T_MORE'                      => array('fr' => 'Plus', 'en' => 'More'),
    'T_EXCL_TAX'                  => array('fr' => 'HT', 'en' => 'excl. tax'),
    'T_VAT_INCLUDED'              => array('fr' => 'TVA %s %% comprise', 'en' => 'VAT %s %% included'),
    'T_NOT_ON_SALE'               => array('fr' => 'Indisponible', 'en' => 'Not on sale'),
    'T_PRODUCT_NOT_ON_SALE'       => array('fr' => 'Ce produit n’est pas en vente', 'en' => 'This product is not on sale'),
    'T_PRODUCT_UNAVAILABLE'       => array('fr' => 'Ce produit n’est pas disponible', 'en' => 'This product is not available'),
    'T_FEATURE_GUEST'             => array('fr' => 'Commande avec ou sans compte', 'en' => 'Checkout as a guest or with your account'),
    'T_FEATURE_DELIVERY'          => array('fr' => 'Adresse de livraison choisie à la commande', 'en' => 'Delivery address chosen at the checkout'),
    'T_IMAGE_N'                   => array('fr' => '%s, image %d', 'en' => '%s - image %d'),

    // Cart, checkout, confirmation
    'T_STEPS_LABEL'               => array('fr' => 'Étapes de la commande', 'en' => 'Steps of the order'),
    'T_STEP_CART'                 => array('fr' => 'Panier', 'en' => 'Cart'),
    'T_STEP_DETAILS'              => array('fr' => 'Coordonnées et livraison', 'en' => 'Details &amp; delivery'),
    'T_STEP_CONFIRMATION'         => array('fr' => 'Confirmation', 'en' => 'Confirmation'),
    'T_CHECKOUT'                  => array('fr' => 'Commander', 'en' => 'Checkout'),
    'T_CHECKOUT_TITLE'            => array('fr' => 'Commande', 'en' => 'Checkout'),
    'T_ORDER'                     => array('fr' => 'Commande confirmée', 'en' => 'Order'),
    'T_ITEMS_ONE'                 => array('fr' => '%d article', 'en' => '%d item'),
    'T_ITEMS'                     => array('fr' => '%d articles', 'en' => '%d items'),
    'T_PRICE_PER_UNIT'            => array('fr' => '%s HT / unité', 'en' => '%s excl. tax / unit'),
    'T_QUANTITY_OF'               => array('fr' => 'Quantité de %s', 'en' => 'Quantity of %s'),
    'T_REMOVE'                    => array('fr' => 'Retirer', 'en' => 'Remove'),
    'T_REMOVE_ITEM'               => array('fr' => 'Retirer %s du panier', 'en' => 'Remove %s'),
    'T_CONTINUE_SHOPPING'         => array('fr' => 'Continuer mes achats', 'en' => 'Continue shopping'),
    'T_UPDATE_CART'               => array('fr' => 'Mettre à jour le panier', 'en' => 'Update the cart'),
    'T_SUMMARY'                   => array('fr' => 'Récapitulatif', 'en' => 'Summary'),
    'T_SUBTOTAL_EXCL_TAX'         => array('fr' => 'Sous-total HT', 'en' => 'Subtotal excl. tax'),
    'T_VAT'                       => array('fr' => 'TVA %s %%', 'en' => 'VAT %s %%'),
    'T_TOTAL_INCL_TAX'            => array('fr' => 'Total TTC', 'en' => 'Total incl. tax'),
    'T_NO_ACCOUNT_NEEDED'         => array('fr' => 'Pas besoin de compte : il est créé lors de la commande.', 'en' => 'No account needed: it is created at the checkout.'),
    'T_CART_EMPTY'                => array('fr' => 'Votre panier est vide', 'en' => 'Your cart is empty'),
    'T_CART_EMPTY_HINT'           => array('fr' => 'Les produits ajoutés à votre panier apparaissent ici.', 'en' => 'The products you add to your cart appear here.'),
    'T_SEE_PRODUCTS'              => array('fr' => 'Voir les produits', 'en' => 'See the products'),
    'T_ALREADY_CUSTOMER'          => array('fr' => 'Déjà client ?', 'en' => 'Already a customer?'),
    'T_SIGN_IN_ADDRESSES'         => array('fr' => 'Connectez-vous pour utiliser vos adresses enregistrées.', 'en' => 'Sign in to use your saved addresses.'),
    'T_PERSONAL_DATA'             => array('fr' => 'Vos coordonnées', 'en' => 'Your personal data'),
    'T_PERSONAL_DATA_NEW'         => array('fr' => 'Nouveau client : vos coordonnées', 'en' => 'New customer: your personal data'),
    'T_FIRST_NAME'                => array('fr' => 'Prénom', 'en' => 'First name'),
    'T_LAST_NAME'                 => array('fr' => 'Nom', 'en' => 'Last name'),
    'T_EMAIL'                     => array('fr' => 'E-mail', 'en' => 'Email'),
    'T_PHONE'                     => array('fr' => 'Téléphone', 'en' => 'Phone'),
    'T_PASSWORD'                  => array('fr' => 'Mot de passe', 'en' => 'Password'),
    'T_PASSWORD_TO_CREATE_ACCOUNT' => array('fr' => '(pour créer votre compte)', 'en' => '(to create your account)'),
    'T_PASSWORD_CONFIRMATION'     => array('fr' => 'Confirmation du mot de passe', 'en' => 'Password confirmation'),
    'T_DELIVERY_ADDRESS'          => array('fr' => 'Adresse de livraison', 'en' => 'Delivery address'),
    'T_POSTAL_ADDRESS'            => array('fr' => 'Adresse postale', 'en' => 'Postal address'),
    'T_SAME_AS_DELIVERY'          => array('fr' => 'Identique à l’adresse de livraison', 'en' => 'Same as the delivery address'),
    'T_NEW_ADDRESS'               => array('fr' => 'Une nouvelle adresse', 'en' => 'A new address'),
    'T_STREET'                    => array('fr' => 'Rue', 'en' => 'Street'),
    'T_NUMBER'                    => array('fr' => 'Numéro', 'en' => 'Number'),
    'T_BOX'                       => array('fr' => 'Boîte', 'en' => 'Box'),
    'T_ADDRESS_BOX'               => array('fr' => 'bte', 'en' => 'box'),
    'T_POSTAL_CODE'               => array('fr' => 'Code postal', 'en' => 'Postal code'),
    'T_CITY'                      => array('fr' => 'Localité', 'en' => 'City'),
    'T_COUNTRY'                   => array('fr' => 'Pays', 'en' => 'Country'),
    'T_COUNTRY_BE'                => array('fr' => 'Belgique', 'en' => 'Belgium'),
    'T_COUNTRY_FR'                => array('fr' => 'France', 'en' => 'France'),
    'T_COUNTRY_LU'                => array('fr' => 'Luxembourg', 'en' => 'Luxembourg'),
    'T_COUNTRY_NL'                => array('fr' => 'Pays-Bas', 'en' => 'Netherlands'),
    'T_COUNTRY_DE'                => array('fr' => 'Allemagne', 'en' => 'Germany'),
    'T_BACK_TO_CART'              => array('fr' => 'Retour au panier', 'en' => 'Back to the cart'),
    'T_CONFIRM_ORDER'             => array('fr' => 'Confirmer la commande', 'en' => 'Confirm the order'),
    'T_YOUR_ORDER'                => array('fr' => 'Votre commande', 'en' => 'Your order'),
    'T_ORDER_NOT_FOUND'           => array('fr' => 'Commande introuvable', 'en' => 'Order not found'),
    'T_ORDER_THANKS'              => array('fr' => 'Merci, votre commande est enregistrée', 'en' => 'Thank you, your order is registered'),
    'T_ORDER_NUMBER'              => array('fr' => 'Commande n°', 'en' => 'Order n°'),
    'T_BACK_TO_PRODUCTS'          => array('fr' => 'Retour aux produits', 'en' => 'Back to the products'),
    'T_MSG_ADDED_TO_CART'         => array('fr' => 'Le produit a été ajouté à votre panier', 'en' => 'The product has been added to your cart'),
    'T_MSG_CANNOT_ADD_TO_CART'    => array('fr' => 'Ce produit ne peut pas être ajouté au panier', 'en' => 'This product cannot be added to the cart'),
    'T_MSG_CART_UPDATED'          => array('fr' => 'Votre panier a été mis à jour', 'en' => 'Your cart has been updated'),
    'T_ERROR_ACCOUNT_EXISTS_SIGN_IN' => array('fr' => 'Un compte existe déjà avec cette adresse e-mail : connectez-vous ci-dessus', 'en' => 'An account already exists with this email address: sign in above'),
    'T_ERROR_ACCOUNT_NOT_CREATED' => array('fr' => 'Le compte n’a pas pu être créé', 'en' => 'The account could not be created'),
    'T_ERROR_ADDRESS_NOT_SAVED'   => array('fr' => 'L’adresse n’a pas pu être enregistrée', 'en' => 'The address could not be saved'),
    'T_ERROR_ORDER_NOT_CREATED'   => array('fr' => 'La commande n’a pas pu être créée, votre panier a été conservé', 'en' => 'The order could not be created, your cart has been kept'),

    // Account: sign in, registration, password, profile
    'T_SIGN_IN_INTRO'             => array('fr' => 'Bon retour ! Connectez-vous avec votre adresse e-mail.', 'en' => 'Welcome back! Sign in with your email address.'),
    'T_FORGOT_PASSWORD_Q'         => array('fr' => 'Mot de passe oublié ?', 'en' => 'Forgot password?'),
    'T_NEW_HERE'                  => array('fr' => 'Nouveau ici ?', 'en' => 'New here?'),
    'T_REGISTER_INTRO'            => array('fr' => 'Cela prend moins d’une minute.', 'en' => 'It takes less than a minute.'),
    'T_CONFIRM_PASSWORD'          => array('fr' => 'Confirmer le mot de passe', 'en' => 'Confirm password'),
    'T_CREATE_MY_ACCOUNT'         => array('fr' => 'Créer mon compte', 'en' => 'Create my account'),
    'T_PASSWORD_MIN_HINT'         => array('fr' => 'Au moins %d caractères.', 'en' => 'At least %d characters.'),
    'T_FORGOT_INTRO'              => array('fr' => 'Indiquez votre adresse e-mail, vous recevrez un lien pour choisir un nouveau mot de passe.', 'en' => 'Enter your email address, you will receive a link to choose a new password.'),
    'T_RESET_LINK'                => array('fr' => 'lien de réinitialisation', 'en' => 'reset link'),
    'T_SEND_LINK'                 => array('fr' => 'Envoyer le lien', 'en' => 'Send the link'),
    'T_BACK_TO_SIGN_IN'           => array('fr' => 'Retour à la connexion', 'en' => 'Back to sign in'),
    'T_NEW_PASSWORD'              => array('fr' => 'Nouveau mot de passe', 'en' => 'New password'),
    'T_RESET_INTRO'               => array('fr' => 'Choisissez le nouveau mot de passe de votre compte.', 'en' => 'Choose the new password of your account.'),
    'T_SAVE_NEW_PASSWORD'         => array('fr' => 'Enregistrer le nouveau mot de passe', 'en' => 'Save the new password'),
    'T_ASK_NEW_LINK'              => array('fr' => 'Demander un nouveau lien', 'en' => 'Ask a new link'),
    'T_CONTINUE_WITH'             => array('fr' => 'Continuer avec %s', 'en' => 'Continue with %s'),
    'T_BACK_TO_SHOP'              => array('fr' => 'Retour à la boutique', 'en' => 'Back to the shop'),
    'T_YOUR_ACCOUNT'              => array('fr' => 'Votre compte', 'en' => 'Your account'),
    'T_CHECKOUT_FASTER'           => array('fr' => 'Commandez plus vite', 'en' => 'Check out faster'),
    'T_ADDRESSES_SAVED'           => array('fr' => 'Vos adresses enregistrées', 'en' => 'Your addresses saved'),
    'T_PERSONAL_DETAILS'          => array('fr' => 'Informations personnelles', 'en' => 'Personal details'),
    'T_EMAIL_GIVEN_BY'            => array('fr' => 'Fournie par votre compte %s', 'en' => 'Given by your %s account'),
    'T_CHANGE_PASSWORD'           => array('fr' => 'Changer de mot de passe', 'en' => 'Change password'),
    'T_PASSWORD_KEEP_HINT'        => array('fr' => 'Laissez vide pour garder votre mot de passe.', 'en' => 'Leave empty to keep your password.'),
    'T_CURRENT_PASSWORD'          => array('fr' => 'Mot de passe actuel', 'en' => 'Current password'),
    'T_CONFIRMATION'              => array('fr' => 'Confirmation', 'en' => 'Confirmation'),
    'T_SIGN_IN_WITH'              => array('fr' => 'Connexion avec', 'en' => 'Sign in with'),
    'T_EMAIL_AND_PASSWORD'        => array('fr' => 'E-mail et mot de passe', 'en' => 'Email and password'),
    'T_MEMBER_SINCE'              => array('fr' => 'Membre depuis', 'en' => 'Member since'),
    'T_LAST_SIGN_IN'              => array('fr' => 'Dernière connexion', 'en' => 'Last sign in'),
    'T_PERMISSIONS_HELP'          => array('fr' => '0 : consultation et profil &middot; %d+ : ajouter, modifier, supprimer &middot; %d : gérer les utilisateurs', 'en' => '0: consultation and own profile &middot; %d+: add, edit, delete &middot; %d: manage the users'),
    'T_PERMISSIONS_OWN'           => array('fr' => 'Vous ne pouvez pas changer votre propre niveau', 'en' => 'You cannot change your own level'),
    'T_MSG_PROFILE_SAVED'         => array('fr' => 'Votre profil a été enregistré', 'en' => 'Your profile has been saved'),
    'T_MSG_RESET_LINK_SENT'       => array('fr' => 'Si un compte existe avec cette adresse e-mail, un lien pour choisir un nouveau mot de passe a été envoyé', 'en' => 'If an account exists with this email address, a link to choose a new password has been sent'),
    'T_MSG_PASSWORD_CHANGED'      => array('fr' => 'Votre mot de passe a été changé, vous pouvez vous connecter', 'en' => 'Your password has been changed, you can sign in'),
    'T_LINK_INVALID'              => array('fr' => 'Ce lien n’est pas valide ou a expiré', 'en' => 'This link is not valid or has expired'),
    'T_ERROR_NAME_TOO_LONG'       => array('fr' => 'Le prénom ou le nom est trop long', 'en' => 'The first name or the last name is too long'),
    'T_ERROR_CURRENT_PASSWORD'    => array('fr' => 'Le mot de passe actuel est incorrect', 'en' => 'The current password is wrong'),
    'T_ERROR_ACCOUNT_EXISTS'      => array('fr' => 'Un compte existe déjà avec cette adresse e-mail', 'en' => 'An account already exists with this email address'),
    'T_ERROR_PASSWORD_MIN'        => array('fr' => 'Le mot de passe doit contenir au moins %d caractères', 'en' => 'The password must contain at least %d characters'),
    'T_ERROR_PASSWORD_MAX'        => array('fr' => 'Le mot de passe est trop long (72 caractères maximum)', 'en' => 'The password is too long (72 characters maximum)'),
    'T_ERROR_PASSWORD_CONFIRMATION' => array('fr' => 'Le mot de passe et sa confirmation sont différents', 'en' => 'The password and its confirmation are different'),
    'T_ERROR_TOO_MANY_ATTEMPTS'   => array('fr' => 'Trop de tentatives, réessayez dans quelques minutes', 'en' => 'Too many attempts, try again in a few minutes'),
    'T_ERROR_WRONG_LOGIN'         => array('fr' => 'E-mail ou mot de passe incorrect', 'en' => 'Wrong email or password'),
    'T_OAUTH_EXPIRED'             => array('fr' => 'La connexion a expiré, réessayez', 'en' => 'The sign in has expired, try again'),
    'T_OAUTH_CANCELLED'           => array('fr' => 'La connexion avec %s a été annulée', 'en' => 'The sign in with %s has been cancelled'),
    'T_OAUTH_FAILED'              => array('fr' => 'La connexion avec %s a échoué, réessayez', 'en' => 'The sign in with %s failed, try again'),
    'T_OAUTH_NO_EMAIL'            => array('fr' => 'Votre compte %s n’a pas fourni d’adresse e-mail vérifiée, elle est nécessaire pour créer votre compte', 'en' => 'Your %s account did not give a verified email address, it is required to create your account'),
    'T_OAUTH_DISABLED'            => array('fr' => 'Ce compte est désactivé', 'en' => 'This account is disabled'),

    // Emails (plain text)
    'T_MAIL_HELLO'                => array('fr' => 'Bonjour,', 'en' => 'Hello,'),
    'T_MAIL_RESET_SUBJECT'        => array('fr' => 'Choisissez un nouveau mot de passe', 'en' => 'Reset your password'),
    'T_MAIL_RESET_TEXT'           => array('fr' => 'Utilisez le lien suivant pour choisir un nouveau mot de passe, il est valable %d minutes :', 'en' => 'Use the following link to choose a new password, it is valid for %d minutes:'),
    'T_MAIL_RESET_IGNORE'         => array('fr' => 'Si vous n’avez pas demandé de nouveau mot de passe, ignorez cet e-mail.', 'en' => 'If you did not ask for a new password, ignore this email.'),
    'T_MAIL_FORM_TEXT'            => array('fr' => 'Merci d’avoir rempli le formulaire. Vous pouvez le consulter avec le lien suivant, valable %d jours :', 'en' => 'Thank you for filling the form. You can see it with the following link, valid for %d days:'),
    'T_MAIL_FORM_CODES'           => array('fr' => 'Voici %d codes qui donnent accès au formulaire :', 'en' => 'Here are %d codes that give access to the form:'),

    // Access by code
    'T_ACCESS_CODE'               => array('fr' => 'Code d’accès', 'en' => 'Access code'),
    'T_ACCESS_CODE_TITLE'         => array('fr' => 'Entrez votre code d’accès', 'en' => 'Enter your access code'),
    'T_ACCESS_CODE_INTRO'         => array('fr' => 'Le code ouvre votre formulaire.', 'en' => 'The code opens your form.'),
    'T_CODE'                      => array('fr' => 'Code', 'en' => 'Code'),
    'T_CODES'                     => array('fr' => 'Codes :', 'en' => 'Codes:'),
    'T_FORM'                      => array('fr' => 'Formulaire', 'en' => 'Form'),
    'T_YOUR_FORM'                 => array('fr' => 'Votre formulaire', 'en' => 'Your form'),
    'T_YOUR_EMAIL'                => array('fr' => 'Votre adresse e-mail', 'en' => 'Your email address'),
    'T_ACCESS_EMAIL_INTRO'        => array('fr' => 'Nous vous enverrons un lien vers votre formulaire.', 'en' => 'We will send you a link to your form.'),
    'T_BACK_TO_FORM'              => array('fr' => 'Retour au formulaire', 'en' => 'Back to the form'),
    'T_LINK_SENT'                 => array('fr' => 'Lien envoyé', 'en' => 'Link sent'),
    'T_CHECK_INBOX'               => array('fr' => 'Consultez votre boîte mail', 'en' => 'Check your inbox'),
    'T_FORM_SAVED'                => array('fr' => 'Votre formulaire a été enregistré', 'en' => 'Your form has been saved'),
    'T_FORM_SAVED_SENT'           => array('fr' => 'Votre formulaire a été enregistré, un lien et de nouveaux codes ont été envoyés à %s.', 'en' => 'Your form has been saved, a link and new codes have been sent to %s.'),
    'T_FORM_SAVED_NOT_SENT'       => array('fr' => 'Mais l’e-mail à %s n’a pas pu être envoyé.', 'en' => 'But the email to %s could not be sent.'),
    'T_ENTER_CODE'                => array('fr' => 'Saisir un code', 'en' => 'Enter a code'),
    'T_ERROR_TOO_MANY_CODES'      => array('fr' => 'Trop de codes erronés, réessayez dans %d minute(s)', 'en' => 'Too many wrong codes, try again in %d minute(s)'),
    'T_ERROR_ENTER_CODE'          => array('fr' => 'Veuillez saisir un code', 'en' => 'Please enter a code'),
    'T_ERROR_CODE_UNKNOWN'        => array('fr' => 'Ce code n’existe pas', 'en' => 'This code does not exist'),
    'T_ERROR_CODE_FIRST'          => array('fr' => 'Veuillez d’abord saisir votre code', 'en' => 'Please enter your code first'),
    'T_ERROR_FORM_NOT_SAVED'      => array('fr' => 'Le formulaire n’a pas pu être enregistré', 'en' => 'The form could not be saved'),
    'T_ACCESS_FIELD_01'           => array('fr' => 'Champ 1', 'en' => 'Field 1'),
    'T_ACCESS_FIELD_02'           => array('fr' => 'Champ 2', 'en' => 'Field 2'),
    'T_ACCESS_FIELD_03'           => array('fr' => 'Champ 3', 'en' => 'Field 3'),
    'T_ACCESS_FIELD_04'           => array('fr' => 'Champ 4', 'en' => 'Field 4'),
    'T_ACCESS_FIELD_05'           => array('fr' => 'Champ 5', 'en' => 'Field 5'),
    'T_ACCESS_FIELD_06'           => array('fr' => 'Champ 6', 'en' => 'Field 6'),
    'T_ACCESS_FIELD_07'           => array('fr' => 'Champ 7', 'en' => 'Field 7'),
    'T_ACCESS_FIELD_08'           => array('fr' => 'Champ 8', 'en' => 'Field 8'),
    'T_ACCESS_FIELD_09'           => array('fr' => 'Champ 9', 'en' => 'Field 9'),
    'T_ACCESS_FIELD_10'           => array('fr' => 'Champ 10', 'en' => 'Field 10'),
    'T_ACCESS_FIELD_11'           => array('fr' => 'Champ 11', 'en' => 'Field 11'),
    'T_ACCESS_FIELD_12'           => array('fr' => 'Champ 12', 'en' => 'Field 12'),
    'T_ACCESS_FIELD_13'           => array('fr' => 'Champ 13', 'en' => 'Field 13'),
    'T_ACCESS_FIELD_14'           => array('fr' => 'Champ 14', 'en' => 'Field 14'),
    'T_ACCESS_FIELD_15'           => array('fr' => 'Champ 15', 'en' => 'Field 15'),

    // Media: images sent in the back office
    'T_IMAGE'                     => array('fr' => 'Image', 'en' => 'Image'),
    'T_REPLACE_IMAGE'             => array('fr' => 'Remplacer l’image', 'en' => 'Replace the image'),
    'T_IMAGE_HELP'                => array('fr' => 'JPEG, PNG, GIF ou WebP, %d Mo maximum. Tailles créées : %s.', 'en' => 'JPEG, PNG, GIF or WebP, %d MB maximum. Sizes created: %s.'),
    'T_IMAGE_PRODUCT_HELP'        => array('fr' => 'Le fichier est nommé d’après le produit. Sans produit : d’après le texte alternatif, sinon le nom du fichier.', 'en' => 'The file is named after the product. Without product: after the alt text, else the name of the file.'),
    'T_IMAGE_PRODUCT_HELP_NEW'    => array('fr' => 'Le fichier est nommé d’après le produit et l’image est ajoutée à ses images. Sans produit : d’après le texte alternatif, sinon le nom du fichier.', 'en' => 'The file is named after the product and the image is added to its images. Without product: after the alt text, else the name of the file.'),
    'T_ERROR_MEDIA_GONE'          => array('fr' => 'Ce média n’existe plus', 'en' => 'This media does not exist anymore'),
    'T_ERROR_CHOOSE_IMAGE'        => array('fr' => 'Choisissez une image', 'en' => 'Choose an image'),
    'T_ERROR_IMAGE_NOT_LINKED'    => array('fr' => 'L’image a été enregistrée, mais elle n’a pas pu être ajoutée au produit', 'en' => 'The image has been saved, but it could not be added to the product'),
    'T_UPLOAD_NO_GD'              => array('fr' => 'L’extension GD de PHP est nécessaire pour redimensionner les images', 'en' => 'The GD extension of PHP is needed to resize the images'),
    'T_UPLOAD_INVALID'            => array('fr' => 'Envoi non valide', 'en' => 'Invalid upload'),
    'T_UPLOAD_NO_FILE'            => array('fr' => 'Aucun fichier envoyé', 'en' => 'No file sent'),
    'T_UPLOAD_TOO_BIG_INI'        => array('fr' => 'Le fichier est trop gros (upload_max_filesize de php.ini : %s)', 'en' => 'The file is too big (upload_max_filesize of php.ini: %s)'),
    'T_UPLOAD_FAILED'             => array('fr' => 'L’envoi a échoué (code %d)', 'en' => 'The upload failed (code %d)'),
    'T_UPLOAD_TOO_BIG'            => array('fr' => 'Le fichier est trop gros, %d Mo maximum', 'en' => 'The file is too big, %d MB maximum'),
    'T_UPLOAD_NOT_IMAGE'          => array('fr' => 'Ce fichier n’est pas une image acceptée (JPEG, PNG, GIF ou WebP)', 'en' => 'This file is not an accepted image (JPEG, PNG, GIF or WebP)'),
    'T_UPLOAD_TOO_MANY_PIXELS'    => array('fr' => 'L’image est trop grande, %d mégapixels maximum', 'en' => 'The image is too large, %d megapixels maximum'),
    'T_UPLOAD_UNREADABLE'         => array('fr' => 'L’image n’a pas pu être lue', 'en' => 'The image could not be read'),
    'T_UPLOAD_NO_FOLDER'          => array('fr' => 'Le dossier %s n’a pas pu être créé', 'en' => 'The folder %s could not be created'),
    'T_UPLOAD_NOT_STORED'         => array('fr' => 'Le fichier n’a pas pu être enregistré dans %s', 'en' => 'The file could not be stored in %s'),
    'T_UPLOAD_SIZE_FAILED'        => array('fr' => 'La taille « %s » de l’image n’a pas pu être créée', 'en' => 'The size "%s" of the image could not be created'),
);


// One constant by text, in the language of the request (a constant already defined, ex: in config.php, is kept)
// ------------------------------------------------------------------------------------------------
foreach($translations as $name => $texts){
    if(!defined($name)){
        define($name, $texts[LANG] ?? $texts[LANG_DEFAULT] ?? (string)reset($texts));
    }
    elseif(!is_string(constant($name))){
        // Name already used by PHP (extension tokenizer), the text would be replaced by a number
        trigger_error('translations.php: '.$name.' is a constant of PHP, give another name to this text', E_USER_WARNING);
    }
}
unset($translations, $name, $texts, $lang);
