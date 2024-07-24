<?php
// footer.php
?>

</main>
<?php
if (is_admin_login()) {
?>
    <footer class="py-4 mt-auto" style="background-color: #f8f9fa;">
        <div style="padding: 15px;
    min-height: 30px;"class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between small">
                <div class="text-muted footer">Copyright &copy; Library Management System <?php echo date('Y'); ?></div>
                <div>
                    <a href="#">Privacy Policy</a>
                    &middot;
                    <a href="#">Terms &amp; Conditions</a>
                </div>
            </div>
        </div>
    </footer>
</div>
<?php
} else {
?>
    <footer class="pt-3 mt-4 text-muted text-center border-top footer" ">
        &copy; <?php echo date('Y'); ?> Library Management System
    </footer>
</div>
<?php 
}
?>

<script src="<?php echo base_url(); ?>asset/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="<?php echo base_url(); ?>asset/js/scripts.js"></script>
<script src="<?php echo base_url(); ?>asset/js/simple-datatables@latest.js" crossorigin="anonymous"></script>
<script src="<?php echo base_url(); ?>asset/js/datatables-simple-demo.js"></script>
</body>
</html>
