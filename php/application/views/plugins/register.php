<div class="col-9 mt-3">
<h4><?= $title ?></h4>
<?php if (isset($_SESSION['success']) && ($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
<?php endif; ?>

<?php if(isset($plugin)) {?>
    <div class="mt-3 w-100"><img class="float-right img-thumbnail" width="100px" src="<?= $plugin->image ?>" alt=""></div>
    <div class="mt-3 w-100 comentario text-secondary border-bottom text-center">
        Integrar <?php echo $plugin->nombre ?>
    </div>

    <div class="mt-3">
        Usted esta autorizando al componente a tener acceso a la información contenida en su catálogo, pedidos, imágenes y stock.
    </div>

    <div class="mt-3">
        <?php echo $plugin->descripcion ?>
    </div>

    <div class="mt-3">
        <?php echo form_open($url_register); ?>
        <?php if ($plugin->campos_registro) {  ?>
        <?php echo paintForm(json_decode($plugin->campos_registro),  isset($before) ? $before: null);  ?>
        <?php } ?>
        <div class="mt-5">
            <button class="btn btn-success" type="submit"><i class="fa fa-exclamation-triangle"> Autorizar</i></button>
            <a href="/admin/apps" class="float-right btn btn-danger"><i class="fa fa-exclamation-triangle"> Rechazar</i></a>
        </div>


        </form>
    </div>

<?php } ?>

<div id='mensaje' class="mt-3 alert-info"  role="alert"></div>    
</div>
