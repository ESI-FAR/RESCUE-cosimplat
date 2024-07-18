<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Long Polling Example</title>
</head>
<body>
    <table id="dataTable"></table>

    <button onclick="insertRow()">Insert Row</button>


</body>
</html>

<script>
var lastTimestamp = '1970-01-01 00:00:00'; // Initial timestamp

function fetchSimData() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "objects/fetcher.php?lastTimestamp=" + encodeURIComponent(lastTimestamp), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var payload = JSON.parse(xhr.responseText);
                    if (payload.length > 0) {
                        payload.forEach(function (pack) {
                            renderpack(pack);
                            lastTimestamp = pack.timestamp;
                        });
                    }
                    // Re-initiate the long-polling immediately regardless of payload
                    setTimeout(fetchSimData, 500); // Wait for 1 second before polling again
                } catch (e) {
                    console.error("Failed to parse JSON response: ", e);
                    console.log("Response text: ", xhr.responseText);
                    setTimeout(fetchSimData, 500); // Retry after 1 second on error
                }
            } else {
                console.error("Failed to fetch payload. Status: ", xhr.status);
                console.log("Response text: ", xhr.responseText);
                setTimeout(fetchSimData, 500); // Retry after 1 second on failure
            }
        }
    };
    xhr.send();
}

// Initiate the long-polling when the page loads
window.onload = function() {
    fetchSimData();
};

function renderpack(pack) {
   // Assuming you have a <table> element with id="dataTable" where you want to append rows
   var table = document.getElementById('dataTable');

// Create a new row (<tr>) for each pack
var row = table.insertRow();

// Insert cells (<td>) into the row for each property in the pack object
var cell1 = row.insertCell(0);
var cell2 = row.insertCell(1);
var cell3 = row.insertCell(2);
var cell4 = row.insertCell(3);
var cell5 = row.insertCell(4);

// Assign values to each cell based on pack properties
cell1.textContent = pack.id;
cell2.textContent = pack.simgame_id;
cell3.textContent = pack.submodel_id;
cell4.textContent = pack.payload;
cell5.textContent = pack.timestamp;
}






// Emulator:

function insertRow() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "objects/insert.php", true); // Use POST method to send data to insert.php
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                // Handle successful response (if needed)
                console.log("Row inserted successfully!");
            } else {
                // Handle error response
                console.error("Failed to insert row. Status:", xhr.status);
                console.log("Response text:", xhr.responseText);
            }
        }
    };
    // Example payload (adjust as per your table structure)
    var payload = {
        simgame_id: 1,
        submodel_id: 123,
        payload: Math.floor(Math.random() * 1000),
        state_history: "Initial state",
        modified: new Date().toISOString() // Example timestamp
    };
    xhr.send(JSON.stringify(payload)); // Send JSON payload
}

</script>