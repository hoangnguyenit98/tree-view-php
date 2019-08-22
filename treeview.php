<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="fontawesome-free-5.10.1-web/css/all.css" rel="stylesheet">
    <title>Document</title>
    <style>
        ul {
            list-style-type: none;
        }

        ul li {
            padding-top: 20px;
            cursor: pointer;
        }

        .root {
            display: none;
        }
    </style>
</head>

<body>
    <form action="" method="POST" role="form">
        <h4>Thêm mới</h4>
        <?php
        $host = 'localhost';
        $db = 'demo';
        $username = 'root';
        $password = '';
        $dsn = "mysql:host=$host;dbname=$db";

        try {
            $conn = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $category_parent = isset($_POST['category_parent']) ? $_POST['category_parent'] : 0;
        $category_name = isset($_POST['category_name']) ? $_POST['category_name'] : "";
        if (isset($category_name) && !empty($category_name)) {
            $sql = "INSERT INTO categories(name, parent_id) VALUES('$category_name','$category_parent')";
            $conn->exec($sql) or die('Loi truy van');
        }

        function getSubCategories($conn, $parent_id)
        {
            $stmt = $conn->prepare("select * from categories where parent_id = $parent_id order by categories.name asc");
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        function generate($conn, $parent_id)
        {
            if (count(getSubCategories($conn, $parent_id)) > 0) {
                foreach (getSubCategories($conn, $parent_id) as $row) {
                    echo "<li>";
                    if (count(getSubCategories($conn, $row['id'])) > 0) {
                        echo "<span><i class='fas fa-caret-right'></i> " . $row['name'] . "<input type = 'radio' name ='category_parent' value='" . $row["id"] . "'/></span>";
                        echo "<ul class='root'>";
                        generate($conn, $row['id']);
                        echo "</ul>";
                    } else {
                        echo $row['name'] . "<input type = 'radio' name ='category_parent' value='" . $row["id"] . "'/>";
                    }
                    echo "</li>";
                }
            }
        }
        echo "<ul>";
        generate($conn, 0);
        echo "</ul>";
        ?>
        <table>
            <tr>
                <td>Category name: </td>
                <td><input type="text" name="category_name" /></td>
            </tr>
        </table>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>



</body>
<script>
    var el_li = document.querySelectorAll('li');
    el_li.forEach(function(item, index) {
        let el_span = item.childNodes[0];
        let el_span_i = el_span.childNodes[0];
        if (el_span_i) {
            el_span_i.onclick = function() {
                el_span_i.className = el_span_i.className.trim() == "fas fa-caret-right" ? "fas fa-caret-down" : "fas fa-caret-right";
                let el_ulroot = item.childNodes[1];
                el_ulroot.style.display = el_ulroot.style.display == "block" ? "none" : "block"
            }
        }
    });
    var category_parent = document.getElementsByName('category_parent');
    category_parent.forEach(function(item, index) {
        item.onclick = function() {
            if(this.checked == false){
                this.checked = true;
            }else{
                this.checked = false;
            }
        }
    });
</script>

</html>