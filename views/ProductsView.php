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
                        </th>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
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
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
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

    $(document).ready(function() {
        $('.show').on('click', function(){
            let id = $(this).attr('id');
            $('#exampleModal').modal('toggle');
            getStock(id);
        });
    });
</script>