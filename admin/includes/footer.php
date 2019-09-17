   <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1 text-center">
                    <p class="text-muted">Copyright &copy; Hakstock 2016</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- End of footer -->

    <script>
        function updateSize() {
            var sizeString = '';
            for (var i = 1; i <= 12; i++) {
                if (jQuery('#sizes'+i).val() != '') {
                    sizeString += jQuery('#sizes'+i).val()+':'+jQuery('#qty'+i).val()+':'+jQuery('#threshold'+i).val()+',';
                }
            }
            jQuery('#size').val(sizeString);
        }

        // This function dynamically retrieves the correct sub-categories of each parent category.
        function get_sub_categories(selected) {
            if (typeof selected == 'undefined') {
                var selected = '';
            }
            var parentID = jQuery('#parent').val();
            jQuery.ajax({
                url: '/hakstocks/admin/parsers/sub_categories.php',
                type: 'POST',
                data: {parentID : parentID, selected : selected},
                success: function(data){
                    jQuery('#child').html(data);
                },
                error: function(){alert("Error retrieving sub-categories.")},
            });
        }

        jQuery('select[name="parent"]').change(function(){
            get_sub_categories();
        });

    </script>
</body>
</html>