    </div>
    <div class="footer">
      <div class="container">
        <p class="text-muted">
        &copy; <?=date('Y')?>
        <?php
if($username){
?>

		<a class="pull-right" href="<?=APP_BASE_PATH?>/logout">退出登陆</a>
<?php
}
?>
        </p>
      </div>
    </div>
<?php
/*
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
<!--
    <script src="<?=$assets_path ?>/external/jquery/dist/jquery.min.js"></script>
    <script src="<?=$assets_path ?>/external/bootstrap/dist/js/bootstrap.min.js"></script>
-->
*/
?>
  </body>
</html>
