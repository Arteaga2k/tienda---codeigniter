<?php

/**
 * Clase: Home
 * 
 * Controlador Home, pantalla principal, muestra productos especiales y categor�as
 * 
 * @author 2DAWT
 *
 */
class Home extends CI_Controller
{

    /**
     * Constructor clase Home
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('home_model');
    }

    /**
     * PÁGINA: index
     *
     * http://tienda/home
     */
    public function index()
    {
        
        // echo $this->categoria;
        $pagination = 5;
        $config['base_url'] = base_url() . 'home/index/';
        $config['total_rows'] = $this->home_model->getTotalProdDestacados();
        $config['per_page'] = $pagination;
        $config["uri_segment"] = 3; // el segmento de la paginación
        $config['num_links'] = 5;
        
        $this->pagination->initialize($config);
        
        $segmento = ! empty($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        
        $categorias = $this->home_model->getCategorias();
        $productosDest = $this->home_model->getProductosDestacados($pagination, $segmento);
        
        echo $this->twig->render('home/index.twig', array(
            'categorias' => $categorias->result_array(),
            'productos' => $productosDest->result_array(),
            'pagination' => $this->pagination->create_links(),
            'carrito' => $this->carrito->getCarrito()
        ));
        
        // $this->carrito->destroy();
        // todo prueba
        //$this->ajaxAddCart(10,5);
    }

    /**
     * PÁGINA: categoria
     *
     * http://tienda/categoria/{id}
     *
     * @param string $categoria            
     */
    public function categoria($idCategoria)
    {
        $pagination = 5;
        $config['base_url'] = base_url() . 'home/categoria/' . $idCategoria . '';
        $config['total_rows'] = $this->home_model->getTotalProductos($idCategoria);
        $config['per_page'] = $pagination;
        $config["uri_segment"] = 4; // el segmento de la paginación
        $config['num_links'] = 5;
        
        $this->pagination->initialize($config);
        
        $segmento = ! empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        
        $categorias = $this->home_model->getCategorias();
        $productosDest = $this->home_model->getProductos($idCategoria, $pagination, $segmento);
        
        echo $this->twig->render('home/index.twig', array(
            'categorias' => $categorias->result_array(),
            'productos' => $productosDest->result_array(),
            'pagination' => $this->pagination->create_links(),
            'carrito' => $this->carrito->getCarrito()
        ));
    }

    /**
     * PÁGINA: producto
     *
     * http://tienda/home/producto/{id}
     *
     * @param string $id            
     */
    public function producto($idProducto)
    {
        $categorias = $this->home_model->getCategorias();
        $producto = $this->home_model->getProducto($idProducto);
        
        $this->form_validation->set_rules('cantidad', 'Cantidad', 'trim|required|min_length[1]|numeric|xss_clean');
        $form["form_open"] = form_open("", array(
            "class" => "form-inline"
        ));
        $form['error_cantidad'] = form_error('cantidad');
        echo form_error('cantidad');
        
        // Comprueba validación formulario
        if ($this->form_validation->run() == FALSE) {
            echo $this->twig->render('home/producto.twig', array(
                'categorias' => $categorias->result_array(),
                'producto' => $producto,
                'form' => $form,
                'carrito' => $this->carrito->getCarrito()
            ));
        } else {
            // añadimos producto al carro
            $this->addProducto();
        }
    }
    
    /**
     * PÁGINA: carro
     * 
     * http://tienda/home/carro
     */
    public function verCarro(){
        
        $carrito = $this->carrito->getCarrito();
        var_dump($carrito);
        echo $this->twig->render('home/carro.twig', array( 
            'carrito' => $this->carrito->getCarrito()
        ));
    }
    
    // probando ajax
    public function ajaxAddCart($cantidad, $idproducto)
    {
        
        // echo "cantidad: [$cantidad]\n";
        $producto = $this->home_model->getProducto($idproducto);
        $carrito = $this->carrito->getCarrito();
        
       
        if ($carrito['items'][$idproducto]['cantidad'] <= $producto->stock) {         
            $cantidadFinal = $producto->stock >= $cantidad ? $cantidad : $producto->stock;
            
            $this->carrito->InsertarItem(array(
                'id' => $idproducto,
                'cantidad' => $cantidadFinal,
                'precio' => $producto->precio,
                'nombre' => $producto->nombre
            ));
        }        
       
        echo json_encode($carrito);
    }
}