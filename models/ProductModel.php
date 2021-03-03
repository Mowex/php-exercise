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
}