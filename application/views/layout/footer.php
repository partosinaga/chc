</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 <?php echo date('Y');?> &copy; The Pakubuwono Development. <?php echo (SERVER_IS_PRODUCTION ? '<span class="label label-success">LIVE DATABASE</span>' : '<span class="label font-yellow pulsate_blink"> DEVELOPMENT SERVER (<i>data lost may sometimes occur due to Development purposes.</i>)</span>'); ?>

	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!-- END FOOTER -->

<!-- END FOOTER LEVEL SCRIPTS -->
<?php
if(isset($footer_script)){
    if(count($footer_script) > 0){
        foreach($footer_script as $row_footer_script){
            echo '<script type="text/javascript" src="' . $row_footer_script . '"></script>';
        }
    }
}
?>
<!-- END FOOTER LEVEL SCRIPTS -->
<script>
    $(document).ready(function(){
        /*
        window.location.hash="nob"; //no-back-button
        window.location.hash="Again-Nob";//Again-No-back-button
        window.onhashchange=function(){window.location.hash="nob";} //no-back-button
        */

        jQuery('.pulsate_blink').pulsate({
            color: "#bf1c56"
        });
        jQuery('.pulsate_blink_yellow').pulsate({
            color: "#FF5722"
        });

    });

</script>
</body>
<!-- END BODY -->

</html>