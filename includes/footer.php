   <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1 text-center">
                    <p class="text-muted">Copyright &copy; Hakstock 2018</p>
                    <p class="text-muted">Project Documents: <a href="http://hakeemusman.byethost9.com/hakstocks/projdocs">Click Here</a></p>
                </div>
            </div>
        </div>
    </footer>
    <!-- End of footer -->

    <script>
        // Toggle quick look modal
        function detailsmodal(id) {
            var data = {"id" : id};
            jQuery.ajax({
                url : '/includes/modaldetails.php',
                method : "post",
                data : data,
                success: function(data){
                    JOuery.noConflict();
                    alert("It works");
                    jQuery('body').append(data);
                    jQuery('#details-modal').modal('toggle');
                },
                error: function(){
                    alert("Why me Lord!");
                }
            });
        }

        function update_cart(mode,editid,editsize) {
            var data = {"mode" : mode, "editid" : editid, "editsize" : editsize};
            jQuery.ajax({
                url: <?=BASEURL;?>+'/admin/parsers/update_cart.php',
                method: "post",
                data: data,
                success: function(){
                    location.reload();
                },
                error: function() {
                    alert("Something went wrong");
                }
            });
        }

        function add_to_cart() {
            jQuery('#modal_error').html("");
            var size = jQuery('#size').val();
            var quantity = jQuery('#quantity').val();
            var available = jQuery('#available').val();
            var error = '';
            var data = jQuery('#add_form').serialize();
            if (size == '' || quantity == '' || quantity == 0) {
                error += '<p class="text-danger">Please choose a size and quantity.</p>';
                jQuery('#modal_error').html(error);
                return;
            }
            else if (quantity > available) {
                error += '<p class="text-danger">Only '+available+' in stock.</p>';
                jQuery('#modal_error').html(error);
                return;
            }
            else {
                jQuery.ajax({
                    url: <?=BASEURL;?>+'/admin/parsers/add_cart.php',
                    method: 'post',
                    data : data,
                    success: function(){
                        location.reload();
                    },
                    error: function(){
                        alert('Please work.');
                    }
                });
            }

        }

    </script>


</body>
</html>