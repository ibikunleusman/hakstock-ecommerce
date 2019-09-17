    
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
                <a class="navbar-brand" href="index.php"><img src="../img/logo.png"></a>
            </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">      
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="brands.php">Manage Brands</a></li>
                    <li><a href="categories.php">Manage Categories</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Manage Products <span class="glyphicon glyphicon-menu-down"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="products.php">Manage Products</a></li>
                            <li><a href="deletedproducts.php">Deleted Products</a></li>
                        </ul>
                    </li>
                    <?php if (has_permission('admin')): ?>
                        <li><a href="users.php">Manage Users</a></li>
                    <?php endif; ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <span class="glyphicon glyphicon-menu-down"></span></a>
                        <ul class="dropdown-menu" role="menu">
                           <li class="disabled">
                                <a href="#">
                                    <strong>Signed in as</strong><br><?php echo $userdata['first']; ?>
                                </a>
                           </li>
                           <li><a href="edit_admin.php">Edit Profile</a></li>
                           <li><a href="logout.php">Sign out</a></li>
                        </ul>
                    </li>
                    <!-- <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $parent['category'] ?><span class="glyphicon glyphicon-menu-down"></span></a> 
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#"><?php echo $child['category']; ?></a></li>
                        </ul>
                    </li> -->
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>