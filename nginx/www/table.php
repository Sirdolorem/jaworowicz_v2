<!DOCTYPE html>
<html>
<head>
  <title>Entries Management</title>
  <style>
    .expired {
      background-color: #FFCDD2;
    }
    .approaching-expiration {
      background-color: #FFF9C4;
    }
    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 400px;
      padding: 20px;
      background-color: #FFFFFF;
      border: 1px solid #CCCCCC;
      border-radius: 5px;
      z-index: 999;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #CCCCCC;
    }
    th {
      background-color: #F5F5F5;
      font-weight: bold;
    }
    button {
      margin-right: 5px;
    }
  </style>
</head>
<body>
  <h1>Entries Management</h1>

  <button onclick="togglePopup('addPopup')">Add New Entry</button>

  <div class="overlay"></div>

  <div id="addPopup" class="popup">
    <h2>Add New Entry</h2>
    <form onsubmit="addNewEntry(event)">
      <label for="name">Name:</label>
      <input type="text" id="name" required><br>

      <label for="dateExpire">Date Expire:</label>
      <input type="date" id="dateExpire" required><br>

      <label for="toBuy">To Buy:</label>
      <input type="text" id="toBuy" required><br>

      <label for="amount">Amount:</label>
      <input type="number" id="amount" required><br>

      <button type="submit">Add</button>
      <button type="button" onclick="togglePopup('addPopup')">Cancel</button>
    </form>
  </div>

  <div id="editPopup" class="popup">
    <h2>Edit Entry</h2>
    <form onsubmit="editEntry(event)">
      <input type="hidden" id="entryId">

      <label for="editName">Name:</label>
      <input type="text" id="editName" required><br>

      <label for="editDateExpire">Date Expire:</label>
      <input type="date" id="editDateExpire" required><br>

      <label for="editToBuy">To Buy:</label>
      <input type="text" id="editToBuy" required><br>

      <label for="editAmount">Amount:</label>
      <input type="number" id="editAmount" required><br>

      <button type="submit">Save</button>
      <button type="button" onclick="togglePopup('editPopup')">Cancel</button>
    </form>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Date Expire</th>
        <th>To Buy</th>
        <th>Amount</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="entriesTableBody">
    </tbody>
  </table>

  <script>
    function togglePopup(popupId) {
      const popup = document.getElementById(popupId);
      const overlay = document.querySelector('.overlay');
      if (popup.style.display === 'none') {
        popup.style.display = 'block';
        overlay.style.display = 'block';
      } else {
        popup.style.display = 'none';
        overlay.style.display = 'none';
      }
    }

    function getToken() {
      // Replace with your authentication logic to get the token
      const token = 'your_token_here';
      return token;
    }

    function fetchEntries() {
      const token = getToken();
      const url = 'http://localhost:8080/api/entries/all';

      fetch(url, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
      .then(response => {
        console.log(response); // Log the response object
        return response.json();
      })
      .then(data => {
        console.log(data); // Log the response data
        if (data.entries) {
          const entriesTableBody = document.getElementById('entriesTableBody');
          entriesTableBody.innerHTML = '';

          data.entries.forEach(entry => {
            const { id, name, date_expire, to_buy, amount } = entry;

            const row = document.createElement('tr');
            row.innerHTML = `
              <td>${id}</td>
              <td>${name}</td>
              <td>${date_expire}</td>
              <td>${to_buy}</td>
              <td>${amount}</td>
              <td>
                <button onclick="populateEditForm(${id})">Edit</button>
                <button onclick="deleteEntry(${id})">Delete</button>
              </td>
            `;

            // Apply CSS class based on expiration status
            const currentDate = new Date();
            const expirationDate = new Date(date_expire);
            if (expirationDate < currentDate) {
              row.classList.add('expired');
            } else if (expirationDate.getTime() - currentDate.getTime() < 7 * 24 * 60 * 60 * 1000) {
              row.classList.add('approaching-expiration');
            }

            entriesTableBody.appendChild(row);
          });
        }
      })
      .catch(error => {
        console.error('Error fetching entries:', error);
      });
    }

    function addNewEntry(event) {
      event.preventDefault();

      const token = getToken();
      const url = 'http://localhost:8080/api/entries/create';
      const name = document.getElementById('name').value;
      const dateExpire = document.getElementById('dateExpire').value;
      const toBuy = document.getElementById('toBuy').value;
      const amount = document.getElementById('amount').value;

      const requestBody = JSON.stringify({
        name: name,
        date_expire: dateExpire,
        to_buy: toBuy,
        amount: amount
      });

      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: requestBody
      })
      .then(response => {
        console.log(response); // Log the response object
        return response.json();
      })
      .then(data => {
        console.log(data); // Log the response data
        if (data.success) {
          togglePopup('addPopup');
          fetchEntries();
        } else {
          console.error('Error creating new entry:', data.error);
        }
      })
      .catch(error => {
        console.error('Error creating new entry:', error);
      });
    }

    function populateEditForm(entryId) {
      const token = getToken();
      const url = `http://localhost:8080/api/entries/${entryId}`;

      fetch(url, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
      .then(response => {
        console.log(response); // Log the response object
        return response.json();
      })
      .then(data => {
        console.log(data); // Log the response data
        if (data.entry) {
          const entry = data.entry;
          const editNameInput = document.getElementById('editName');
          const editDateExpireInput = document.getElementById('editDateExpire');
          const editToBuyInput = document.getElementById('editToBuy');
          const editAmountInput = document.getElementById('editAmount');
          const entryIdInput = document.getElementById('entryId');

          editNameInput.value = entry.name;
          editDateExpireInput.value = entry.date_expire;
          editToBuyInput.value = entry.to_buy;
          editAmountInput.value = entry.amount;
          entryIdInput.value = entry.id;

          togglePopup('editPopup');
        } else {
          console.error('Error fetching entry:', data.error);
        }
      })
      .catch(error => {
        console.error('Error fetching entry:', error);
      });
    }

    function editEntry(event) {
      event.preventDefault();

      const token = getToken();
      const entryId = document.getElementById('entryId').value;
      const url = `http://localhost:8080/api/entries/${entryId}`;
      const name = document.getElementById('editName').value;
      const dateExpire = document.getElementById('editDateExpire').value;
      const toBuy = document.getElementById('editToBuy').value;
      const amount = document.getElementById('editAmount').value;

      const requestBody = JSON.stringify({
        name: name,
        date_expire: dateExpire,
        to_buy: toBuy,
        amount: amount
      });

      fetch(url, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: requestBody
      })
      .then(response => {
        console.log(response); // Log the response object
        return response.json();
      })
      .then(data => {
        console.log(data); // Log the response data
        if (data.success) {
          togglePopup('editPopup');
          fetchEntries();
        } else {
          console.error('Error updating entry:', data.error);
        }
      })
      .catch(error => {
        console.error('Error updating entry:', error);
      });
    }

    function deleteEntry(entryId) {
      const token = getToken();
      const url = `http://localhost:8080/api/entries/${entryId}`;

      fetch(url, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
      .then(response => {
        console.log(response); // Log the response object
        return response.json();
      })
      .then(data => {
        console.log(data); // Log the response data
        if (data.success) {
          fetchEntries();
        } else {
          console.error('Error deleting entry:', data.error);
        }
      })
      .catch(error => {
        console.error('Error deleting entry:', error);
      });
    }

    // Initial fetch of entries
    fetchEntries();
  </script>
</body>
</html>
