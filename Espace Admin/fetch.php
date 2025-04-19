<?php   

$limit = 5; //Number per page
$page = 0;

if (isset($_POST["page"])) {
    $page = $_POST["page"];
} else {
    $page = 1;
}

// Calculate the starting point for the query
$start_from = ($page - 1) * $limit;

//db connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "td1";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM ues ORDER BY ID LIMIT $start_from, $limit");

$ues_count = $conn->query("SELECT COUNT(ID) FROM ues")->fetch_row()[0];

$display = "";

$display .= "
        <table class=\"table table-striped table-bordered table-hover\">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
";

if ($ues_count > 0) {
    while ($row = $result->fetch_assoc()) {
        $display .= "
                <tr>
                    <td>" . $row["ID"] . "</td>
                    <td>" . $row["title"] . "</td>
                    <td>" . $row["code"] . "</td>
                    <td>
                        <a href=\"#\" class=\"delete-item\" data-id=\"" . $row["ID"] . "\">Delete</a>
                        <a href=\"#\" class=\"modify-item\" data-idmodify=\"" . $row["ID"] . "\" data-titlemodify=\"" . $row["title"] . "\" data-codemodify=\"" . $row["code"] . "\">Modifier</a>
                    </td>
                </tr>
            ";
    }
} else {
    $display .= "<tr><td colspan='3'>No records found</td></tr>";
}

$display .= "
            </tbody>
        </table>
";

$total_pages = ceil($ues_count/$limit);
$display .= "
        <nav aria-label=\"Page navigation example\">
            <ul class=\"pagination justify-content-center\">";
    if ($page > 1) {
        //$previous = $page - 1;
        $display .= "<li class=\"page-item\" id =\"1\"><span class =\"page-link\">First page</span></li>";
        //$display .= "<li class=\"page-item\" id =\"$previous\"><\li>"; //here test adding some valies to check
    }


    for ($i=1; $i <= $total_pages; $i++) {
        $active_class = "";
        if ($i == $page) {
            $active_class = "active";
        }
        $display .= "<li class=\"page-item $active_class\" id=\"$i\"><span class=\"page-link\">$i</span></li>";
    }

$display .= "
            </ul>
        </nav>
";

echo $display;

?>