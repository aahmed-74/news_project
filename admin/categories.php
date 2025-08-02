<?php
session_start();
//if(isset($_SESSION['auth_message'])){
include("init.php");
$page="all";
if(isset($_GET["page"])){
    $page = $_GET["page"];
}
if($page=='all'){
$statment = $connect->prepare("SELECT * FROM categories");
$statment->execute();
$ucount = $statment->rowCount();
$result = $statment->fetchAll();
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 m-auto">
            <?php
            if(isset($_SESSION['message'])){
                echo"<h2 class='alert alert-success text-center'>".$_SESSION['message']."</h2>";
                unset($_SESSION["message"]);
                header("Refresh:3;url=categories.php");
            }
            ?>
            <h2 class="text-center">Details of categories
                <span class="btn btn-primary"><?php echo $ucount;?></span>
                <a href="?page=create" class="btn btn-success">Add new category</a>
            </h2>
            <table class="table table-dark">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Created_at</th>
                    <th>Operation</th>
                </tr>
                </thead>
                <tbody class="text center">
                    <?php
                    foreach ($result as $item) {
                    ?>
                    <tr>
                        <td><?php echo $item['categories_id'] ?></td>
                        <td><?php echo $item['name'] ?></td>
                        <td><?php echo $item['created_at'] ?></td>
                        <td>
                        <a href="?page=show&categories=<?php echo $item['categories_id']?>" class="btn btn-success"><i class="fa-solid fa-eye"></i></a>
                        <a href="?page=edit&categories=<?php echo $item['categories_id'] ?>" class="btn btn-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="?page=delete&categories=<?php echo $item['categories_id'];?>" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
}elseif($page== 'show'){
    if(isset($_GET['categories'])){
        $categories = $_GET['categories'];
    }
    $statment = $connect->prepare('SELECT * FROM categories WHERE categories_id=?');
    $statment->execute(array($categories));
    $ucount = $statment->rowCount();
    $result = $statment->fetch();
    ?>
    <div class="container mt-5">
    <div class="row">
        <div class="col-md-10 m-auto">
            <h2 class="text-center">Details of categories
                <span class="btn btn-primary"><?php echo $ucount;?></span>
            </h2>
            <table class="table table-dark">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Created_at</th>
                    <th>Operation</th>
                </tr>
                </thead>
                <tbody class="text center">
                        <td><?php echo $result['categories_id'] ?></td>
                        <td><?php echo $result['name'] ?></td>
                        <td><?php echo $result['created_at'] ?></td>
                        <td>
                        <a href="categories.php" class="btn btn-success"><i class="fa-solid fa-house"></i></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
}elseif($page== 'delete'){
    if(isset($_GET['categories'])){
        $categories = $_GET['categories'];
    }
    $statment = $connect->prepare('DELETE FROM categories WHERE categories_id=?');
    $statment->execute(array($categories));
    $_SESSION['message']="Deleted Successfully";
    header('Location:categories.php');
}elseif($page == 'create'){
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 m-auto">
            <h3 class="text-center">Add new category</h3>
            <form action="?page=savecreate" method="post">
                <label>ID</label>
                <input type="text" name="id" class="form-control mb-3"
                placeholder="<?php
                if(isset($_SESSION['error_id'])){
                     echo $_SESSION['error_id'];
                     unset($_SESSION['error_id']);
                }?>">
                <label>Name</label>
                <input type="text" name="name" class="form-control mb-3" 
                value="<?php
                if(isset($_SESSION['error_name'])){
                     echo $_SESSION['error_name'];
                     unset($_SESSION['error_name']);
                }
                ?>">
                <input type="submit" name="submit" value="Insert" class="btn btn-success form-control mb-3">
            </form>
            </div>
        </div>
    </div>
    
<?php
}elseif($page== 'savecreate'){
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $id = $_POST['id'];
        $name = $_POST['name'];
        try{
        $statment= $connect->prepare('INSERT INTO
        categories(categories_id,`name`,created_at)
        VALUES(?,?,now())
        ');
        $statment->execute(array($id, $name));
        $_SESSION['message']='Created Successfully';

        header('Location:categories.php');
        }catch(PDOException $e){
            $_SESSION['error_id']='Enter another id';
            $_SESSION['error_name']=$name;
            echo"<h4 class='alert alert-danger text-center'>Duplicated ID</h4>";
            header("Refresh:3;url=categories.php?page=create");

        }
    }
}elseif($page== "edit"){
    if(isset($_GET['categories'])){
        $categories = $_GET['categories'];
    }
    $statment=$connect->prepare('SELECT * FROM categories WHERE categories_id=?');
    $statment->execute(array($categories));
    $result=$statment->fetch();
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 m-auto">
            <h3 class="text-center">Edit user</h3>
            <form action="?page=saveedit&old_id=<?php echo $result['categories_id'];?>" method="post">
                <label>ID</label>
                <input type="text" name="new_id" class="form-control mb-3" value="<?php echo $result['categories_id'];?>">
                <label>Name</label>
                <input type="text" name="name" class="form-control mb-3" value="<?php echo $result['name'];?>">
                <input type="submit" name="submit" value="Insert" class="btn btn-success form-control mb-3">
            </form>
            </div>
        </div>
    </div>
    <?php
}elseif($page== "saveedit"){

        if(isset($_GET["old_id"])){
            $old_id = $_GET["old_id"];
        }
        if($_SERVER["REQUEST_METHOD"]== "POST"){
            $new_id=$_POST["new_id"];
            $name = $_POST["name"];
            try{
                $statment=$connect->prepare("UPDATE categories SET
                categories_id=?,
                `name`=?
                WHERE categories_id=?
                ");
                $statment->execute(array($new_id, $name, $old_id));
                $_SESSION['message']='Edit succesfully';
                header('Location:categories.php');
            }catch(PDOException $e){
                echo "<h4 class='alert alert-danger text-center'>Dublicated ID</h4>";
                header("Refresh:3;url=categories.php?page=edit&categories=$old_id");
            }

        }
}
?>
<?php
include("includes/temp/footer.php");
//}else{
//    $_SESSION['message_login'] ='Enter data first';
 //   header('Location:../login.php');
//}
?>