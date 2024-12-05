<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoSimPlat - Visualization</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Center the body content vertically and horizontally */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Add a container to hold the content */
        .container {
            width: 80%; /* Or any desired width */
            max-width: 1200px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .left-column {
            width: 70%;
        }

        .right-column {
            width: 25%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        .tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: auto;
            background-color: #555;
            color: #fff;
            text-align: center;
            padding: 5px;
            border-radius: 6px;
            position: absolute;
            z-index: 1;
            bottom: 100%; /* Position tooltip above text */
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        .progress {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 15px; /* Added margin for spacing between progress bars */
        }

        .progress-bar {
            height: 20px;
            width: 0%;
            background-color: blue;
            transition: width 0.4s;
        }

        .table-container {
            width: 100%; /* Adjust width as needed */
            height: 300px; /* Set the height for the scrollable area */
            overflow: auto; /* Enables scrolling */
            border: 1px solid #ddd; /* Optional: add border around the scrollable area */
        }

        th, td {
            border: 1px solid #ddd; /* Optional: adds borders to table cells */
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-column">
            <div class="logo">
                <img src="esciencelogo.png" alt="Website Logo" style="width: 250px; height: auto;">
            </div>

            <hr>
            <div class="d-flex justify-content-center gap-3 mb-4">
                
                <button class="btn btn-danger" onclick="reset()">Reset MySQL</button>
            </div>
            <hr>

            <!-- Move progress bars to the top -->
            <div class="progress">
                <div class="progress-bar" id="progress-bar-1" style="width: 0%;"></div>
            </div>
            <div class="progress">
                <div class="progress-bar" id="progress-bar-2" style="width: 0%;"></div>
            </div>
            <div class="progress">
                <div class="progress-bar" id="progress-bar-3" style="width: 0%;"></div>
            </div>

            <hr>
            <div class="table-container" id="tableContainer">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th>Submodel ID</th>
                            <th>Sim Step</th>
                            <th>Payload (shortened)</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table rows go here -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="right-column">
            <!-- You can add content here if needed -->
        </div>
    </div>
</body>

<!-- Add Bootstrap 5 JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</html>



<script>
var lastTimestamp = '1970-01-01 00:00:00'; // Initial timestamp

// Initialize separate simulation step counters for Player 2 and Player 3
var player2Step = 0;
var player3Step = 0;

// Initialize a max simulation steps constant
const MAX_SIM_STEP = 29;

// Fetch simulation data function
function fetchSimData() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "objects/fetcher.php?lastTimestamp=" + encodeURIComponent(lastTimestamp), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var payload = JSON.parse(xhr.responseText);
                if (payload.length > 0) {
                    payload.forEach(function (pack) {
                        renderpack(pack);
                        lastTimestamp = pack.timestamp;
                    });
                }
                setTimeout(fetchSimData, 500); // Wait for 1 second before polling again
            } catch (e) {
                console.error("Failed to parse JSON response: ", e);
                setTimeout(fetchSimData, 500); // Retry after 1 second on error
            }
        } else if (xhr.readyState === 4) {
            console.error("Failed to fetch payload. Status: ", xhr.status);
            setTimeout(fetchSimData, 500); // Retry after 1 second on failure
        }
    };
    xhr.send();
}

// Run fetch simulation data on load
window.onload = function() {
    fetchSimData();
};

// Render a new packet in the table
function renderpack(pack) {
    var table = document.getElementById('dataTable');
    var row = table.insertRow();

    // Submodel ID
    var cell1 = row.insertCell(0);
    cell1.textContent = pack.submodel_id;

    // Sim Step
    var cell2 = row.insertCell(1);
    cell2.textContent = pack.sim_step;

    // Payload (shortened) with tooltip
    var cell3 = row.insertCell(2);
    var shortPayload = JSON.stringify(pack.payload).substring(0, 50) + '...';
    var fullPayload = JSON.stringify(pack.payload);
    cell3.innerHTML = `<span class="tooltip">${shortPayload}<span class="tooltiptext">${fullPayload}</span></span>`;

    // Timestamp
    var cell4 = row.insertCell(3);
    cell4.textContent = pack.timestamp;

    // Update progress bars after adding new row
    updateProgressBars(pack.submodel_id, pack.sim_step);

    // Scroll to the bottom of the table container after adding the new row
    const tableContainer = document.getElementById("tableContainer");
            tableContainer.scrollTop = tableContainer.scrollHeight;
}


// Reset function to clear the table
function reset() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "objects/reset.php", true); 
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log("Table truncated successfully!");
            document.getElementById('dataTable').innerHTML = `
            <tr>
                <th>Submodel ID</th>
                <th>Sim Step</th>
                <th>Payload (shortened)</th>
                <th>Timestamp</th>
            </tr>`;
            // Reset the step counters
            player2Step = 0;
            player3Step = 0;
            // Reset progress bars
            resetProgressBars();
        }
    };
    xhr.send(); // No payload needed for truncating the table
}

// Update progress bars based on new data
function updateProgressBars(submodel_id, sim_step) {
    let progress = (sim_step / MAX_SIM_STEP) * 100; // Calculate progress percentage

    if (submodel_id === 1) {
        document.getElementById('progress-bar-1').style.width = progress + '%';
    } else if (submodel_id === 2) {
        document.getElementById('progress-bar-2').style.width = progress + '%';
    } else if (submodel_id === 3) {
        document.getElementById('progress-bar-3').style.width = progress + '%';
    }
}

// Reset all progress bars
function resetProgressBars() {
    document.getElementById('progress-bar-1').style.width = '0%';
    document.getElementById('progress-bar-2').style.width = '0%';
    document.getElementById('progress-bar-3').style.width = '0%';
}
</script>
