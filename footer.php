</div> <!-- Close the main content container -->

<footer class="bg-light text-center py-3 mt-5">
    <p>© <?php echo date("Y"); ?> The Wandering Wok. All rights reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php if (isset($extraScripts)) { echo $extraScripts; } ?>
</body>
</html>