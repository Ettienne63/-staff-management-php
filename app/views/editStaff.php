<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Staff</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow-sm">
        <div class="card-body">

            <h1 class="h3 mb-4">Edit Staff Member</h1>

            <form method="POST" action="<?= url('admin/staff/edit/' . (int) ($staffMember['id'] ?? 0)) ?>">
                <input type="hidden" name="id" value="<?= (int) ($staffMember['id'] ?? 0) ?>">

                <div class="mb-3">
                    <label class="form-label">First Name</label>

                    <input
                        type="text"
                        name="first_name"
                        class="form-control"
                        value="<?= htmlspecialchars($staffMember['first_name'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name</label>

                    <input
                        type="text"
                        name="last_name"
                        class="form-control"
                        value="<?= htmlspecialchars($staffMember['last_name'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($staffMember['email'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Department</label>

                    <input
                        type="text"
                        name="department"
                        class="form-control"
                        value="<?= htmlspecialchars($staffMember['department'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Position</label>

                    <input
                        type="text"
                        name="position"
                        class="form-control"
                        value="<?= htmlspecialchars($staffMember['position'] ?? '') ?>"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Update
                </button>

                <a
                    href="<?= url('admin/staff') ?>"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>
