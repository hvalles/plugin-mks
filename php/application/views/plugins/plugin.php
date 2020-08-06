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
        <?php echo $plugin->descripcion ?>
    </div>

    <div class="mt-3">
        <?php echo $plugin->instrucciones ?>
    </div>
    <div class="mt-3">
        <?php echo form_open($url_run); ?>
        <?php if ($plugin->campos) {  ?>
        <?php echo paintForm(json_decode($plugin->campos),  isset($before) ? $before: null);  ?>
        <?php } ?>
        <div class="mt-5">
            <button class="btn btn-primary" type="submit">Ejecutar</button>
            <a href="/admin/apps" class="float-right btn btn-danger"><i class="fa fa-exclamation-triangle">Cancelar</i></a>
        </div>


        </form>
    </div>

<?php } ?>

<div id='mensaje' class="mt-3 alert-info"  role="alert"></div>    
</div>
