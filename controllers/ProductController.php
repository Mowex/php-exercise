<?php
require_once('models/ProductModel.php');

class ProductController {
    
    private $product;    

    public function __construct(){
        $this->product = new Product();
    }

    public function index() {
        $products = $this->product->getProducts();
        require_once('views/partials/header.php');
        require_once('views/ProductsView.php');
        require_once('views/partials/footer.php');
    }
    
    public function getStock(){
        $id = $_GET['id'];
        $product = $this->product->getStockById($id);
        echo json_encode($product);
    }
    
    public function getWareHouseStock(){
        $id = $_GET['id'];
        $product = $this->product->getWareHouseStockById($id);
        echo json_encode($product);
    }
    
    public function saveSTock(){
        $data = $_POST;
        $success = $this->product->saveSTock($data);
        echo json_encode(['success' => $success]);
    }

}


