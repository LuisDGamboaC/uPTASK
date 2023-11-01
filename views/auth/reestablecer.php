<div class="contenedor reestablecer">
<?php include_once __DIR__ .'/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu nuevo password</p>
        <?php include_once __DIR__ .'/../templates/alertas.php'; ?>
        <?php if($mostrar) { ?>

        <form class="formulario" method="POST"> 
            <!-- no debe llevar el action porque la url tiene el token y el action hace que se pierda la referencia del token porque la manda a la url /reestablecer-->

            <div class="campo">
                <label for="password">Password</label>
                <input type="password" id="password" placeholder="Tu Password" name="password">
            </div>

            <div class="campo">
                <label for="password2">Repetir Password</label>
                <input type="password" id="password2" placeholder=" Repite tu Password" name="password2">
            </div>

            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>

        <?php } ?>
        <div class="acciones">
            <a href="/">Ya tienes una cuenta? Iniciar Sesión</a>
            <a href="/crear">¿Aún no tienes una cuenta? Obtener una</a>
        </div>
    </div> <!--.contenedor-sm-->
</div>