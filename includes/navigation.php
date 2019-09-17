<!-- For dynamic navigation -->


<?php
$sql = "SELECT * FROM categories WHERE parent = 0"; // Get menu links dynamically.
$parentquery = $db->query($sql);
?>


    
    <nav class="navbar navbar-default navbar-fixed-top" id="menubar">
        <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><img src="img/logo.png"></a>
            </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">      
                <ul class="nav navbar-nav navbar-right">
                    <!-- Loop through and fetch main categories -->
                    <?php while ($parent = mysqli_fetch_assoc($parentquery)) : ?> 
                        <?php 
                        $parent_id = $parent['id']; 
                        $submenusql = "SELECT * FROM categories WHERE parent = '$parent_id'";
                        $submenuquery = $db->query($submenusql);

                        ?>
                        <li class="dropdown">
                            <!-- Echo categories dynamically from the database -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $parent['category'] ?><span class="glyphicon glyphicon-menu-down"></span></a> 
                            <ul class="dropdown-menu" role="menu">
                                <!-- Loop and Fetch submenu links from database. In this case, submenu links are children of the parent categories -->
                                <?php while ($child = mysqli_fetch_assoc($submenuquery)) : ?>
                                    <li><a href="category.php?cat=<?php echo $child['id']; ?>"><?php echo $child['category']; ?></a></li>
                                <?php endwhile; ?>
                            </ul>
                        </li>
                    <?php endwhile; ?>
                    <li><a href="cart.php"><span class="glyphicon glyphicon-shopping-cart"></span> Shopping Cart</a></li>
                    <li><a href="account.php">My Account</a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>