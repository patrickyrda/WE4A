<?php   
//transformer tout dans un objet 
$limit = 5; //Number per page
$page = 0;

if (isset($_POST["page"]) ) {
    $page = $_POST["page"];
} else {
    $page = 1;
}
//fix this shit
if ($page == 0) {
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

$result = $conn->query("SELECT * FROM users ORDER BY id LIMIT $start_from, $limit");

$ues_count = $conn->query("SELECT COUNT(id) FROM users")->fetch_row()[0];

$display = "";

$display .= "
        <table class=\"table table-striped table-bordered table-hover\">
            <thead>
                <tr>
                    <th>id</th>
                    <th>name</th>
                    <th>surname</th>
                    <th>email</th>
                    
                </tr>
            </thead>
            <tbody>
";

if ($ues_count > 0) {
    while ($row = $result->fetch_assoc()) {
        $display .= "
                <tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["name"] . "</td>
                    <td>" . $row["surname"] . "</td>
                    <td>" . $row["email"] . "</td>
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
        $previous = $page - 1;
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