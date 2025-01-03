<?php
    include('config.php');
?>

<!DOCTYPE html>
<html data-theme="dim">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link href="https://cdn.jsdelivr.net/npm/daisyui@5.0.0-beta.1/daisyui.css" rel="stylesheet" type="text/css" />
        <link href="https://cdn.jsdelivr.net/npm/daisyui@5.0.0-beta.1/themes.css" rel="stylesheet" type="text/css" />
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="max-w-7xl mx-auto p-4">
            <div class="navbar bg-base-200 mt-5 shadow-md rounded-lg">
                <div class="flex-1">
                    <a class="btn btn-ghost btn-disabled text-neutral-content text-6xl h-24">MasterGrade</a>
                </div>
                <div class="flex-none">
                    <ul class="menu menu-horizontal px-1">
                    <?php
                        // Include database connection
                        include('config.php'); 

                        // Initialize variables to hold the total weighted GPA, total unweighted GPA, and total credits
                        $total_weighted_gpa = 0;
                        $total_unweighted_gpa = 0;
                        $total_credits = 0;

                        // SQL query to get grade, credit, and quality points for each row
                        $sql = "SELECT grade, credit, qp FROM transcript";
                        $result = $conn->query($sql);

                        // Function to convert numerical grade to GPA
                        function convert_grade_to_gpa($grade) {
                            if ($grade > 89) {
                                return 4; // A
                            } elseif ($grade >= 80 && $grade <= 89) {
                                return 3; // B
                            } elseif ($grade >= 75 && $grade <= 79) {
                                return 2; // C
                            } elseif ($grade >= 70 && $grade <= 74) {
                                return 1; // D
                            } else {
                                return 0; // F
                            }
                        }

                        // Check if query results exist
                        if ($result && $result->num_rows > 0) {
                            // Loop through each row in the transcript table
                            while ($row = $result->fetch_assoc()) {
                                $grade = $row['grade'];
                                $credit = $row['credit'];
                                $qp = $row['qp'];

                                if ($grade == -1) {
                                    continue;
                                }

                                // Convert the grade to GPA
                                $gpa = convert_grade_to_gpa($grade);

                                // Weighted GPA (add quality points to the GPA)
                                $weighted_gpa = $gpa + $qp;

                                // Calculate weighted GPA for this row: (GPA + qp) * credit
                                $weighted_gpa_per_row = $weighted_gpa * $credit;

                                // Calculate unweighted GPA for this row: GPA * credit (without quality points)
                                $unweighted_gpa_per_row = $gpa * $credit;

                                // Add weighted and unweighted GPA for this row to the total GPA
                                $total_weighted_gpa += $weighted_gpa_per_row;
                                $total_unweighted_gpa += $unweighted_gpa_per_row;

                                // Add the credits of this row to the total credits
                                $total_credits += $credit;
                            }

                            // Calculate final GPAs by dividing by total credits
                            $final_weighted_gpa = $total_weighted_gpa / $total_credits;
                            $final_unweighted_gpa = $total_unweighted_gpa / $total_credits;

                            // Output the results
                            echo "<li><a class='btn btn-ghost btn-disabled text-neutral-content text-3xl h-24'>Weighted: " . round($final_weighted_gpa, 3) . "</a></li>";
                            echo "<li><a class='btn btn-ghost btn-disabled text-neutral-content text-3xl h-24'>Unweighted: " . round($final_unweighted_gpa, 3) . "</a></li>";

                        } else {
                            echo "<li><a class='btn btn-ghost btn-disabled text-danger text-3xl h-24'>N/A</a></li>";
                        }
                    ?>
                    </ul>
                </div>
            </div>
            <a class="btn btn-ghost h-8 my-2" onclick="reload()">Reset Filters</a>
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="rounded-lg">
                    <tr class="bg-base-200 text-neutral-content text-l">
                        <th><button onclick="sortTable(0)">Year</button></th>
                        <th><button onclick="sortTable(1)">Semester</button></th>
                        <th><button onclick="sortTable(2)">Quality Points</button></th>
                        <th><button onclick="sortTable(3)">Type</button></th>
                        <th><button onclick="sortTable(4)">Class</button></th>
                        <th><button onclick="sortTable(5, true)">Grade</button></th>
                        <th><button onclick="sortTable(6, true)">Credit</button></th>
                        <th>Action</th> <!-- Added a new column for the Edit button -->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        // SQL query to fetch data
                        $sql = "SELECT * FROM transcript";
                        $result = $conn->query($sql);

                        if (!$result) {
                            die("Query failed: " . $conn->error);
                        }

                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>20" . substr($row["year"], 0, 2) . " - 20" . substr($row["year"], 2, 2) . "</td>";
                                echo "<td>" . $row["semester"] . "</td>";
                                echo "<td>" . $row["qp"] . "</td>";
                                echo "<td class='text-xl'>" . $row["type"] . "</td>";
                                echo "<td class='text-xl'>" . $row["class"] . "</td>";

                                // Use convert_grade_to_gpa to determine the GPA from grade
                                $gpa = convert_grade_to_gpa($row["grade"]);

                                // Assign a CSS class based on GPA value
                                if ($gpa == 4) {
                                    $class = "text-green-400"; // A
                                } elseif ($gpa == 3) {
                                    $class = "text-yellow-300"; // B
                                } elseif ($gpa == 2) {
                                    $class = "text-amber-500"; // C
                                } elseif ($gpa == 1) {
                                    $class = "text-red-500"; // D
                                } elseif ($gpa == 0) {
                                    $class = "text-purple-500"; // F
                                } else {
                                    $class = "text-gray-400";
                                }

                                // Display the grade with the assigned CSS class
                                if ($row["grade"] >= 0) {
                                    echo "<td class='font-bold text-xl $class'>" . $row["grade"] . "</td>";
                                } else {
                                    echo "<td class='font-bold text-xl'>N/A</td>";
                                }
                                
                                echo "<td>" . $row["credit"] . "</td>";
                                echo "<td><a href='edit.php?id=" . $row["id"] . "' class='btn btn-secondary btn-outline'>Edit</a></td>";  // Added Edit button
                                echo "</tr>";
                            }
                        } else {
                            echo "0 results";
                        }
                        $conn->close();
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            function sortTable(columnIndex, isNumeric = false)
            {
                var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
                table = document.querySelector("table");  // Select the table
                switching = true;  // Set switching to true to start the sorting loop
                dir = "asc";  // Set the default sorting direction to ascending

                // Run a loop to keep switching until no switching is done
                while (switching) {
                    switching = false;
                    rows = table.rows;
                    
                    // Loop through all table rows (skip the first row, which is the header)
                    for (i = 1; i < (rows.length - 1); i++) {
                        shouldSwitch = false;
                        // Get the two elements to compare, current and next row
                        x = rows[i].getElementsByTagName("TD")[columnIndex];
                        y = rows[i + 1].getElementsByTagName("TD")[columnIndex];
                        
                        // Convert to numbers if numeric sorting is enabled for the column
                        let xContent = isNumeric ? parseFloat(x.innerHTML) || 0 : x.innerHTML.toLowerCase();
                        let yContent = isNumeric ? parseFloat(y.innerHTML) || 0 : y.innerHTML.toLowerCase();
                        
                        // Check if the two rows should switch place, based on the sorting direction (asc or desc)
                        if (dir == "asc") {
                            if (xContent > yContent) {
                                shouldSwitch = true;
                                break;
                            }
                        } else if (dir == "desc") {
                            if (xContent < yContent) {
                                shouldSwitch = true;
                                break;
                            }
                        }
                    }
                    if (shouldSwitch) {
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                        switchcount++;
                    } else {
                        if (switchcount == 0 && dir == "asc") {
                            dir = "desc";
                            switching = true;
                        }
                    }
                }
            }

            function reload()
            {
                location.reload();
            }
        </script>
    </body>
</html>
