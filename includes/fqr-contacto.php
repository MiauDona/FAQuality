<?php
// Añadimos la clase wp_list_table de wordpress y pedimos que sea requerido ya que no es publico
// (se recoge de otro enlace dentro del mismo wordpress).

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'; //ABSPATH es ruta absoluta
}

add_action('wp_ajax_actualizar_estado_atendido', 'actualizar_estado_atendido');

//Para actualizar los checkbox en la base de datos
function actualizar_estado_atendido()
{
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';

    $id = intval($_POST['id']);
    $estado = intval($_POST['estado']);

    $wpdb->update(
        $tabla_contacto,
        array('estado_atendido' => $estado),
        array('id' => $id),
        array('%d'),
        array('%d')
    );
    echo $estado;
}


//Creamos la clase categoria_list_table que al extender de wp_list_table, cogemos lo que realiza la funcion
//wp_list_table y la personalizamos 
class Categoria_List_Contacto extends WP_List_Table
{

    //Creamos un constructor con la informacion principal (ajax desactivado por ahora)
    function __construct()
    {
        parent::__construct([
            'singular' => 'contacto',
            'plural' => 'contactos',
            'ajax' => false
        ]);
    }


    function get_total_items()
    {
        global $wpdb;
        $prefijo = $wpdb->prefix . 'fqr_';
        $tabla_contacto = $prefijo . 'contacto';
        return $wpdb->get_var("SELECT COUNT(*) FROM $tabla_contacto WHERE borrado=0");
    }
    //Obtiene los datos de la base de datos 
    function get_contactos($per_page, $page_number)
    {
        global $wpdb;
        $prefijo = $wpdb->prefix . 'fqr_';
        $tabla_contacto = $prefijo . 'contacto';
        $offset = ($page_number - 1) * $per_page;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, fecha, nombre, email, mensaje, FK_idfaq, estado_atendido FROM $tabla_contacto WHERE borrado=0 LIMIT %d OFFSET %d",
                $per_page,
                $offset
            ),
            ARRAY_A
        );
    }

    //Cargamos datos en las columnas
    function prepare_items()
    {
        $per_page = 15;
        $current_page = $this->get_pagenum();
        $total_items = $this->get_total_items();

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
        ]);

        $this->items = $this->get_contactos($per_page, $current_page);

        $columns = $this->get_columns();  // Obtiene las columnas definidas antes
        $hidden = [];                    // Columnas ocultas (vacío porque mostramos todas)
        $sortable = [];                    // Columnas ordenables (no usamos ordenamiento)
        $this->_column_headers = [$columns, $hidden, $sortable];
    }

    //Creamos nuestras columnas (indicamos el tipo de columna que queremos y despues le ponemos nombre)    
    function get_columns()
    {
        return [
            'fecha' => 'Fecha',
            'nombre' => 'Nombre',
            'email' => 'Email',
            'FK_idfaq' => 'De la pregunta:',
            'mensaje' => 'Mensaje',
            'estado' => 'Estado',
            'acciones' => 'Acciones'
        ];
    }

    //Agregamos contenido a las columnas

    //Generamos hueco para checkbox indicando que el valor de cada checbox es igual a su id
//Y si check box es 1, lo marcamos, pero si esta en 0, no esta marcado
    function column_estado($item)
    {
        return sprintf(
            '<input type="checkbox" class="checkbox_estado" data-id="%s" %s />',
            $item['id'],
            $item['estado_atendido'] ? 'checked' : ''
        );
    }

    //Generamos hueco para nombre con enlace externo y le da el efecto cliqueable
    function column_nombre($item)
    {
        return esc_html($item['nombre']);
    }

    function column_mensaje($item) {
        return esc_html($item['mensaje']);
    }

    //Generamos hueco para email    
    function column_email($item)
    {
        return esc_html($item['email']);
    }

    //Generamos hueco para la fecha
    function column_fecha($item)
    {                                           //Cambiamos el formato de fecha 
        return date("d-m-Y", strtotime($item['fecha']));
    }

    //Generamos hueco para la clave foranea
    function column_FK_idfaq($item)
    {
        global $wpdb;
        $prefijo = $wpdb->prefix . 'fqr_';  // Asegúrate de que este prefijo es correcto
        $tabla_faq = $prefijo . 'faq';  // Nombre de la tabla que contiene las preguntas
        $fkidfaq = $item['FK_idfaq'];  // Obtenemos el valor de la ID de la pregunta

        // Hacemos la consulta para obtener la pregunta relacionada con esta ID
        $pregunta = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT pregunta FROM $tabla_faq WHERE id = %d",
                $fkidfaq
            )
        );

        // Verificamos si hemos obtenido una pregunta
        if ($pregunta) {
            return esc_html($pregunta);  // Devolvemos la pregunta con seguridad (escapada para evitar XSS)
        } else {
            return 'Pregunta no encontrada';  // En caso de que no haya una pregunta asociada
        }
    }


    //Agrega botones de acción en la columna "Acciones" 
    function column_acciones($item)
    {
        $delete_link = '?page=Contactos&action=delete&id=' . $item['id'];
        return sprintf(
            '<a href="%s" onclick="return confirm(\'¿Estás seguro?\')">❌ Eliminar</a>',
            esc_url($delete_link)
        );
    }
}



//Muestra la tabla en la pagina con los datos que agregamos anteriormente
function FAQuality_contact_page()
{
    function FAQuality_selection_contact_page()
    {
        require_once 'bbdd.actions.php';

        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            dbMarkAsDeletedContact($_GET['id']);
        }
    }

    FAQuality_selection_contact_page();

    echo '<div class="wrap"><h1>Contactos</h1>';
    $categoria_table = new Categoria_List_Contacto();
    $categoria_table->prepare_items();
    $categoria_table->display();
    echo '</div>';
    ?>

    <!-- Javascript para los checkbox, usando ajax para poder guardar la informacion de forma constante -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Carga la biblioteca Jquery -->
    <script>
        jQuery(document).ready(function ($) {
            $(".checkbox_estado").on("change", function () { //Comprobamos si hay algun cambio en el checkbox
                var id = $(this).data("id"); //Guardamos en variable el id delc checkbox cambiado
                var estado = $(this).is(":checked") ? 1 : 0; //Guardamos el estado del checkbox

                $.ajax({ //Para mandar informacion por ajax (que es lo que guarda de forma constante)
                    url: "<?php echo admin_url('admin-ajax.php'); ?>", //Coge de la libreria el ajax
                    type: "POST", //Indicamos el tipo de actualizacion que es POST (hacer consulta a la base)
                    data: {
                        action: "actualizar_estado_atendido",
                        id: id,
                        estado: estado
                    },
                    success: function (response) { //Si se realiza manda por consola un succes
                        console.log("Estado actualizado: " + response);
                    }
                });
            });
        });
    </script>
    <?php
}




