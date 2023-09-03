<?php if(!class_exists('Rain\Tpl')){exit;}?><!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Lista de Produtos
  </h1>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
  	<div class="col-md-12">
  		<div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Editar Produto</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="/admin/products/<?php echo htmlspecialchars( $product["idproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" method="post" enctype="multipart/form-data">
          <div class="box-body">
            <div class="form-group">
              <label for="product">Nome da produto</label>
              <input type="text" class="form-control" id="product" name="product" placeholder="Digite o nome do produto" value="<?php echo htmlspecialchars( $product["product"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="form-group">
              <label for="price">Preço</label>
              <input type="number" class="form-control" id="price" name="price" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars( $product["price"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="form-group">
              <label for="width">Largura</label>
              <input type="number" class="form-control" id="width" name="width" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars( $product["width"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="form-group">
              <label for="height">Altura</label>
              <input type="number" class="form-control" id="height" name="height" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars( $product["height"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="form-group">
              <label for="length">Comprimento</label>
              <input type="number" class="form-control" id="length" name="length" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars( $product["length"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="form-group">
              <label for="weight">Peso</label>
              <input type="number" class="form-control" id="weight" name="weight" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars( $product["weight"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="form-group">
              <label for="url">Url</label>
              <input type="text" class="form-control" id="url" name="url" value="<?php echo htmlspecialchars( $product["url"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="form-group">
              <label for="file">Foto</label>
              <input type="file" class="form-control" id="file" name="file">
              <div class="box box-widget">
                <div class="box-body">
                  <img class="img-responsive" id="image-preview" src="<?php echo htmlspecialchars( $product["photo"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" alt="Photo">
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </form>
      </div>
  	</div>
  </div>

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
document.querySelector('#file').addEventListener('change', function(){
  
  var file = new FileReader();

  file.onload = function() {
    
    document.querySelector('#image-preview').src = file.result;

  }

  file.readAsDataURL(this.files[0]);

});
</script>