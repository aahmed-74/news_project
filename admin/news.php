<?php
session_start();
include("init.php");
$page="all";
if(isset($_GET["page"])){
    $page = $_GET["page"];
}
if($page=='all'){
$statment = $connect->prepare("
    SELECT n.news_id, n.title, n.created_at, 
           GROUP_CONCAT(c.name SEPARATOR ', ') AS category_names
    FROM news n
    LEFT JOIN news_category nc ON n.news_id = nc.news_id
    LEFT JOIN categories c ON c.categories_id = nc.category_id
    GROUP BY n.news_id
    ORDER BY n.news_id DESC
    ");
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
                header("Refresh:3;url=news.php");
            }
            ?>
            <h2 class="text-center">Details of news
                <span class="btn btn-primary"><?php echo $ucount;?></span>
                <a href="?page=create" class="btn btn-success">Add new news</a>
            </h2>
            <table class="table table-dark">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Categories</th>
                    <th>Created_at</th>
                    <th>Operation</th>
                </tr>
                </thead>
                <tbody class="text center">
                    <?php
                    foreach ($result as $item) {
                    ?>
                    <tr>
                        <td><?php echo $item['news_id'] ?></td>
                        <td><?php echo $item['title'] ?></td>
                        <td><?php echo $item['content'] ?></td>
                        <td><?php echo $item['created_at'] ?></td>
                        <td>
                        <a href="?page=show&news=<?php echo $item['news_id']?>" class="btn btn-success"><i class="fa-solid fa-eye"></i></a>
                        <a href="?page=edit&news=<?php echo $item['news_id'] ?>" class="btn btn-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="?page=delete&news=<?php echo $item['news_id'];?>" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a>
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
    if(isset($_GET['news'])){
        $news = $_GET['news'];
    }
    $statment = $connect->prepare("SELECT * FROM news WHERE news_id = ?");
    $statment->execute(array($news));
    $ucount = $statment->rowCount();
    $result = $statment->fetch();
    $statment2 = $connect->prepare("
            SELECT c.name FROM categories c
            JOIN news_category nc ON c.categories_id = nc.category_id
            WHERE nc.news_id = ?
        ");
    $statment2->execute(array($news));
    $result = $statment->fetchAll();
    ?>
    <div class="container mt-5">
    <div class="row">
        <div class="col-md-10 m-auto">
            <h2 class="text-center">Details of news
                <span class="btn btn-primary"><?php echo $ucount;?></span>
            </h2>
            <table class="table table-dark">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Categories</th>
                    <th>Created_at</th>
                    <th>Operation</th>
                </tr>
                </thead>
                <tbody class="text center">
                        <td><?php echo $item['news_id'] ?></td>
                        <td><?php echo $item['title'] ?></td>
                        <td><?= implode(', ', $categories) ?></td>
                        <td><?php echo $item['content'] ?></td>
                        <td><?php echo $item['created_at'] ?></td>
                        <td>
                        <a href="news.php" class="btn btn-success"><i class="fa-solid fa-house"></i></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
}elseif($page== 'delete'){
    if(isset($_GET['news'])){
        $news = $_GET['news'];
    }
    $statment1 = $connect->prepare('DELETE FROM news_category WHERE news_id=?');
    $statment2 = $connect->prepare('DELETE FROM news WHERE news_id=?');
    $statment1->execute(array($news));
    $statment2->execute(array($news));
    $_SESSION['message']="Deleted Successfully";
    header('Location:news.php');
}elseif($page == 'create'){
        $categ = $connect->query("SELECT * FROM categories")->fetchAll();
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 m-auto">
            <h3 class="text-center">Add new news</h3>
            <form action="?page=savecreate" method="post">
                <label>ID</label>
                <input type="text" name="id" class="form-control mb-3"
                placeholder="<?php
                if(isset($_SESSION['error_id'])){
                     echo $_SESSION['error_id'];
                     unset($_SESSION['error_id']);
                }?>">
                <label>Title</label>
                <input type="text" name="title" class="form-control mb-3" 
                value="<?php
                if(isset($_SESSION['error_name'])){
                     echo $_SESSION['error_name'];
                     unset($_SESSION['error_name']);
                }
                ?>">
                <label>Content</label>
                <input type="text" name="content" class="form-control mb-3" 
                value="<?php
                if(isset($_SESSION['error_name'])){
                     echo $_SESSION['error_name'];
                     unset($_SESSION['error_name']);
                }
                ?>">
                <label>Categories</label><br>
                <?php foreach ($categ as $category){
                ?>
                <label>
                    <input type="checkbox" name="categories[]" value="<?php $category['categories_id'] ?>"> <?php $category['name'] ?>
                </label><br>
                <?php
                }?>
                <input type="submit" name="submit" value="Insert" class="btn btn-success form-control mb-3">
            </form>
            </div>
        </div>
    </div>
    
<?php
}elseif($page== 'savecreate'){
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $id = $_POST['id'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        $categories = $_POST['categories'] ?? [];
        try{
        $statment= $connect->prepare('INSERT INTO
        news(id,title, content,created_at)
        VALUES(?,?,?,now())
        ');
        $statment->execute(array($id, $title, $content));
        $news_id = $connect->lastInsertId();
        foreach ($categories as $cat_id) {
            $connect->prepare("INSERT INTO news_category(news_id, category_id) VALUES (?, ?)")->execute(array($news_id, $cat_id));
        }
        $_SESSION['message']='Created Successfully';

        header('Location:news.php');
        }catch(PDOException $e){
            $_SESSION['error_id']='Enter another id';
            echo"<h4 class='alert alert-danger text-center'>Duplicated ID</h4>";
            header("Refresh:3;url=news.php?page=create");

        }
    }
}elseif($page== "edit"){
    if(isset($_GET['news'])){
        $news = $_GET['news'];
    }
    $statment=$connect->prepare('SELECT * FROM news WHERE news_id=?');
    $statment->execute(array($news));
    $result=$statment->fetch();

    $cats = $connect->query("SELECT * FROM categories")->fetchAll();
    $stmt = $connect->prepare("SELECT category_id FROM news_category WHERE news_id = ?");
    $stmt->execute(array($id));
    $selected = $stmt->fetchAll();
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 m-auto">
            <h3 class="text-center">Edit user</h3>
            <form action="?page=saveedit&old_id=<?php echo $result['news_id'];?>" method="post">
                <label>ID</label>
                <input type="text" name="new_id" class="form-control mb-3" value="<?php echo $result['news_id'];?>">
                <label>Title</label>
                <input type="text" name="title" class="form-control mb-3" value="<?php echo $data['title']?>">
                <label>Content</label>
                <textarea name="content" class="form-control mb-3" value="<?php echo $data['content'];?>"></textarea>
                <label>Categories</label><br>
                <?php foreach ($cats as $cat){ ?>
                <label>
                <input type="checkbox" name="categories[]" value="<?php $cat['categories_id'] ?>"
                <?php in_array($cat['categories_id'], $selected) ? 'checked' : '' ?>>
                <?php $cat['name'] ?>
                </label><br>
                <?php
                } ?>
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
            $title = $_POST['title'];
            $content = $_POST['content'];
            $categories = $_POST['categories'] ?? [];
            try{
                $statment=$connect->prepare("UPDATE news SET
                categories_id=?,
                title=?,
                content=?
                WHERE news_id=?
                ");
                $statment->execute(array($new_id, $title, $content, $old_id));
                 $connect->prepare("DELETE FROM news_category WHERE news_id=?")->execute(array($id));
                foreach ($categories as $cat_id) {
                    $connect->prepare("INSERT INTO news_category(news_id, category_id) VALUES (?, ?)")->execute(array($id, $cat_id));
                }
                $_SESSION['message']='Edit succesfully';
                header('Location:news.php');
            }catch(PDOException $e){
                echo "<h4 class='alert alert-danger text-center'>Dublicated ID</h4>";
                header("Refresh:3;url=news.php?page=edit&news=$old_id");
            }

        }
}
?>
<?php
include("includes/temp/footer.php");
?>