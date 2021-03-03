<body style="background-color: grey;">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Inventarios</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="#">Productos <span class="sr-only">(current)</span></a>
            </li>        
        </ul>
    </div>
    </nav>
    <div class="container py-3">
        <div class="row">
            <div class="col-12 text-center py-3">
                <h1>Productos</h1>
            </div>
        </div>

        <table class="table table-hover table-striped table-bordered table-dark">
            <thead>
                <tr class="text-center">
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Color</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr class="text-center">
                        <th><?= $product['sku'] ?></th>
                        <th><?= $product['descripcion'] ?></th>
                        <th><?= $product['marca'] ?></th>
                        <th><?= $product['color'] ?></th>
                        <th>$<?= number_format($product['precio'], 2, '.', ',') ?></th>
                        <th>
                            <button class="btn btn-primary show" id="<?= $product['id'] ?>">Ver</button>
                            <button class="btn btn-primary edit" product_id="<?= $product['id'] ?>" >Editar</button>
                        </th>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="getStock" tabindex="-1" aria-labelledby="getStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="getStockLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12" id="showStock">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="editStock" tabindex="-1" aria-labelledby="editStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStockLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12" id="stock">
                            sss
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveStock">Guardar</button>
            </div>
            </div>
        </div>
    </div>
</body>

<script>
    const getStock = function(id) {
        $.ajax({
            url: "?c=Product&a=getStock&id="+id,
            type: "GET",
            beforeSend: function(){
                
            },
            success: function(data){
                data = JSON.parse(data);
                if (data.length > 0) {
                    $('#getStockLabel').html(data[0].descripcion);
                    let html = `
                        <table class="table table-hover table-striped table-bordered table-dark">
                            <thead>
                                <tr class="text-center">
                                    <th>Almacen</th>
                                    <th>Localización</th>
                                    <th>Encargado</th>
                                    <th>Existencias</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>`;

                    data.map(({nombre_almacen, localizacion, responsable, existencias, tipo}) => {
                        html += `
                            <tr class="text-center">
                                <th>${nombre_almacen}</th>
                                <th>${localizacion}</th>
                                <th>${responsable}</th>
                                <th>${existencias}</th>
                                <th>${tipo === 1 ? 'Virtual' : 'Tienda Física'}</th>
                            </tr>
                        `;
                    });
                    html += `</tbody></table>`;

                    $('#showStock').html(html);
                }
            },
            error: function(xhr, status, thrownError) {
                
            },
            complete: function(){ 
                
            }
        });
    }
   
    const getWareHouseStock = function(id) {
        $.ajax({
            url: "?c=Product&a=getWareHouseStock&id="+id,
            type: "GET",
            beforeSend: function(){
                
            },
            success: function(data){
                data = JSON.parse(data);
                if (data.length > 0) {
                    $('#editStockLabel').html(data[0].descripcion);
                   let html = `
                   <table class="table table-hover table-striped table-bordered table-dark">
                        <thead>
                            <tr class="text-center">
                                <th>Almacen</th>
                                <th>Existencia</th>                                
                            </tr>
                        </thead>
                        <tbody>`;
                    data.map(({nombre_almacen, existencias, producto_id, almacen_id}) => {
                        html += `
                            <tr class="text-center">
                                <th>${nombre_almacen}</th>
                                <th><input type="number" class="input-stock" stock_id="${almacen_id}" product_id="${id}" value="${!!existencias ? existencias : 0}"/></th>
                            </tr>
                        `;
                    });
                    $('#stock').html(html);
                }
            },
            error: function(xhr, status, thrownError) {
                
            },
            complete: function(){ 
                
            }
        });
    }



    $(document).ready(function() {
        $('.show').on('click', function(){
            let id = $(this).attr('id');
            $('#getStock').modal('toggle');
            getStock(id);
        });
        
        $('.edit').on('click', function(){
            let id = $(this).attr('product_id');
            $('#editStock').modal('toggle');
            getWareHouseStock(id);
        });
        
        $('#saveStock').on('click', function(){
            var multi = $('.input-stock');
            var data = [];

            $.each(multi, function (index, item) {
                data.push({
                    product_id: $(item).attr('product_id'), 
                    stock_id: $(item).attr('stock_id'),
                    stock: $(item).val()
                });  
            });

            $.ajax({
                url: "?c=Product&a=saveStock",
                type: "POST",
                data: {data},
                beforeSend: function(){
                    
                },
                success: function(data){
                    data = JSON.parse(data);
                    if (data.success){
                        alert('Inventario actualizado correctamente');
                    }
                },
                error: function(xhr, status, thrownError) {
                    
                },
                complete: function(){ 
                    
                }
            });


        });
    });
</script>