<?php

$staffMembers = $staffMembers ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Members</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Staff Members</h1>

            <a href="<?= url('admin/staff/create') ?>" class="btn btn-primary">
                Add Staff
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody>
                        <?php foreach ($staffMembers as $staff): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($staff['first_name']) ?>
                                    <?= htmlspecialchars($staff['last_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($staff['email']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($staff['department']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($staff['position']) ?>
                                </td>
                                <td>
                                    <a
                                        href="<?= url('admin/staff/edit/' . (int) $staff['id']) ?>"
                                        class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <a
                                        href="<?= url('admin/staff/delete/' . (int) $staff['id']) ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this staff member?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>

        <a href="<?= url('admin/dashboard') ?>" class="btn btn-outline-secondary mt-3">
            Back to Dashboard
        </a>

    </div>

</body>

</html>
