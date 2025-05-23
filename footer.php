</div> <!-- end of #page-content-wrapper -->
</div> <!-- end of #wrapper -->

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3" >
  <div class="container">
    <small>&copy; <?= date('Y') ?> Faculty of Technology, University of Colombo</small>
  </div>
</footer>


<!-- Bootstrap JS -->
<script>
  document.getElementById('menu-toggle').addEventListener('click', function () {
    document.getElementById('sidebar-wrapper').classList.toggle('active');
    document.getElementById('page-content-wrapper').classList.toggle('collapsed');
    document.getElementById('overlay').classList.toggle('active');
  });

  document.getElementById('overlay').addEventListener('click', function () {
    document.getElementById('sidebar-wrapper').classList.remove('active');
    document.getElementById('page-content-wrapper').classList.remove('collapsed');
    this.classList.remove('active');
  });
</script>


</body>
</html>
