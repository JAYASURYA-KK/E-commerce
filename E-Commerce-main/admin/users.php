<?php 
include_once('./includes/headerNav.php');
include_once('./includes/restriction.php');

// Redirect if not logged in
if (!(isset($_SESSION['logged-in']))) {
    header("Location: login.php?unauthorizedAccess");
    exit();
}

include "config.php";

// Define pagination settings
$limit = 4; // Items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$sn = ($page - 1) * $limit;
$offset = $sn;

// Fetch users from database
$sql = "SELECT * FROM users LIMIT {$offset}, {$limit}";
$result = $conn->query($sql);

if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>

<h1>Users</h1>
<hr>

<?php if ($result->num_rows > 0): ?>
    <div class="table-cont">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Username</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Address</th>
                    <th scope="col">Edit</th>      
                    <th scope="col">Delete</th>      
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <th scope="row"><?php echo ++$sn; ?></th>
                    <td><?php echo $row["username"]; ?></td>
                    <td><?php echo $row["f_name"] . ' ' . $row["l_name"]; ?></td>
                    <td><?php echo $row["email"]; ?></td>
                    <td><?php echo $row["phone"]; ?></td>
                    <td><?php echo $row["address"]; ?></td>
                    <td>
                        <a class="fn_link" href="update-user.php?id=<?php echo $row["u_id"]; ?>">
                            <i class='fa fa-edit'></i>
                        </a>
                    </td>          
                    <td>
                        <a class="fn_link" href="remove-user.php?id=<?php echo $row["u_id"]; ?>">
                            <i class='fa fa-trash'></i>
                        </a>
                    </td>         
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>No user records found.</p>
<?php endif; ?>

<!-- Pagination -->
<?php
// Pagination logic
$sql1 = "SELECT COUNT(*) as total FROM users";
$result1 = mysqli_query($conn, $sql1);

if (!$result1) {
    die("Pagination Query Failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result1);
$total_products = $row['total'];
$total_page = ceil($total_products / $limit);
?>

<nav aria-label="..." style="margin-left: 10px;">
    <ul class="pagination pagination-sm">
        <?php for ($i = 1; $i <= $total_page; $i++): ?>
            <li class="page-item">
                <a class="page-link <?php echo ($page == $i) ? 'active' : ''; ?>" href="users.php?page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
