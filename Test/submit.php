<!DOCTYPE html>
<html>

<head>
    <title>Hospital Dashboard</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
        }

        input,
        select {
            margin: 5px;
            padding: 5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #eee;
        }

        button {
            padding: 6px 12px;
        }
    </style>
</head>

<body>

    <h2>🏥 Patient Management</h2>

    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id'] ?? '') ?>">

        <input type="text" name="code" placeholder="Patient Code" required
            value="<?= htmlspecialchars($editData['patient_code'] ?? '') ?>">

        <input type="text" name="name" placeholder="Full Name" required
            value="<?= htmlspecialchars($editData['full_name'] ?? '') ?>">

        <input type="date" name="dob"
            value="<?= htmlspecialchars($editData['date_of_birth'] ?? '') ?>">

        <select name="gender">
            <option value="Male" <?= ($editData['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= ($editData['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= ($editData['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
        </select>

        <input type="text" name="phone" placeholder="Phone"
            value="<?= htmlspecialchars($editData['phone'] ?? '') ?>">

        <input type="text" name="address" placeholder="Address"
            value="<?= htmlspecialchars($editData['address'] ?? '') ?>">

        <button type="submit">Save</button>
    </form>

    <hr>

    <table>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Name</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Action</th>
        </tr>

        <?php foreach ($patients as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['id']) ?></td>
                <td><?= htmlspecialchars($p['patient_code']) ?></td>
                <td><?= htmlspecialchars($p['full_name']) ?></td>
                <td><?= htmlspecialchars($p['date_of_birth']) ?></td>
                <td><?= htmlspecialchars($p['gender']) ?></td>
                <td><?= htmlspecialchars($p['phone']) ?></td>
                <td><?= htmlspecialchars($p['address']) ?></td>
                <td>
                    <a href="?edit=<?= $p['id'] ?>">Edit</a> |
                    <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>

</body>

</html>