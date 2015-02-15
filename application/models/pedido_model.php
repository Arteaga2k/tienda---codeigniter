<?php

/**
 * 
 * @author Carlos
 *
 */
class Pedido_model extends CI_Model
{

    /**
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Devuelve un pedido determinado por su id
     *
     * @param unknown $id            
     */
    public function getPedidoById($id)
    {
        $this->db->where('idPedido', $id);
        $pedido = $this->db->get('pedido');
        
        return $pedido->row_array();
    }

    /**
     * Devuelve historial pedidos procesados
     * de un determinado usuario
     *
     * @param unknown $id            
     * @return unknown
     */
    public function getPedidos($id)
    {
        $this->db->where('idUsuario', $id);
        $this->db->where('estado', 1);
        
        $pedidos = $this->db->get('pedido');
        
        return $pedidos->result_array();
    }

    /**
     * Devuelve pedidos no procesados
     * de un determinado usuario
     *
     * @param unknown $id            
     * @return unknown
     */
    public function getPedsNoProcesados($id)
    {
        $this->db->where('idUsuario', $id);
        $this->db->where('estado', 0);
        
        $pedidos = $this->db->get('pedido');
        
        return $pedidos->result_array();
    }

    /**
     * Crea un pedido en la bbdd
     *
     * @param unknown $data            
     */
    public function creaPedido($data)
    {
        $this->db->insert('pedido', $data);
        
        return $this->db->insert_id();
    }

    public function eliminaPedido()
    {}

    public function editaPedido()
    {}

    /**
     * 
     * @param unknown $carrito
     * @param unknown $idPedido
     */
    public function insertaLineas($carrito,$idPedido){
     $data = array();
     foreach ($carrito['items'] as $idProducto => $item) {            
            $data[] = array(
                'idProducto' => $idProducto,
                'idPedido' => $idPedido,
                'cantidad' => $item['cantidad'],
                'precio' => $item['precio']
            );         
        } 
       $this->db->insert_batch('linea_pedido',$data);
        
    }
    
    /**
     * Devuelve las líneas de un pedido
     * determinado por su id
     * 
     * @param unknown $id
     */
    public function getLineasById($id)
    {
        $consulta = "select pe.*, p.*,l.*
        from pedido pe
        inner join linea_pedido l on l.idPedido = pe.idPedido
        inner join producto p on l.idProducto = p.idProducto
        where l.idPedido = $id";
        
        $lineas = $this->db->query($consulta);
        
        return $lineas->result_array();
    }
}