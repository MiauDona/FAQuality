<?php

function deleteCategoria() {
    // Verifica si la acción es "delete" y si el ID de la categoría está definido
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']); // Asegúrate de que el ID sea un número válido
    
        include_once("./bbdd.actions.php");
        // Eliminar la categoría de la base de datos
        dbMarkAsDeletedCategoria($id);

        // Redirigir para evitar que la acción se repita al recargar la página
        wp_safe_redirect(admin_url('admin.php?page=FAQ_Categoria'));
        exit;
    }
}

function FAQuality_edit_categoria_page() {
    global $wpdb;
    
    //Asignamos nombre y prefijo a la tabla
    $prefijo = $wpdb->prefix . 'fqr_'; // Prefijo para todas las tablas
    $tabla_categoria = $prefijo . 'categoria';  
    
      // Si la acción es editar, mostramos el formulario con los datos actuales
      if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $categoria = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_categoria WHERE id = $id AND borrado = 0"));
        
        if ($categoria) {
            // Mostrar formulario de edición con los datos actuales
            ?>
           <div class="wrap">
            <h1>Editar Categoría</h1>
            <form method="post" action="">
                <!-- Campo oculto para la ID -->
                <input type="hidden" name="id" value="<?php echo esc_attr($id); ?>">
                <!-- Campo para el título -->      
                <!-- Insertamos los datos con el nombre   -->
                <label for="titulo_categoria"><strong>Título de la Categoría:</strong></label><br>
                <input type="text" id="categoria" name="categoria" value="<?php echo esc_attr($categoria->categoria); ?>"
                style="width: 100%; font-size: 18px; padding: 10px; margin-bottom: 10px;" placeholder="Escribe el nombre de la categoría aquí">   
                <label for="titulo_descripcion"><strong>Descripción:</strong></label><br>
                <textarea id="descripcion" name="descripcion"
                style="width: 100%; height: 200px; min-height:48px; font-size: 16px; padding: 10px; 
                    margin-bottom: 10px; resize: vertical;"
                placeholder="Escribe la descripción aquí"></textarea><br>
                <!-- Boton de enviar -->
                <input type="submit" name="update_categoria" value="Actualizar" class="button button-primary">

            </form>            
            </div>
         <?php
        } 
    } else {
        $action = $_GET['action'];
        $id = $_GET['id'];
        $categoria = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_categoria WHERE id = $id AND borrado = 0"));
        wp_die("No se ha podido editar. Action -> $action. ID -> $id. Consulta-> $categoria");
    }

    
    
    // Verificar si se ha enviado el formulario de actualización
    if (isset($_POST['update_categoria'])) {
        $id = intval($_POST['id']);
        $categoria = sanitize_text_field($_POST['categoria']);
        $descripcion = wp_kses_post($_POST['descripcion']);      
    
        // Actualizar la categoría en la base de datos
        
        $resultado = $wpdb->update(
            $tabla_categoria,
            ['categoria' => $categoria, 'descripcion' => $descripcion],
            ['id' => $id]
        );
        
        // Forzar la redirección aunque no haya cambios
        if ($resultado === false) {
            die("Error en la actualización: " . $wpdb->last_error);
        } else {
            wp_safe_redirect(admin_url('admin.php?page=FAQ_Categoria'));
            exit;
        }
    }
}
