<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Staff</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow-sm">
        <div class="card-body">

            <h1 class="h3 mb-4">Add Staff Member</h1>

            <form method="POST" action="<?= url('admin/staff/create') ?>">

                <div class="mb-3">
                    <label class="form-label">First Name</label>

                    <input
                        type="text"
                        name="first_name"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name</label>

                    <input
                        type="text"
                        name="last_name"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Department</label>

                    <input
                        type="text"
                        name="department"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Position</label>

                    <input
                        type="text"
                        name="position"
                        class="form-control"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Save
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
