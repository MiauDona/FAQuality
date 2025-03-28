<?php
//Insertamos los contenidos de los archivos
add_action( 'admin_menu', 'fqr_Add_My_Admin_Link' );
include 'fqr-primera-pagina.php';
include 'fqr-categoria.php';
include 'fqr-new-categoria.php';
include 'fqr-faq.php';
include 'fqr-nuevo-faq.php';
include 'fqr-contacto.php';
include 'fqr-aboutus.php';
include 'fqr-ajustes.php';

// Add a new top level menu link to the ACP
function fqr_Add_My_Admin_Link()
{
    add_menu_page( //Menu principal
        'FAQuality', // Title of the page
        'FAQuality', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'FAQuality', // Slug del menú (URL amigable)
        'FAQuality_page', // Función que mostrará el contenido de la página
        'dashicons-format-status', // Icono del menú (puedes usar uno de los iconos predeterminados de WordPress)
        65 // Posición en el menú de administración
    );

    add_submenu_page( //Menu categoria
        'FAQuality',           // El slug del menú principal al que pertenece
        'Categorías', // Título de la página del submenú
        'Categorías',              // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'FAQ_Categoria',   // Slug único para la página del submenú
        'FAQuality_categoria_page' // Función que renderiza la página del submenú
    );
    
    add_submenu_page( //Menu crear categorias
        'FAQuality',           // El slug del menú principal al que pertenece
        'Nueva Categoría', // Título de la página del submenú
        'Nueva Categoría',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'FAQ_New_Categoria',   // Slug único para la página del submenú
        'FAQuality_new_categoria_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu FAQ
        'FAQuality',   // El slug del menú principal al que pertenece
        'FAQs', // Título de la página del submenú
        'FAQs',  // Nombre del submenú que aparecerá en el menú
        'manage_options',  // Permiso requerido
        'FAQ',   // Slug único para la página del submenú
        'FAQuality_faq_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu crear faqs
        'FAQuality',  // El slug del menú principal al que pertenece
        'Nuevo FAQ', // Título de la página del submenú
        'Nuevo FAQ',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'Nuevo_FAQ',   // Slug único para la página del submenú
        'FAQuality_new_faq_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu contacto
        'FAQuality',  // El slug del menú principal al que pertenece
        'Contactos', // Título de la página del submenú
        'Contactos',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'Contactos',   // Slug único para la página del submenú
        'FAQuality_contact_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu ABOUT US
        'FAQuality',  // El slug del menú principal al que pertenece
        'Sobre nosotros', // Título de la página del submenú
        'Sobre nosotros',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'aboutus',   // Slug único para la página del submenú
        'FAQuality_aboutus_page' // Función que renderiza la página del submenú
    );

    add_submenu_page( //Menu envio de Email
        'FAQuality',  // El slug del menú principal al que pertenece
        'Ajustes', // Título de la página del submenú
        'Ajustes',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'ajustes',   // Slug único para la página del submenú
        'ajustes_page' // Función que renderiza la página del submenú
    );
}




