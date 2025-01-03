<?php
// Include database connection
include('config.php'); 

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch the current values of the row
    $sql = "SELECT * FROM transcript WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        die("Record not found.");
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the updated values from the form
        $year = $_POST['year'];
        $semester = $_POST['semester'];
        $qp = $_POST['qp'];
        $type = $_POST['type'];
        $class = $_POST['class'];
        $grade = $_POST['grade'];
        $credit = $_POST['credit'];

        // Check if any of the fields were actually changed
        if ($year != $row['year'] || $semester != $row['semester'] || $qp != $row['qp'] || $type != $row['type'] || $class != $row['class'] || $grade != $row['grade'] || $credit != $row['credit']) {
            // Update the record in the database
            $update_sql = "UPDATE transcript SET year = ?, semester = ?, qp = ?, type = ?, class = ?, grade = ?, credit = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssssssdi", $year, $semester, $qp, $type, $class, $grade, $credit, $id);
            $update_stmt->execute();
            
            if ($update_stmt->affected_rows > 0) {
                echo "Record updated successfully.";
            } else {
                echo "Error updating record.";
            }
        } else {
            // No changes made, just redirect
            header("Location: index.php");
            exit();  // Stop further execution
        }

        // Redirect after update or if no changes
        header("Location: index.php");
        exit();
    }
} else {
    die("No ID provided.");
}
?>

<!DOCTYPE html>
<html data-theme="dim">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Record</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5.0.0-beta.1/daisyui.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5.0.0-beta.1/themes.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="max-w-7xl mx-auto p-4">
            <h1 class="text-6xl my-5 font-bold text-left leading-normal">
                Edit
            </h1>
        <form method="POST">
            <fieldset class="fieldset">
                <legend class="fieldset-legend">Year:</legend>
                <input type="text" name="year" value="<?php echo $row['year']; ?>" required class="input">
                <p class="fieldset-label">Enter the academic year in the format Y1Y2</p>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Semester:</legend>
                <input type="text" name="semester" value="<?php echo $row['semester']; ?>" required class="input">
                <p class="fieldset-label">Enter the semester/quarter (e.g., 1, 2, 3) </p>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Quality Points:</legend>
                <input type="text" name="qp" value="<?php echo $row['qp']; ?>" required class="input">
                <p class="fieldset-label">Enter the quality points on top of GPA calculation for the course</p>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Type:</legend>
                <input type="text" name="type" value="<?php echo $row['type']; ?>" required class="input">
                <p class="fieldset-label">Enter the course type (e.g., OL for On Level, AP for Advanced Placement, H for Honors)</p>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Class:</legend>
                <input type="text" name="class" value="<?php echo $row['class']; ?>" required class="input">
                <p class="fieldset-label">Enter the course name without Honors or AP Designation</p>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Grade:</legend>
                <input type="text" name="grade" value="<?php echo $row['grade']; ?>" required class="input">
                <p class="fieldset-label">Enter the final grade / Put -1 for no calculation</p>
            </fieldset>

            <fieldset class="fieldset mb-10">
                <legend class="fieldset-legend">Credit:</legend>
                <input type="text" name="credit" value="<?php echo $row['credit']; ?>" required class="input">
                <p class="fieldset-label">Enter the number of credits earned for that semester of that course</p>
            </fieldset>

            <button type="submit" class="btn btn-outline btn-primary">Update Record</button>
        </form>
    </div>
</body>
</html>