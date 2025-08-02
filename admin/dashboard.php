<?php
session_start();
    include("init.php");
    
    $statment2=$connect->prepare("SELECT * FROM categories");
    $statment2->execute();
    $catCount=$statment2->rowCount();
    
    $statment3=$connect->prepare("SELECT * FROM news");
    $statment3->execute();
    $newsCount=$statment3->rowCount();
    
    ?>
    
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-6 text-center" >
                <div class="box pt-5 pb-5">
                <i class="fa-solid fa-shapes fa-2xl"></i>
                <h3 class="my-3">Categories</h3>
                <h5><?php echo $catCount; ?></h5>
                <a href="categories.php" class="btn btn-warning"> Show</a>
                </div>
            </div>
            <div class="col-md-6 text-center" >
                <div class="box pt-5 pb-5">
                <i class="fa-regular fa-address-card fa-2xl"></i>
                <h3 class="my-3">News</h3>
                <h5><?php echo $newsCount; ?></h5>
                <a href="news.php" class="btn btn-primary"> Show</a>
                </div>
            </div>

<?php
include("includes/temp/footer.php");

?>