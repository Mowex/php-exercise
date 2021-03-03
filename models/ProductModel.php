<?php

class Product {
    private $conn;
    private $product;
    private $products;

    public function __construct(){
        $this->conn = new Conn();
        $this->product = null;
        $this->products = [];
    }

    public function getProducts(){
        $sql = "SELECT id, sku, descripcion, marca, color, precio FROM productos order by 1";
        $this->products = $this->conn->query($sql);
        return $this->products;
    }
    
    public function getStockById($id){
        $sql = "SELECT p.descripcion, a.nombre_almacen, 
                a.localizacion, a.responsable, a.tipo, e.existencias 
                FROM existencias e
                JOIN productos p ON p.id = e.id_producto
                JOIN almacenes a ON a.id = e.id_almacen
                WHERE p.id = ".$id;
        $this->products = $this->conn->query($sql);
        return $this->products;
    }

    public function getWareHouseStockById($id) {
        $sql = "SELECT p.id as producto_id, a.id as almacen_id, 
                a.nombre_almacen, p.descripcion, e.existencias 
                FROM existencias e
                JOIN productos p ON p.id = e.id_producto and p.id = ".$id."
                RIGHT JOIN almacenes a ON a.id = e.id_almacen";
        $this->products = $this->conn->query($sql);
        return $this->products;
    }
    
    public function saveSTock($data) {
        
        foreach ($data['data'] as $el) {
            $sql = "SELECT count(*) 
                    FROM existencias 
                    WHERE id_almacen = ".$el['stock_id']." AND id_producto = ".$el['product_id'];
            $scalar = $this->conn->scalar($sql);

            if ($scalar > 0){
                // update
                $sql = "UPDATE existencias SET existencias = ".$el['stock']." WHERE id_almacen = ".$el['stock_id']." AND id_producto = ".$el['product_id'];
                $this->conn->addSQL($sql);
            } else if ($scalar <= 0 && $el['stock'] > 0) {
                // insert
                $sql = "INSERT INTO existencias( id_producto, id_almacen, existencias) 
                        VALUES (".$el['product_id'].", ".$el['stock_id'].", ".$el['stock'].")";
                $this->conn->addSQL($sql); } 
        }
        $this->conn->set_data($sql);
        return true;
    }

}